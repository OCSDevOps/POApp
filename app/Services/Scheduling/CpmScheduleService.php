<?php

namespace App\Services\Scheduling;

use App\Models\Project;
use App\Models\ScheduleActivity;
use App\Models\ScheduleActivityActual;
use App\Models\ScheduleBaseline;
use App\Models\ScheduleBaselineActivity;
use App\Models\ScheduleCalendar;
use App\Models\ScheduleDependency;
use App\Models\ScheduleDriver;
use App\Models\ScheduleRun;
use App\Models\ScheduleRunActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CPM (Critical Path Method) Scheduling Engine
 *
 * Implements full forward/backward pass scheduling with calendar-aware
 * working time, constraint handling, driver integration, actuals lock-down,
 * lookahead/constraint-log reporting, baseline management, and health grading.
 */
class CpmScheduleService
{
    protected CalendarService $calendarService;

    /** @var array<int, ScheduleCalendar> Calendars loaded during a run, keyed by cal_id */
    protected array $calendars = [];

    // ---------------------------------------------------------------
    // Near-critical threshold: activities with total float at or below
    // one working day (in minutes) are flagged as "near-critical".
    // ---------------------------------------------------------------
    protected int $nearCriticalThresholdMinutes = 510; // default 8.5 hr day

    // ---------------------------------------------------------------
    // Default lookahead constraint buffer in calendar days
    // ---------------------------------------------------------------
    protected int $constraintBufferDays = 7;

    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    // =================================================================
    //  PUBLIC API
    // =================================================================

    /**
     * Main entry point: Run the CPM schedule calculation for a project.
     *
     * Steps:
     *  1.  Load all activities, dependencies, calendars, drivers, actuals
     *  2.  Validate the dependency graph (cycles, missing nodes)
     *  3.  Build topological sort order (Kahn's algorithm)
     *  4.  Apply actuals (lock completed / in-progress activities)
     *  5.  Forward pass  (compute ES / EF)
     *  6.  Determine project finish date
     *  7.  Backward pass (compute LS / LF)
     *  8.  Compute float + identify critical path
     *  9.  Persist results (ScheduleRun + ScheduleRunActivity)
     * 10.  Update activity computed fields
     *
     * @return array{success: bool, run?: ScheduleRun, violations?: array, error?: string}
     */
    public function calculateSchedule(int $projectId, ?int $scenarioId = null): array
    {
        $startMs = microtime(true);

        try {
            // ----------------------------------------------------------
            // 1. Load data
            // ----------------------------------------------------------
            $project = Project::find($projectId);
            if (!$project) {
                return ['success' => false, 'error' => "Project {$projectId} not found."];
            }

            $activityModels = ScheduleActivity::where('act_project_id', $projectId)->get();
            if ($activityModels->isEmpty()) {
                return ['success' => false, 'error' => 'No activities found for this project.'];
            }

            // Index activities by act_id as plain arrays for fast manipulation
            $activities = [];
            foreach ($activityModels as $m) {
                $activities[$m->act_id] = $m->toArray();
            }

            $dependencies = ScheduleDependency::where('dep_project_id', $projectId)
                ->get()
                ->toArray();

            // Load calendars (project-level + global defaults)
            $this->loadCalendars($projectId);

            // Default calendar for the project
            $defaultCalendar = $this->resolveDefaultCalendar($project);

            // Set near-critical threshold from default calendar
            $this->nearCriticalThresholdMinutes = $defaultCalendar->getWorkMinutesPerDay();

            // Actuals keyed by activity id
            $actuals = [];
            $actualModels = ScheduleActivityActual::whereIn(
                'aca_activity_id',
                array_keys($activities)
            )->get();
            foreach ($actualModels as $a) {
                $actuals[$a->aca_activity_id] = $a->toArray();
            }

            // Active drivers
            $drivers = DB::table('schedule_drivers')
                ->where('drv_project_id', $projectId)
                ->whereIn('drv_status', ['OPEN', 'AT_RISK'])
                ->whereNotNull('drv_activity_id')
                ->get()
                ->toArray();
            // Convert stdClass objects to arrays
            $drivers = array_map(function ($d) {
                return (array) $d;
            }, $drivers);

            // ----------------------------------------------------------
            // 2. Validate graph
            // ----------------------------------------------------------
            $validation = $this->validateGraph($activities, $dependencies);
            if (!$validation['valid']) {
                return [
                    'success'    => false,
                    'error'      => 'Dependency graph is invalid.',
                    'violations' => $validation['errors'],
                ];
            }

            // ----------------------------------------------------------
            // 3. Topological sort
            // ----------------------------------------------------------
            $topoOrder = $this->topologicalSort(array_keys($activities), $dependencies);

            // ----------------------------------------------------------
            // 4-5. Forward pass (applies actuals + drivers internally)
            // ----------------------------------------------------------
            $projectStart = $this->resolveProjectStart($project);
            $violations = $this->forwardPass(
                $activities,
                $dependencies,
                $topoOrder,
                $defaultCalendar,
                $projectStart,
                $actuals,
                $drivers
            );

            // ----------------------------------------------------------
            // 6. Determine project finish date
            // ----------------------------------------------------------
            $projectFinish = $this->determineProjectFinish($activities, $project);

            // ----------------------------------------------------------
            // 7. Backward pass
            // ----------------------------------------------------------
            $this->backwardPass(
                $activities,
                $dependencies,
                $topoOrder,
                $defaultCalendar,
                $projectFinish
            );

            // ----------------------------------------------------------
            // 8. Compute float + critical path
            // ----------------------------------------------------------
            $this->computeFloat($activities, $dependencies, $defaultCalendar);

            // ----------------------------------------------------------
            // Detect negative float violations
            // ----------------------------------------------------------
            foreach ($activities as $id => &$act) {
                if (isset($act['total_float_minutes']) && $act['total_float_minutes'] < 0) {
                    $violations[] = [
                        'type'        => 'negative_float',
                        'activity_id' => $id,
                        'message'     => "Activity \"{$act['act_name']}\" (ID {$id}) has negative float of {$act['total_float_minutes']} minutes.",
                    ];
                }
            }
            unset($act);

            // ----------------------------------------------------------
            // 9-10. Persist results + update activity table
            // ----------------------------------------------------------
            $computationMs = (int) ((microtime(true) - $startMs) * 1000);

            $run = $this->persistResults(
                $projectId,
                $scenarioId,
                $activities,
                $violations,
                $projectFinish,
                $computationMs
            );

            return [
                'success'    => true,
                'run'        => $run,
                'violations' => $violations,
            ];
        } catch (\Throwable $e) {
            Log::error('CpmScheduleService::calculateSchedule failed', [
                'project_id' => $projectId,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate lookahead report for a date window.
     *
     * Returns activities whose early start or early finish falls within the
     * window, together with a readiness assessment.
     *
     * Ready rule:
     *   - All predecessors complete (or will complete before this activity's ES)
     *   - No OPEN drivers attached
     *   - Not BLOCKED status
     *
     * @return array{activities: array, from: string, to: string}
     */
    public function getLookahead(int $projectId, Carbon $from, Carbon $to): array
    {
        $activities = ScheduleActivity::where('act_project_id', $projectId)
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('act_early_start', [$from, $to])
                  ->orWhereBetween('act_early_finish', [$from, $to])
                  ->orWhere(function ($q2) use ($from, $to) {
                      $q2->where('act_early_start', '<=', $from)
                          ->where('act_early_finish', '>=', $to);
                  });
            })
            ->orderBy('act_early_start')
            ->get();

        $result = [];

        foreach ($activities as $activity) {
            // Load predecessors
            $predecessorDeps = ScheduleDependency::where('dep_successor_id', $activity->act_id)->get();
            $predecessors = [];
            $allPredsDone = true;
            $blockingReasons = [];

            foreach ($predecessorDeps as $dep) {
                $pred = ScheduleActivity::find($dep->dep_predecessor_id);
                if (!$pred) {
                    continue;
                }

                $predComplete = $pred->act_status === ScheduleActivity::STATUS_COMPLETE;
                $predWillFinish = $pred->act_early_finish
                    && Carbon::parse($pred->act_early_finish)->lte(Carbon::parse($activity->act_early_start));

                if (!$predComplete && !$predWillFinish) {
                    $allPredsDone = false;
                    $blockingReasons[] = "Predecessor \"{$pred->act_name}\" (ID {$pred->act_id}) not complete; EF = " . ($pred->act_early_finish ?? 'N/A');
                }

                $predecessors[] = [
                    'act_id'   => $pred->act_id,
                    'act_name' => $pred->act_name,
                    'status'   => $pred->act_status,
                    'ef'       => $pred->act_early_finish ? Carbon::parse($pred->act_early_finish)->toDateTimeString() : null,
                    'dep_type' => $dep->dep_type,
                    'dep_lag'  => $dep->dep_lag_minutes,
                ];
            }

            // Check for OPEN drivers
            $openDrivers = DB::table('schedule_drivers')
                ->where('drv_activity_id', $activity->act_id)
                ->whereIn('drv_status', ['OPEN', 'AT_RISK'])
                ->get();

            $hasOpenDrivers = $openDrivers->count() > 0;
            if ($hasOpenDrivers) {
                foreach ($openDrivers as $drv) {
                    $blockingReasons[] = "Open driver: \"{$drv->drv_name}\" (status: {$drv->drv_status})";
                }
            }

            $isBlocked = $activity->act_status === ScheduleActivity::STATUS_BLOCKED;
            if ($isBlocked) {
                $blockingReasons[] = 'Activity is in BLOCKED status.';
            }

            $isReady = $allPredsDone && !$hasOpenDrivers && !$isBlocked;

            $result[] = [
                'act_id'           => $activity->act_id,
                'act_name'         => $activity->act_name,
                'act_type'         => $activity->act_type,
                'act_status'       => $activity->act_status,
                'early_start'      => $activity->act_early_start ? Carbon::parse($activity->act_early_start)->toDateTimeString() : null,
                'early_finish'     => $activity->act_early_finish ? Carbon::parse($activity->act_early_finish)->toDateTimeString() : null,
                'duration_minutes' => $activity->act_duration_minutes,
                'is_critical'      => (bool) $activity->act_is_critical,
                'total_float'      => $activity->act_total_float_minutes,
                'is_ready'         => $isReady,
                'blocking_reasons' => $blockingReasons,
                'predecessors'     => $predecessors,
            ];
        }

        return [
            'activities' => $result,
            'from'       => $from->toDateTimeString(),
            'to'         => $to->toDateTimeString(),
        ];
    }

    /**
     * Get constraint log for lookahead planning.
     *
     * For each activity in the window, list blocking drivers with needed-by
     * dates (ES minus a configurable buffer, default 7 calendar days).
     *
     * @return array
     */
    public function getConstraintLog(int $projectId, Carbon $from, Carbon $to): array
    {
        $activities = ScheduleActivity::where('act_project_id', $projectId)
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('act_early_start', [$from, $to])
                  ->orWhereBetween('act_early_finish', [$from, $to]);
            })
            ->orderBy('act_early_start')
            ->get();

        $log = [];

        foreach ($activities as $activity) {
            $drivers = DB::table('schedule_drivers')
                ->where('drv_activity_id', $activity->act_id)
                ->whereIn('drv_status', ['OPEN', 'AT_RISK'])
                ->get();

            if ($drivers->isEmpty()) {
                continue;
            }

            foreach ($drivers as $driver) {
                $neededBy = $activity->act_early_start
                    ? Carbon::parse($activity->act_early_start)->subDays($this->constraintBufferDays)
                    : null;

                $log[] = [
                    'activity_id'   => $activity->act_id,
                    'activity_name' => $activity->act_name,
                    'early_start'   => $activity->act_early_start ? Carbon::parse($activity->act_early_start)->toDateTimeString() : null,
                    'driver_id'     => $driver->drv_id,
                    'driver_name'   => $driver->drv_name,
                    'driver_type'   => $driver->drv_type,
                    'driver_status' => $driver->drv_status,
                    'driver_confidence' => $driver->drv_confidence,
                    'needed_by_date'    => $neededBy ? $neededBy->toDateTimeString() : null,
                    'constraint_date'   => $driver->drv_constraint_date,
                    'owner_role'        => $driver->drv_type, // driver type often maps to responsible role
                    'evidence_link'     => $driver->drv_evidence_link,
                ];
            }
        }

        return [
            'constraints' => $log,
            'from'        => $from->toDateTimeString(),
            'to'          => $to->toDateTimeString(),
            'buffer_days' => $this->constraintBufferDays,
        ];
    }

    /**
     * Create a baseline snapshot from the current schedule.
     *
     * @return array{success: bool, baseline?: ScheduleBaseline, error?: string}
     */
    public function createBaseline(int $projectId, string $name): array
    {
        try {
            DB::beginTransaction();

            $baseline = ScheduleBaseline::create([
                'bl_project_id' => $projectId,
                'bl_name'       => $name,
                'bl_created_by' => auth()->id(),
                'bl_created_at' => Carbon::now(),
            ]);

            $activities = ScheduleActivity::where('act_project_id', $projectId)->get();

            foreach ($activities as $activity) {
                ScheduleBaselineActivity::create([
                    'bla_baseline_id'     => $baseline->bl_id,
                    'bla_activity_id'     => $activity->act_id,
                    'bla_start'           => $activity->act_early_start,
                    'bla_finish'          => $activity->act_early_finish,
                    'bla_duration_minutes' => $activity->act_duration_minutes,
                ]);
            }

            DB::commit();

            return [
                'success'  => true,
                'baseline' => $baseline,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('CpmScheduleService::createBaseline failed', [
                'project_id' => $projectId,
                'error'      => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get baseline variance report.
     *
     * For each activity, compares the current schedule dates against the
     * baseline snapshot dates.
     *
     * @return array{success: bool, activities?: array, baseline?: array, error?: string}
     */
    public function getBaselineVariance(int $projectId, int $baselineId): array
    {
        $baseline = ScheduleBaseline::find($baselineId);
        if (!$baseline || $baseline->bl_project_id !== $projectId) {
            return ['success' => false, 'error' => 'Baseline not found for this project.'];
        }

        $project = Project::find($projectId);
        $defaultCalendar = $this->resolveDefaultCalendar($project);
        $this->loadCalendars($projectId);

        $baselineActivities = ScheduleBaselineActivity::where('bla_baseline_id', $baselineId)
            ->get()
            ->keyBy('bla_activity_id');

        $currentActivities = ScheduleActivity::where('act_project_id', $projectId)
            ->get();

        $variances = [];

        foreach ($currentActivities as $current) {
            $bl = $baselineActivities->get($current->act_id);
            if (!$bl) {
                $variances[] = [
                    'act_id'   => $current->act_id,
                    'act_name' => $current->act_name,
                    'note'     => 'Activity added after baseline.',
                ];
                continue;
            }

            $startVariance = 0;
            $finishVariance = 0;
            $durationVariance = $current->act_duration_minutes - $bl->bla_duration_minutes;

            if ($current->act_early_start && $bl->bla_start) {
                $calendar = $this->getCalendar($current->toArray(), $defaultCalendar);
                $startVariance = $this->calendarService->workMinutesBetween(
                    $calendar,
                    Carbon::parse($bl->bla_start),
                    Carbon::parse($current->act_early_start)
                );
            }

            if ($current->act_early_finish && $bl->bla_finish) {
                $calendar = $this->getCalendar($current->toArray(), $defaultCalendar);
                $finishVariance = $this->calendarService->workMinutesBetween(
                    $calendar,
                    Carbon::parse($bl->bla_finish),
                    Carbon::parse($current->act_early_finish)
                );
            }

            $variances[] = [
                'act_id'            => $current->act_id,
                'act_name'          => $current->act_name,
                'act_type'          => $current->act_type,
                'baseline_start'    => $bl->bla_start ? Carbon::parse($bl->bla_start)->toDateTimeString() : null,
                'baseline_finish'   => $bl->bla_finish ? Carbon::parse($bl->bla_finish)->toDateTimeString() : null,
                'baseline_duration' => $bl->bla_duration_minutes,
                'current_start'     => $current->act_early_start ? Carbon::parse($current->act_early_start)->toDateTimeString() : null,
                'current_finish'    => $current->act_early_finish ? Carbon::parse($current->act_early_finish)->toDateTimeString() : null,
                'current_duration'  => $current->act_duration_minutes,
                'start_variance_minutes'    => $startVariance,
                'finish_variance_minutes'   => $finishVariance,
                'duration_variance_minutes' => $durationVariance,
            ];
        }

        // Activities removed since baseline
        foreach ($baselineActivities as $actId => $bl) {
            if (!$currentActivities->contains('act_id', $actId)) {
                $variances[] = [
                    'act_id'   => $actId,
                    'act_name' => '(deleted)',
                    'note'     => 'Activity existed in baseline but has been removed.',
                ];
            }
        }

        return [
            'success'    => true,
            'baseline'   => [
                'id'         => $baseline->bl_id,
                'name'       => $baseline->bl_name,
                'created_at' => $baseline->bl_created_at ? Carbon::parse($baseline->bl_created_at)->toDateTimeString() : null,
            ],
            'activities' => $variances,
        ];
    }

    /**
     * Get critical path as an ordered list of activity IDs.
     *
     * Returns activities flagged as critical in the most recent schedule run,
     * sorted in topological (dependency) order.
     *
     * @return array
     */
    public function getCriticalPath(int $projectId): array
    {
        $criticalActivities = ScheduleActivity::where('act_project_id', $projectId)
            ->where('act_is_critical', 1)
            ->orderBy('act_early_start')
            ->get();

        $path = [];
        foreach ($criticalActivities as $act) {
            $path[] = [
                'act_id'       => $act->act_id,
                'act_name'     => $act->act_name,
                'act_type'     => $act->act_type,
                'early_start'  => $act->act_early_start ? Carbon::parse($act->act_early_start)->toDateTimeString() : null,
                'early_finish' => $act->act_early_finish ? Carbon::parse($act->act_early_finish)->toDateTimeString() : null,
                'duration_minutes' => $act->act_duration_minutes,
                'total_float'      => $act->act_total_float_minutes,
            ];
        }

        return $path;
    }

    /**
     * Get schedule health summary for a project.
     *
     * Includes:
     *  - Total activities
     *  - Critical count
     *  - Near-critical count (float <= 1 working day)
     *  - Completion %
     *  - Overdue activities
     *  - Schedule health grade (A / B / C / D / F)
     *
     * @return array
     */
    public function getHealthSummary(int $projectId): array
    {
        $project = Project::find($projectId);
        if (!$project) {
            return ['error' => 'Project not found.'];
        }

        $defaultCalendar = $this->resolveDefaultCalendar($project);
        $minutesPerDay = $defaultCalendar->getWorkMinutesPerDay();

        $activities = ScheduleActivity::where('act_project_id', $projectId)->get();

        $total         = $activities->count();
        $complete      = 0;
        $criticalCount = 0;
        $nearCritical  = 0;
        $overdue       = 0;
        $now           = Carbon::now();

        foreach ($activities as $act) {
            if ($act->act_status === ScheduleActivity::STATUS_COMPLETE) {
                $complete++;
            }

            if ($act->act_is_critical) {
                $criticalCount++;
            }

            // Near-critical: float > 0 but <= 1 working day
            if (
                !$act->act_is_critical
                && $act->act_total_float_minutes !== null
                && $act->act_total_float_minutes > 0
                && $act->act_total_float_minutes <= $minutesPerDay
            ) {
                $nearCritical++;
            }

            // Overdue: early finish in the past and not complete
            if (
                $act->act_status !== ScheduleActivity::STATUS_COMPLETE
                && $act->act_early_finish
                && Carbon::parse($act->act_early_finish)->lt($now)
            ) {
                $overdue++;
            }
        }

        $completionPct = $total > 0 ? round(($complete / $total) * 100, 1) : 0;

        // Health grade logic
        $grade = $this->computeHealthGrade($total, $criticalCount, $nearCritical, $overdue, $completionPct);

        // Latest run info
        $latestRun = ScheduleRun::where('run_project_id', $projectId)
            ->orderByDesc('run_created_at')
            ->first();

        return [
            'project_id'          => $projectId,
            'total_activities'    => $total,
            'complete_activities' => $complete,
            'completion_pct'      => $completionPct,
            'critical_count'      => $criticalCount,
            'near_critical_count' => $nearCritical,
            'overdue_count'       => $overdue,
            'health_grade'        => $grade,
            'last_run_at'         => $latestRun ? Carbon::parse($latestRun->run_created_at)->toDateTimeString() : null,
            'last_run_ms'         => $latestRun ? $latestRun->run_computation_ms : null,
            'project_finish'      => $latestRun && $latestRun->run_project_finish
                ? Carbon::parse($latestRun->run_project_finish)->toDateTimeString()
                : null,
        ];
    }

    // =================================================================
    //  GRAPH VALIDATION
    // =================================================================

    /**
     * Validate the dependency graph: check for cycles and missing references.
     *
     * Uses DFS-based cycle detection.
     *
     * @param  array  $activities    Keyed by act_id
     * @param  array  $dependencies  Flat array of dependency records
     * @return array{valid: bool, errors: array}
     */
    protected function validateGraph(array $activities, array $dependencies): array
    {
        $errors = [];
        $actIds = array_keys($activities);

        // Check for missing predecessor/successor references
        foreach ($dependencies as $dep) {
            $predId = $dep['dep_predecessor_id'];
            $succId = $dep['dep_successor_id'];

            if (!isset($activities[$predId])) {
                $errors[] = [
                    'type'        => 'logic_violation',
                    'activity_id' => $predId,
                    'message'     => "Dependency references non-existent predecessor activity ID {$predId}.",
                ];
            }
            if (!isset($activities[$succId])) {
                $errors[] = [
                    'type'        => 'logic_violation',
                    'activity_id' => $succId,
                    'message'     => "Dependency references non-existent successor activity ID {$succId}.",
                ];
            }
            if ($predId === $succId) {
                $errors[] = [
                    'type'        => 'logic_violation',
                    'activity_id' => $predId,
                    'message'     => "Activity ID {$predId} has a self-referencing dependency.",
                ];
            }
        }

        // Build adjacency list for DFS cycle detection
        $adj = [];
        foreach ($actIds as $id) {
            $adj[$id] = [];
        }
        foreach ($dependencies as $dep) {
            $predId = $dep['dep_predecessor_id'];
            $succId = $dep['dep_successor_id'];
            if (isset($adj[$predId])) {
                $adj[$predId][] = $succId;
            }
        }

        // DFS-based cycle detection using 3-color marking
        // WHITE = 0 (unvisited), GRAY = 1 (in progress), BLACK = 2 (done)
        $color = array_fill_keys($actIds, 0);
        $cycleFound = false;
        $cyclePath = [];

        $dfs = function (int $node, array &$path) use (&$dfs, &$adj, &$color, &$errors, &$cycleFound) {
            $color[$node] = 1; // GRAY
            $path[] = $node;

            foreach ($adj[$node] ?? [] as $neighbor) {
                if (!isset($color[$neighbor])) {
                    continue; // skip missing nodes (already reported)
                }
                if ($color[$neighbor] === 1) {
                    // Cycle detected - extract the cycle portion
                    $cycleStart = array_search($neighbor, $path);
                    $cycle = array_slice($path, $cycleStart);
                    $cycle[] = $neighbor;
                    $cycleIds = implode(' -> ', $cycle);
                    $errors[] = [
                        'type'        => 'logic_violation',
                        'activity_id' => $neighbor,
                        'message'     => "Circular dependency detected: {$cycleIds}",
                    ];
                    $cycleFound = true;
                    return;
                }
                if ($color[$neighbor] === 0) {
                    $dfs($neighbor, $path);
                    if ($cycleFound) {
                        return;
                    }
                }
            }

            array_pop($path);
            $color[$node] = 2; // BLACK
        };

        foreach ($actIds as $id) {
            if ($color[$id] === 0) {
                $path = [];
                $dfs($id, $path);
                if ($cycleFound) {
                    break; // report first cycle found
                }
            }
        }

        return [
            'valid'  => empty($errors),
            'errors' => $errors,
        ];
    }

    // =================================================================
    //  TOPOLOGICAL SORT (Kahn's algorithm)
    // =================================================================

    /**
     * Build topological sort order using Kahn's algorithm.
     *
     * @param  array  $activityIds   List of activity IDs
     * @param  array  $dependencies  Flat array of dependency records
     * @return array  Activity IDs in topological order
     *
     * @throws \RuntimeException if graph has a cycle
     */
    protected function topologicalSort(array $activityIds, array $dependencies): array
    {
        // Build in-degree map and adjacency list
        $inDegree = array_fill_keys($activityIds, 0);
        $adj = [];
        foreach ($activityIds as $id) {
            $adj[$id] = [];
        }

        foreach ($dependencies as $dep) {
            $pred = $dep['dep_predecessor_id'];
            $succ = $dep['dep_successor_id'];

            // Skip references to activities not in our set
            if (!isset($inDegree[$pred]) || !isset($inDegree[$succ])) {
                continue;
            }

            $adj[$pred][] = $succ;
            $inDegree[$succ]++;
        }

        // Seed the queue with zero in-degree nodes
        $queue = [];
        foreach ($inDegree as $id => $deg) {
            if ($deg === 0) {
                $queue[] = $id;
            }
        }

        $sorted = [];

        while (!empty($queue)) {
            $node = array_shift($queue);
            $sorted[] = $node;

            foreach ($adj[$node] as $neighbor) {
                $inDegree[$neighbor]--;
                if ($inDegree[$neighbor] === 0) {
                    $queue[] = $neighbor;
                }
            }
        }

        if (count($sorted) !== count($activityIds)) {
            throw new \RuntimeException(
                'Topological sort failed: dependency graph contains a cycle. '
                . count($sorted) . ' of ' . count($activityIds) . ' activities sorted.'
            );
        }

        return $sorted;
    }

    // =================================================================
    //  CALENDAR HELPERS
    // =================================================================

    /**
     * Get the effective calendar for an activity.
     *
     * Falls back to the project default calendar if the activity has no
     * calendar assigned.
     */
    protected function getCalendar(array $activity, ScheduleCalendar $defaultCalendar): ScheduleCalendar
    {
        $calId = $activity['act_calendar_id'] ?? null;

        if ($calId && isset($this->calendars[$calId])) {
            return $this->calendars[$calId];
        }

        return $defaultCalendar;
    }

    /**
     * Load all calendars relevant to a project into the local cache.
     */
    protected function loadCalendars(int $projectId): void
    {
        $this->calendars = [];

        $cals = ScheduleCalendar::where(function ($q) use ($projectId) {
            $q->where('cal_project_id', $projectId)
              ->orWhereNull('cal_project_id');
        })
        ->where('cal_status', 1)
        ->get();

        foreach ($cals as $cal) {
            $this->calendars[$cal->cal_id] = $cal;
        }
    }

    /**
     * Resolve the default calendar for a project.
     *
     * Priority: project's assigned default calendar -> project-level default
     * flag -> first active calendar.
     */
    protected function resolveDefaultCalendar(Project $project): ScheduleCalendar
    {
        // 1. Explicit project default
        $calId = $project->proj_default_calendar_id ?? null;
        if ($calId && isset($this->calendars[$calId])) {
            return $this->calendars[$calId];
        }

        // 2. Calendar flagged as default for this project
        $default = ScheduleCalendar::where('cal_project_id', $project->proj_id)
            ->where('cal_is_default', 1)
            ->where('cal_status', 1)
            ->first();
        if ($default) {
            $this->calendars[$default->cal_id] = $default;
            return $default;
        }

        // 3. Global default calendar (no project)
        $global = ScheduleCalendar::whereNull('cal_project_id')
            ->where('cal_is_default', 1)
            ->where('cal_status', 1)
            ->first();
        if ($global) {
            $this->calendars[$global->cal_id] = $global;
            return $global;
        }

        // 4. Any active calendar for this project
        $any = ScheduleCalendar::where('cal_project_id', $project->proj_id)
            ->where('cal_status', 1)
            ->first();
        if ($any) {
            $this->calendars[$any->cal_id] = $any;
            return $any;
        }

        // 5. Any active calendar at all
        $fallback = ScheduleCalendar::where('cal_status', 1)->first();
        if ($fallback) {
            $this->calendars[$fallback->cal_id] = $fallback;
            return $fallback;
        }

        // 6. Create a synthetic in-memory default (Mon-Fri 07:00-15:30)
        $synthetic = new ScheduleCalendar();
        $synthetic->cal_id = 0;
        $synthetic->cal_name = 'Default (auto-generated)';
        $synthetic->cal_work_week = 'Mon,Tue,Wed,Thu,Fri';
        $synthetic->cal_work_start = '07:00';
        $synthetic->cal_work_end = '15:30';
        $synthetic->cal_timezone = 'America/New_York';
        $synthetic->cal_is_default = 1;
        $synthetic->cal_status = 1;

        return $synthetic;
    }

    // =================================================================
    //  PROJECT HELPERS
    // =================================================================

    /**
     * Determine the project start date.
     *
     * Uses progress_date if set, otherwise falls back to today.
     */
    protected function resolveProjectStart(Project $project): Carbon
    {
        if (!empty($project->proj_progress_date)) {
            return Carbon::parse($project->proj_progress_date);
        }

        return Carbon::today();
    }

    /**
     * Determine the project finish date from computed early finishes.
     *
     * If the project has a target finish date, use whichever is later
     * (to preserve the backward pass semantics -- LF cannot exceed
     * project finish). If the target is earlier than the computed finish,
     * it will result in negative float on some activities.
     */
    protected function determineProjectFinish(array $activities, Project $project): Carbon
    {
        $maxEf = null;

        foreach ($activities as $act) {
            if (!empty($act['early_finish'])) {
                $ef = Carbon::parse($act['early_finish']);
                if ($maxEf === null || $ef->gt($maxEf)) {
                    $maxEf = $ef;
                }
            }
        }

        if ($maxEf === null) {
            $maxEf = Carbon::today();
        }

        // If a target finish date is set, use whichever is later so that
        // negative float surfaces when the schedule overshoots.
        if (!empty($project->proj_target_finish_date)) {
            $target = Carbon::parse($project->proj_target_finish_date);
            // Use the target as project finish for backward pass;
            // if schedule exceeds target, negative float will appear.
            return $target;
        }

        return $maxEf;
    }

    // =================================================================
    //  FORWARD PASS
    // =================================================================

    /**
     * Forward pass: compute Early Start (ES) and Early Finish (EF).
     *
     * Handles all four dependency types (FS, SS, FF, SF), activity
     * constraints (SNET, MSO), actuals lock-down, and active drivers.
     *
     * @param  array  $activities    Keyed by act_id, modified in-place
     * @param  array  $dependencies
     * @param  array  $topoOrder
     * @param  ScheduleCalendar $defaultCalendar
     * @param  Carbon $projectStart
     * @param  array  $actuals       Keyed by act_id
     * @param  array  $drivers       Active driver records (OPEN / AT_RISK)
     * @return array  Violations detected during forward pass
     */
    protected function forwardPass(
        array &$activities,
        array $dependencies,
        array $topoOrder,
        ScheduleCalendar $defaultCalendar,
        Carbon $projectStart,
        array $actuals,
        array $drivers
    ): array {
        $violations = [];

        // Build predecessor lookup: successorId => [dep, dep, ...]
        $predMap = [];
        foreach ($dependencies as $dep) {
            $succId = $dep['dep_successor_id'];
            $predMap[$succId][] = $dep;
        }

        // Build driver lookup: activityId => [driver, driver, ...]
        $driverMap = [];
        foreach ($drivers as $drv) {
            $actId = $drv['drv_activity_id'];
            $driverMap[$actId][] = $drv;
        }

        foreach ($topoOrder as $actId) {
            $act = &$activities[$actId];
            $calendar = $this->getCalendar($act, $defaultCalendar);
            $isMilestone = ($act['act_type'] === ScheduleActivity::TYPE_MILESTONE);
            $duration = (int) ($act['act_duration_minutes'] ?? 0);

            // -------------------------------------------------------
            // 4. Apply actuals lock-down
            // -------------------------------------------------------
            if (isset($actuals[$actId])) {
                $actual = $actuals[$actId];

                // Completed activity: lock both dates
                if (!empty($actual['aca_actual_finish'])) {
                    $act['early_start']  = Carbon::parse($actual['aca_actual_start'])->copy();
                    $act['early_finish'] = Carbon::parse($actual['aca_actual_finish'])->copy();
                    $act['driving_predecessor_id'] = null;
                    $act['driving_constraint_id']  = null;
                    continue; // skip all other logic
                }

                // In-progress: lock start, use remaining duration
                if (!empty($actual['aca_actual_start'])) {
                    $lockedStart = Carbon::parse($actual['aca_actual_start'])->copy();
                    $remainDur = $actual['aca_remaining_duration_minutes'] ?? $duration;

                    $es = $this->calendarService->nextWorkTime($calendar, $lockedStart);
                    if ($isMilestone) {
                        $ef = $es->copy();
                    } else {
                        $ef = $this->calendarService->addWorkMinutes($calendar, $es->copy(), $remainDur);
                    }

                    $act['early_start']  = $es;
                    $act['early_finish'] = $ef;
                    $act['driving_predecessor_id'] = null;
                    $act['driving_constraint_id']  = null;
                    continue;
                }
            }

            // -------------------------------------------------------
            // 5. Compute candidate start from predecessors
            // -------------------------------------------------------
            $candidateStart = $projectStart->copy();
            $drivingPredId = null;
            $drivingConstraintId = null;

            if (isset($predMap[$actId])) {
                foreach ($predMap[$actId] as $dep) {
                    $predId = $dep['dep_predecessor_id'];

                    // Skip if predecessor wasn't computed yet (shouldn't happen after topo sort)
                    if (!isset($activities[$predId]['early_start']) || !isset($activities[$predId]['early_finish'])) {
                        continue;
                    }

                    $predEs = $activities[$predId]['early_start'] instanceof Carbon
                        ? $activities[$predId]['early_start']->copy()
                        : Carbon::parse($activities[$predId]['early_start']);

                    $predEf = $activities[$predId]['early_finish'] instanceof Carbon
                        ? $activities[$predId]['early_finish']->copy()
                        : Carbon::parse($activities[$predId]['early_finish']);

                    $lag = (int) ($dep['dep_lag_minutes'] ?? 0);
                    $depType = $dep['dep_type'] ?? 'FS';

                    $newCandidate = null;

                    // -----------------------------------------------
                    // FS (Finish-to-Start): most common
                    // successor.ES >= predecessor.EF + lag
                    // -----------------------------------------------
                    if ($depType === 'FS') {
                        if ($lag !== 0) {
                            $newCandidate = $this->calendarService->addWorkMinutes(
                                $calendar,
                                $predEf->copy(),
                                $lag
                            );
                        } else {
                            $newCandidate = $predEf->copy();
                        }
                    }

                    // -----------------------------------------------
                    // SS (Start-to-Start):
                    // successor.ES >= predecessor.ES + lag
                    // -----------------------------------------------
                    elseif ($depType === 'SS') {
                        if ($lag !== 0) {
                            $newCandidate = $this->calendarService->addWorkMinutes(
                                $calendar,
                                $predEs->copy(),
                                $lag
                            );
                        } else {
                            $newCandidate = $predEs->copy();
                        }
                    }

                    // -----------------------------------------------
                    // FF (Finish-to-Finish):
                    // successor.EF >= predecessor.EF + lag
                    // => successor.ES = successor.EF - duration
                    // We compute the candidate finish first, then
                    // derive the candidate start.
                    // -----------------------------------------------
                    elseif ($depType === 'FF') {
                        $candidateFinish = $predEf->copy();
                        if ($lag !== 0) {
                            $candidateFinish = $this->calendarService->addWorkMinutes(
                                $calendar,
                                $candidateFinish,
                                $lag
                            );
                        }
                        // Derive start from finish
                        if ($isMilestone) {
                            $newCandidate = $candidateFinish->copy();
                        } else {
                            $newCandidate = $this->calendarService->subtractWorkMinutes(
                                $calendar,
                                $candidateFinish->copy(),
                                $duration
                            );
                        }
                    }

                    // -----------------------------------------------
                    // SF (Start-to-Finish):
                    // successor.EF >= predecessor.ES + lag
                    // => successor.ES = successor.EF - duration
                    // -----------------------------------------------
                    elseif ($depType === 'SF') {
                        $candidateFinish = $predEs->copy();
                        if ($lag !== 0) {
                            $candidateFinish = $this->calendarService->addWorkMinutes(
                                $calendar,
                                $candidateFinish,
                                $lag
                            );
                        }
                        if ($isMilestone) {
                            $newCandidate = $candidateFinish->copy();
                        } else {
                            $newCandidate = $this->calendarService->subtractWorkMinutes(
                                $calendar,
                                $candidateFinish->copy(),
                                $duration
                            );
                        }
                    }

                    // Pick the latest candidate (forward pass = max)
                    if ($newCandidate !== null && $newCandidate->gt($candidateStart)) {
                        $candidateStart = $newCandidate;
                        $drivingPredId = $predId;
                    }
                }
            }

            // -------------------------------------------------------
            // Apply activity constraints (SNET, MSO)
            // -------------------------------------------------------
            $constraintType = $act['act_constraint_type'] ?? 'NONE';
            $constraintDate = !empty($act['act_constraint_date'])
                ? Carbon::parse($act['act_constraint_date'])
                : null;

            if ($constraintDate) {
                // SNET: Start No Earlier Than
                if ($constraintType === 'SNET') {
                    if ($constraintDate->gt($candidateStart)) {
                        $candidateStart = $constraintDate->copy();
                        $drivingPredId = null;
                        $drivingConstraintId = $actId; // self-constraint drives
                    }
                }

                // MSO: Must Start On
                if ($constraintType === 'MSO') {
                    if ($constraintDate->gt($candidateStart)) {
                        $candidateStart = $constraintDate->copy();
                        $drivingPredId = null;
                        $drivingConstraintId = $actId;
                    } elseif ($constraintDate->lt($candidateStart)) {
                        $violations[] = [
                            'type'        => 'constraint_violation',
                            'activity_id' => $actId,
                            'message'     => "MSO constraint date (" . $constraintDate->toDateString()
                                . ") is earlier than predecessor-driven start ("
                                . $candidateStart->toDateString() . ") for \"{$act['act_name']}\".",
                        ];
                        // MSO is hard -- force the date anyway
                        $candidateStart = $constraintDate->copy();
                        $drivingPredId = null;
                        $drivingConstraintId = $actId;
                    }
                }
            }

            // -------------------------------------------------------
            // Apply active drivers (OPEN / AT_RISK with constraint dates)
            // -------------------------------------------------------
            if (isset($driverMap[$actId])) {
                foreach ($driverMap[$actId] as $drv) {
                    if (!empty($drv['drv_constraint_date'])) {
                        $drvDate = Carbon::parse($drv['drv_constraint_date']);
                        // Drivers with constraint_type SNET or none act like SNET
                        $drvConstraint = $drv['drv_constraint_type'] ?? 'SNET';
                        if ($drvConstraint === 'SNET' || $drvConstraint === 'MSO' || $drvConstraint === 'NONE') {
                            if ($drvDate->gt($candidateStart)) {
                                $candidateStart = $drvDate->copy();
                                $drivingPredId = null;
                                $drivingConstraintId = $drv['drv_id'];
                            }
                        }
                    }
                }
            }

            // -------------------------------------------------------
            // Snap to next work time & compute finish
            // -------------------------------------------------------
            $es = $this->calendarService->nextWorkTime($calendar, $candidateStart);

            if ($isMilestone) {
                $ef = $es->copy();
            } else {
                $ef = $this->calendarService->addWorkMinutes($calendar, $es->copy(), $duration);
            }

            $act['early_start']              = $es;
            $act['early_finish']             = $ef;
            $act['driving_predecessor_id']   = $drivingPredId;
            $act['driving_constraint_id']    = $drivingConstraintId;
        }

        unset($act);

        return $violations;
    }

    // =================================================================
    //  BACKWARD PASS
    // =================================================================

    /**
     * Backward pass: compute Late Start (LS) and Late Finish (LF).
     *
     * Starting from the project finish date, works backward through the
     * topological order computing the latest allowable dates.
     *
     * @param  array            $activities      Keyed by act_id, modified in-place
     * @param  array            $dependencies
     * @param  array            $topoOrder
     * @param  ScheduleCalendar $defaultCalendar
     * @param  Carbon           $projectFinish
     */
    protected function backwardPass(
        array &$activities,
        array $dependencies,
        array $topoOrder,
        ScheduleCalendar $defaultCalendar,
        Carbon $projectFinish
    ): void {
        // Build successor lookup: predecessorId => [dep, dep, ...]
        $succMap = [];
        foreach ($dependencies as $dep) {
            $predId = $dep['dep_predecessor_id'];
            $succMap[$predId][] = $dep;
        }

        // Process in reverse topological order
        $reversed = array_reverse($topoOrder);

        foreach ($reversed as $actId) {
            $act = &$activities[$actId];
            $calendar = $this->getCalendar($act, $defaultCalendar);
            $isMilestone = ($act['act_type'] === ScheduleActivity::TYPE_MILESTONE);
            $duration = (int) ($act['act_duration_minutes'] ?? 0);

            // -------------------------------------------------------
            // Default LF = project finish (if no successors)
            // -------------------------------------------------------
            $latestFinish = $projectFinish->copy();

            if (isset($succMap[$actId])) {
                foreach ($succMap[$actId] as $dep) {
                    $succId = $dep['dep_successor_id'];

                    if (!isset($activities[$succId]['late_start']) && !isset($activities[$succId]['late_finish'])) {
                        continue;
                    }

                    $succLs = $activities[$succId]['late_start'] instanceof Carbon
                        ? $activities[$succId]['late_start']->copy()
                        : Carbon::parse($activities[$succId]['late_start']);

                    $succLf = $activities[$succId]['late_finish'] instanceof Carbon
                        ? $activities[$succId]['late_finish']->copy()
                        : Carbon::parse($activities[$succId]['late_finish']);

                    $lag = (int) ($dep['dep_lag_minutes'] ?? 0);
                    $depType = $dep['dep_type'] ?? 'FS';

                    $newLatest = null;

                    // FS: predecessor.LF <= successor.LS - lag
                    if ($depType === 'FS') {
                        if ($lag !== 0) {
                            $newLatest = $this->calendarService->subtractWorkMinutes(
                                $calendar,
                                $succLs->copy(),
                                $lag
                            );
                        } else {
                            $newLatest = $succLs->copy();
                        }
                    }

                    // SS: predecessor.LS <= successor.LS - lag
                    // => predecessor.LF = predecessor.LS + duration
                    // We need to compute predecessor.LS first, then derive LF.
                    // For backward pass consistency, compute as:
                    //   candidate_LS = successor.LS - lag
                    //   candidate_LF = candidate_LS + duration
                    elseif ($depType === 'SS') {
                        $candidateLs = $succLs->copy();
                        if ($lag !== 0) {
                            $candidateLs = $this->calendarService->subtractWorkMinutes(
                                $calendar,
                                $candidateLs,
                                $lag
                            );
                        }
                        // Derive LF from this LS
                        if ($isMilestone) {
                            $newLatest = $candidateLs->copy();
                        } else {
                            $newLatest = $this->calendarService->addWorkMinutes(
                                $calendar,
                                $candidateLs->copy(),
                                $duration
                            );
                        }
                    }

                    // FF: predecessor.LF <= successor.LF - lag
                    elseif ($depType === 'FF') {
                        if ($lag !== 0) {
                            $newLatest = $this->calendarService->subtractWorkMinutes(
                                $calendar,
                                $succLf->copy(),
                                $lag
                            );
                        } else {
                            $newLatest = $succLf->copy();
                        }
                    }

                    // SF: predecessor.LS <= successor.LF - lag
                    // => predecessor.LF = predecessor.LS + duration
                    elseif ($depType === 'SF') {
                        $candidateLs = $succLf->copy();
                        if ($lag !== 0) {
                            $candidateLs = $this->calendarService->subtractWorkMinutes(
                                $calendar,
                                $candidateLs,
                                $lag
                            );
                        }
                        if ($isMilestone) {
                            $newLatest = $candidateLs->copy();
                        } else {
                            $newLatest = $this->calendarService->addWorkMinutes(
                                $calendar,
                                $candidateLs->copy(),
                                $duration
                            );
                        }
                    }

                    // Pick the earliest (backward pass = min)
                    if ($newLatest !== null && $newLatest->lt($latestFinish)) {
                        $latestFinish = $newLatest;
                    }
                }
            }

            // -------------------------------------------------------
            // Apply finish constraints (FNLT, MFO)
            // -------------------------------------------------------
            $constraintType = $act['act_constraint_type'] ?? 'NONE';
            $constraintDate = !empty($act['act_constraint_date'])
                ? Carbon::parse($act['act_constraint_date'])
                : null;

            if ($constraintDate) {
                // FNLT: Finish No Later Than
                if ($constraintType === 'FNLT') {
                    if ($constraintDate->lt($latestFinish)) {
                        $latestFinish = $constraintDate->copy();
                    }
                }

                // MFO: Must Finish On
                if ($constraintType === 'MFO') {
                    $latestFinish = $constraintDate->copy();
                }
            }

            // -------------------------------------------------------
            // Compute LS from LF
            // -------------------------------------------------------
            $lf = $latestFinish;

            if ($isMilestone) {
                $ls = $lf->copy();
            } else {
                $ls = $this->calendarService->subtractWorkMinutes($calendar, $lf->copy(), $duration);
            }

            $act['late_start']  = $ls;
            $act['late_finish'] = $lf;
        }

        unset($act);
    }

    // =================================================================
    //  FLOAT COMPUTATION
    // =================================================================

    /**
     * Compute total float and free float for each activity.
     *
     * Total Float  = LS - ES  (in working minutes)
     * Free Float   = min(successor.ES) - EF  (in working minutes, for FS deps)
     *
     * Mark activity as critical if total_float <= 0.
     */
    protected function computeFloat(
        array &$activities,
        array $dependencies,
        ScheduleCalendar $defaultCalendar
    ): void {
        // Build successor lookup for free float: predecessorId => [dep, ...]
        $succMap = [];
        foreach ($dependencies as $dep) {
            $predId = $dep['dep_predecessor_id'];
            $succMap[$predId][] = $dep;
        }

        foreach ($activities as $actId => &$act) {
            $calendar = $this->getCalendar($act, $defaultCalendar);

            // -------------------------------------------------------
            // Total Float = LS - ES (in working minutes)
            // -------------------------------------------------------
            $es = $act['early_start'] ?? null;
            $ls = $act['late_start'] ?? null;

            if ($es && $ls) {
                $esCarbon = $es instanceof Carbon ? $es : Carbon::parse($es);
                $lsCarbon = $ls instanceof Carbon ? $ls : Carbon::parse($ls);

                if ($lsCarbon->gte($esCarbon)) {
                    $act['total_float_minutes'] = $this->calendarService->workMinutesBetween(
                        $calendar,
                        $esCarbon,
                        $lsCarbon
                    );
                } else {
                    // Negative float: LS before ES
                    $act['total_float_minutes'] = -1 * $this->calendarService->workMinutesBetween(
                        $calendar,
                        $lsCarbon,
                        $esCarbon
                    );
                }
            } else {
                $act['total_float_minutes'] = 0;
            }

            // -------------------------------------------------------
            // Free Float = min(successor.ES) - EF  (FS relationships)
            // For other dep types, compute the appropriate gap.
            // -------------------------------------------------------
            $ef = $act['early_finish'] ?? null;
            $efCarbon = $ef instanceof Carbon ? $ef : ($ef ? Carbon::parse($ef) : null);

            if ($efCarbon && isset($succMap[$actId])) {
                $minGap = null;

                foreach ($succMap[$actId] as $dep) {
                    $succId = $dep['dep_successor_id'];
                    if (!isset($activities[$succId]['early_start'])) {
                        continue;
                    }

                    $succEs = $activities[$succId]['early_start'];
                    $succEsCarbon = $succEs instanceof Carbon ? $succEs : Carbon::parse($succEs);

                    $lag = (int) ($dep['dep_lag_minutes'] ?? 0);
                    $depType = $dep['dep_type'] ?? 'FS';

                    // For FS: free float = successor.ES - (EF + lag)
                    // For simplicity, compute raw gap in working minutes
                    $gap = 0;
                    if ($depType === 'FS') {
                        $adjustedEf = $efCarbon->copy();
                        if ($lag !== 0) {
                            $adjustedEf = $this->calendarService->addWorkMinutes($calendar, $adjustedEf, $lag);
                        }
                        if ($succEsCarbon->gte($adjustedEf)) {
                            $gap = $this->calendarService->workMinutesBetween($calendar, $adjustedEf, $succEsCarbon);
                        } else {
                            $gap = -1 * $this->calendarService->workMinutesBetween($calendar, $succEsCarbon, $adjustedEf);
                        }
                    } elseif ($depType === 'SS') {
                        $esAct = $act['early_start'] instanceof Carbon
                            ? $act['early_start']
                            : Carbon::parse($act['early_start']);
                        $adjustedEs = $esAct->copy();
                        if ($lag !== 0) {
                            $adjustedEs = $this->calendarService->addWorkMinutes($calendar, $adjustedEs, $lag);
                        }
                        if ($succEsCarbon->gte($adjustedEs)) {
                            $gap = $this->calendarService->workMinutesBetween($calendar, $adjustedEs, $succEsCarbon);
                        } else {
                            $gap = -1 * $this->calendarService->workMinutesBetween($calendar, $succEsCarbon, $adjustedEs);
                        }
                    } elseif ($depType === 'FF') {
                        $succEf = $activities[$succId]['early_finish'] ?? null;
                        if ($succEf) {
                            $succEfCarbon = $succEf instanceof Carbon ? $succEf : Carbon::parse($succEf);
                            $adjustedEf2 = $efCarbon->copy();
                            if ($lag !== 0) {
                                $adjustedEf2 = $this->calendarService->addWorkMinutes($calendar, $adjustedEf2, $lag);
                            }
                            if ($succEfCarbon->gte($adjustedEf2)) {
                                $gap = $this->calendarService->workMinutesBetween($calendar, $adjustedEf2, $succEfCarbon);
                            } else {
                                $gap = -1 * $this->calendarService->workMinutesBetween($calendar, $succEfCarbon, $adjustedEf2);
                            }
                        }
                    } elseif ($depType === 'SF') {
                        $esAct = $act['early_start'] instanceof Carbon
                            ? $act['early_start']
                            : Carbon::parse($act['early_start']);
                        $succEf2 = $activities[$succId]['early_finish'] ?? null;
                        if ($succEf2) {
                            $succEfCarbon2 = $succEf2 instanceof Carbon ? $succEf2 : Carbon::parse($succEf2);
                            $adjustedEs2 = $esAct->copy();
                            if ($lag !== 0) {
                                $adjustedEs2 = $this->calendarService->addWorkMinutes($calendar, $adjustedEs2, $lag);
                            }
                            if ($succEfCarbon2->gte($adjustedEs2)) {
                                $gap = $this->calendarService->workMinutesBetween($calendar, $adjustedEs2, $succEfCarbon2);
                            } else {
                                $gap = -1 * $this->calendarService->workMinutesBetween($calendar, $succEfCarbon2, $adjustedEs2);
                            }
                        }
                    }

                    if ($minGap === null || $gap < $minGap) {
                        $minGap = $gap;
                    }
                }

                $act['free_float_minutes'] = $minGap ?? 0;
            } else {
                // No successors -> free float = total float
                $act['free_float_minutes'] = $act['total_float_minutes'];
            }

            // -------------------------------------------------------
            // Critical flag
            // -------------------------------------------------------
            $act['is_critical'] = ($act['total_float_minutes'] <= 0) ? 1 : 0;
        }

        unset($act);
    }

    // =================================================================
    //  PERSIST RESULTS
    // =================================================================

    /**
     * Persist schedule results to database.
     *
     * Creates a ScheduleRun record and per-activity ScheduleRunActivity
     * records. Also updates the activity table's computed columns.
     */
    protected function persistResults(
        int $projectId,
        ?int $scenarioId,
        array $activities,
        array $violations,
        Carbon $projectFinish,
        int $computationMs
    ): ScheduleRun {
        $criticalCount = 0;
        $nearCriticalCount = 0;
        $totalActivities = count($activities);

        foreach ($activities as $act) {
            if (($act['is_critical'] ?? 0) == 1) {
                $criticalCount++;
            } elseif (
                isset($act['total_float_minutes'])
                && $act['total_float_minutes'] > 0
                && $act['total_float_minutes'] <= $this->nearCriticalThresholdMinutes
            ) {
                $nearCriticalCount++;
            }
        }

        // Health summary for storage
        $healthSummary = [
            'total_activities'    => $totalActivities,
            'critical_count'      => $criticalCount,
            'near_critical_count' => $nearCriticalCount,
            'violation_count'     => count($violations),
        ];

        DB::beginTransaction();

        try {
            // Create the run record
            $run = new ScheduleRun();
            $run->run_project_id      = $projectId;
            $run->run_scenario_id     = $scenarioId;
            $run->run_progress_date   = $this->resolveProjectStart(Project::find($projectId));
            $run->run_project_finish  = $projectFinish;
            $run->run_total_activities     = $totalActivities;
            $run->run_critical_count       = $criticalCount;
            $run->run_near_critical_count  = $nearCriticalCount;
            $run->run_violations      = $violations;
            $run->run_health_summary  = $healthSummary;
            $run->run_status          = 'completed';
            $run->run_computation_ms  = $computationMs;
            $run->run_created_by      = auth()->id();
            $run->run_created_at      = Carbon::now();
            $run->save();

            // Create per-activity run records and update activity table
            foreach ($activities as $actId => $act) {
                $esStr = $this->carbonToDateTimeString($act['early_start'] ?? null);
                $efStr = $this->carbonToDateTimeString($act['early_finish'] ?? null);
                $lsStr = $this->carbonToDateTimeString($act['late_start'] ?? null);
                $lfStr = $this->carbonToDateTimeString($act['late_finish'] ?? null);

                // ScheduleRunActivity record
                DB::table('schedule_run_activities')->insert([
                    'ra_run_id'                 => $run->run_id,
                    'ra_activity_id'            => $actId,
                    'ra_early_start'            => $esStr,
                    'ra_early_finish'           => $efStr,
                    'ra_late_start'             => $lsStr,
                    'ra_late_finish'            => $lfStr,
                    'ra_total_float_minutes'    => $act['total_float_minutes'] ?? null,
                    'ra_free_float_minutes'     => $act['free_float_minutes'] ?? null,
                    'ra_is_critical'            => $act['is_critical'] ?? 0,
                    'ra_driving_predecessor_id' => $act['driving_predecessor_id'] ?? null,
                    'ra_driving_constraint_id'  => $act['driving_constraint_id'] ?? null,
                ]);

                // Update the activity table's computed columns
                DB::table('schedule_activities')
                    ->where('act_id', $actId)
                    ->update([
                        'act_early_start'            => $esStr,
                        'act_early_finish'           => $efStr,
                        'act_late_start'             => $lsStr,
                        'act_late_finish'            => $lfStr,
                        'act_total_float_minutes'    => $act['total_float_minutes'] ?? null,
                        'act_free_float_minutes'     => $act['free_float_minutes'] ?? null,
                        'act_is_critical'            => $act['is_critical'] ?? 0,
                        'act_driving_predecessor_id' => $act['driving_predecessor_id'] ?? null,
                        'act_driving_constraint_id'  => $act['driving_constraint_id'] ?? null,
                        'act_modifydate'             => Carbon::now(),
                    ]);
            }

            DB::commit();

            return $run;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // =================================================================
    //  HEALTH GRADE
    // =================================================================

    /**
     * Compute a letter grade (A-F) for schedule health.
     *
     * Scoring rules (out of 100):
     *  - Start at 100
     *  - Deduct 3 per overdue activity (max 30)
     *  - Deduct 1 per near-critical activity (max 15)
     *  - Deduct 2 per critical activity above 10% of total (max 20)
     *  - Deduct based on completion shortfall vs linear expectation (max 20)
     *  - If zero total activities, return 'N/A'
     *
     * Grade thresholds: A >= 90, B >= 80, C >= 70, D >= 60, F < 60
     */
    protected function computeHealthGrade(
        int $total,
        int $criticalCount,
        int $nearCritical,
        int $overdue,
        float $completionPct
    ): string {
        if ($total === 0) {
            return 'N/A';
        }

        $score = 100;

        // Overdue penalty
        $overdueDeduct = min($overdue * 3, 30);
        $score -= $overdueDeduct;

        // Near-critical penalty
        $nearCritDeduct = min($nearCritical * 1, 15);
        $score -= $nearCritDeduct;

        // Critical density penalty (if more than 10% of activities are critical)
        $criticalPct = ($criticalCount / $total) * 100;
        if ($criticalPct > 10) {
            $excessCritical = $criticalCount - (int) ceil($total * 0.10);
            $critDeduct = min($excessCritical * 2, 20);
            $score -= $critDeduct;
        }

        // Completion shortfall penalty (mild; for context)
        // If less than 50% complete and schedule is well underway, penalize
        if ($completionPct < 50 && $overdue > 0) {
            $score -= min(15, (int) ((50 - $completionPct) * 0.3));
        }

        $score = max(0, $score);

        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    // =================================================================
    //  UTILITY HELPERS
    // =================================================================

    /**
     * Safely convert a Carbon instance or date string to a datetime string.
     */
    protected function carbonToDateTimeString($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_string($value)) {
            try {
                return Carbon::parse($value)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}
