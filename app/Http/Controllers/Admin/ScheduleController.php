<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Scheduling\CpmScheduleService;
use App\Models\{
    Project,
    ScheduleCalendar,
    ScheduleCalendarException,
    ScheduleActivity,
    ScheduleDependency,
    ScheduleDriver,
    ScheduleConstraintLog,
    ScheduleRun,
    ScheduleBaseline,
    ScheduleWbsNode,
    ScheduleActivityActual,
    ScheduleScenario
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    protected CpmScheduleService $cpmService;

    public function __construct(CpmScheduleService $cpmService)
    {
        $this->cpmService = $cpmService;
    }

    // ──────────────────────────────────────────────────────────────────
    //  Helpers
    // ──────────────────────────────────────────────────────────────────

    /**
     * Load a project and verify it belongs to the current company.
     *
     * @param  int  $projectId
     * @return Project
     */
    protected function loadProject(int $projectId): Project
    {
        $project = Project::find($projectId);

        abort_unless($project && $project->company_id == session('company_id'), 403,
            'Project not found or does not belong to your company.');

        return $project;
    }

    // ================================================================
    //  Schedule Dashboard
    // ================================================================

    /**
     * GET /schedule
     * List all projects that have schedules (or can have schedules).
     */
    public function index()
    {
        $companyId = session('company_id');

        $projects = Project::where('company_id', $companyId)
            ->where('proj_status', 1)
            ->orderBy('proj_name')
            ->get();

        // Gather schedule metrics per project
        $projectData = [];
        foreach ($projects as $project) {
            $activityCount = ScheduleActivity::where('act_project_id', $project->proj_id)->count();

            $latestRun = ScheduleRun::where('run_project_id', $project->proj_id)
                ->orderByDesc('run_created_at')
                ->first();

            $healthGrade = null;
            $lastRunDate = null;
            $status = 'No Schedule';

            if ($latestRun) {
                $healthSummary = $latestRun->run_health_summary;
                $healthGrade = is_array($healthSummary) ? ($healthSummary['health_grade'] ?? null) : null;
                $lastRunDate = $latestRun->run_created_at;
                $status = 'Scheduled';
            }

            if ($activityCount === 0) {
                $status = 'No Activities';
            }

            // If no grade in run summary, compute it live
            if ($activityCount > 0 && $healthGrade === null) {
                $summary = $this->cpmService->getHealthSummary($project->proj_id);
                $healthGrade = $summary['health_grade'] ?? 'N/A';
            }

            $projectData[] = [
                'project'        => $project,
                'activity_count' => $activityCount,
                'last_run_date'  => $lastRunDate,
                'health_grade'   => $healthGrade ?? 'N/A',
                'status'         => $status,
            ];
        }

        return view('admin.schedule.index', compact('projectData'));
    }

    /**
     * GET /schedule/{projectId}
     * Main schedule view for a project.
     */
    public function show($projectId)
    {
        $project = $this->loadProject($projectId);

        $activities = ScheduleActivity::where('act_project_id', $projectId)
            ->with(['predecessorDeps', 'successorDeps', 'actuals', 'wbsNode', 'calendar'])
            ->orderBy('act_sort_order')
            ->get();

        $dependencies = ScheduleDependency::where('dep_project_id', $projectId)->get();

        $calendars = ScheduleCalendar::where(function ($q) use ($projectId) {
            $q->where('cal_project_id', $projectId)
              ->orWhereNull('cal_project_id');
        })
        ->where('cal_status', 1)
        ->get();

        $latestRun = ScheduleRun::where('run_project_id', $projectId)
            ->orderByDesc('run_created_at')
            ->first();

        $healthSummary = $this->cpmService->getHealthSummary($projectId);

        $wbsNodes = ScheduleWbsNode::where('wbs_project_id', $projectId)
            ->orderBy('wbs_sort_order')
            ->get();

        $baselines = ScheduleBaseline::where('bl_project_id', $projectId)
            ->orderByDesc('bl_created_at')
            ->get();

        // Prepare JSON data for Gantt chart JS
        $ganttJson = $this->buildGanttJson($project, $activities, $dependencies, $healthSummary);

        return view('admin.schedule.show', compact(
            'project',
            'activities',
            'dependencies',
            'calendars',
            'latestRun',
            'healthSummary',
            'wbsNodes',
            'baselines',
            'ganttJson'
        ));
    }

    // ================================================================
    //  Activities CRUD
    // ================================================================

    /**
     * POST /schedule/{projectId}/activities
     */
    public function storeActivity(Request $request, $projectId)
    {
        $project = $this->loadProject($projectId);

        $request->validate([
            'act_name'            => 'required|string|max:255',
            'act_duration_minutes' => 'required|integer|min:0',
            'act_type'            => 'sometimes|in:TASK,MILESTONE,SUMMARY',
            'act_constraint_type' => 'sometimes|in:NONE,SNET,FNLT,MSO,MFO',
            'act_constraint_date' => 'nullable|date',
            'act_description'     => 'nullable|string|max:1000',
            'act_calendar_id'     => 'nullable|integer',
            'act_wbs_id'          => 'nullable|integer',
            'act_priority'        => 'nullable|integer|min:0',
            'act_color'           => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            $maxSort = ScheduleActivity::where('act_project_id', $projectId)
                ->max('act_sort_order') ?? 0;

            $activity = ScheduleActivity::create([
                'act_project_id'      => $projectId,
                'act_name'            => $request->act_name,
                'act_description'     => $request->act_description,
                'act_type'            => $request->act_type ?? ScheduleActivity::TYPE_TASK,
                'act_duration_minutes' => $request->act_duration_minutes,
                'act_calendar_id'     => $request->act_calendar_id,
                'act_wbs_id'          => $request->act_wbs_id,
                'act_status'          => ScheduleActivity::STATUS_NOT_STARTED,
                'act_percent_complete' => 0,
                'act_is_locked'       => 0,
                'act_priority'        => $request->act_priority ?? 0,
                'act_constraint_type' => $request->act_constraint_type ?? ScheduleActivity::CONSTRAINT_NONE,
                'act_constraint_date' => $request->act_constraint_date,
                'act_is_critical'     => 0,
                'act_sort_order'      => $maxSort + 1,
                'act_color'           => $request->act_color,
                'act_createby'        => auth()->id(),
                'act_createdate'      => Carbon::now(),
                'company_id'          => session('company_id'),
            ]);

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'Activity created successfully.',
                'activity' => $activity,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ScheduleController::storeActivity failed', [
                'project_id' => $projectId,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create activity.',
            ], 500);
        }
    }

    /**
     * PUT /schedule/{projectId}/activities/{activityId}
     */
    public function updateActivity(Request $request, $projectId, $activityId)
    {
        $project = $this->loadProject($projectId);

        $request->validate([
            'act_name'            => 'sometimes|required|string|max:255',
            'act_duration_minutes' => 'sometimes|required|integer|min:0',
            'act_type'            => 'sometimes|in:TASK,MILESTONE,SUMMARY',
            'act_constraint_type' => 'sometimes|in:NONE,SNET,FNLT,MSO,MFO',
            'act_constraint_date' => 'nullable|date',
            'act_description'     => 'nullable|string|max:1000',
            'act_calendar_id'     => 'nullable|integer',
            'act_wbs_id'          => 'nullable|integer',
            'act_priority'        => 'nullable|integer|min:0',
            'act_color'           => 'nullable|string|max:20',
            'act_status'          => 'sometimes|in:NOT_STARTED,IN_PROGRESS,COMPLETE,BLOCKED',
            'act_percent_complete' => 'sometimes|numeric|min:0|max:100',
        ]);

        $activity = ScheduleActivity::where('act_id', $activityId)
            ->where('act_project_id', $projectId)
            ->first();

        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Activity not found.',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $fillable = [
                'act_name', 'act_description', 'act_type', 'act_duration_minutes',
                'act_calendar_id', 'act_wbs_id', 'act_status', 'act_percent_complete',
                'act_priority', 'act_constraint_type', 'act_constraint_date', 'act_color',
            ];

            foreach ($fillable as $field) {
                if ($request->has($field)) {
                    $activity->{$field} = $request->{$field};
                }
            }

            $activity->act_modifyby = auth()->id();
            $activity->act_modifydate = Carbon::now();
            $activity->save();

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'Activity updated successfully.',
                'activity' => $activity,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ScheduleController::updateActivity failed', [
                'project_id'  => $projectId,
                'activity_id' => $activityId,
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update activity.',
            ], 500);
        }
    }

    /**
     * DELETE /schedule/{projectId}/activities/{activityId}
     */
    public function deleteActivity($projectId, $activityId)
    {
        $project = $this->loadProject($projectId);

        $activity = ScheduleActivity::where('act_id', $activityId)
            ->where('act_project_id', $projectId)
            ->first();

        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Activity not found.',
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Delete related dependencies (where this activity is predecessor or successor)
            ScheduleDependency::where('dep_project_id', $projectId)
                ->where(function ($q) use ($activityId) {
                    $q->where('dep_predecessor_id', $activityId)
                      ->orWhere('dep_successor_id', $activityId);
                })
                ->delete();

            // Delete related actuals
            ScheduleActivityActual::where('aca_activity_id', $activityId)->delete();

            // Delete related drivers
            ScheduleDriver::where('drv_activity_id', $activityId)->delete();

            // Delete related constraint logs
            ScheduleConstraintLog::where('cl_activity_id', $activityId)->delete();

            $activity->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Activity and related records deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ScheduleController::deleteActivity failed', [
                'project_id'  => $projectId,
                'activity_id' => $activityId,
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete activity.',
            ], 500);
        }
    }

    /**
     * POST /schedule/{projectId}/activities/reorder
     */
    public function reorderActivities(Request $request, $projectId)
    {
        $project = $this->loadProject($projectId);

        $request->validate([
            'order'              => 'required|array|min:1',
            'order.*.id'         => 'required|integer',
            'order.*.sort_order' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->order as $item) {
                ScheduleActivity::where('act_id', $item['id'])
                    ->where('act_project_id', $projectId)
                    ->update([
                        'act_sort_order' => $item['sort_order'],
                        'act_modifyby'   => auth()->id(),
                        'act_modifydate' => Carbon::now(),
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Activities reordered successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ScheduleController::reorderActivities failed', [
                'project_id' => $projectId,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder activities.',
            ], 500);
        }
    }

    // ================================================================
    //  Dependencies CRUD
    // ================================================================

    /**
     * POST /schedule/{projectId}/dependencies
     */
    public function storeDependency(Request $request, $projectId)
    {
        $project = $this->loadProject($projectId);

        $request->validate([
            'predecessor_id' => 'required|integer',
            'successor_id'   => 'required|integer|different:predecessor_id',
            'type'           => 'sometimes|in:FS,SS,FF,SF',
            'lag_minutes'    => 'sometimes|integer',
        ]);

        // Verify both activities exist in this project
        $predExists = ScheduleActivity::where('act_id', $request->predecessor_id)
            ->where('act_project_id', $projectId)
            ->exists();

        $succExists = ScheduleActivity::where('act_id', $request->successor_id)
            ->where('act_project_id', $projectId)
            ->exists();

        if (!$predExists || !$succExists) {
            return response()->json([
                'success' => false,
                'message' => 'Predecessor or successor activity not found in this project.',
            ], 422);
        }

        // Check for duplicate dependency
        $duplicate = ScheduleDependency::where('dep_project_id', $projectId)
            ->where('dep_predecessor_id', $request->predecessor_id)
            ->where('dep_successor_id', $request->successor_id)
            ->exists();

        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => 'This dependency already exists.',
            ], 422);
        }

        // Check for potential cycles by temporarily adding the dependency
        // and running a topological sort check
        $allDeps = ScheduleDependency::where('dep_project_id', $projectId)->get()->toArray();
        $allDeps[] = [
            'dep_predecessor_id' => $request->predecessor_id,
            'dep_successor_id'   => $request->successor_id,
        ];

        $activityIds = ScheduleActivity::where('act_project_id', $projectId)
            ->pluck('act_id')
            ->toArray();

        if ($this->hasCycle($activityIds, $allDeps)) {
            return response()->json([
                'success' => false,
                'message' => 'Adding this dependency would create a circular reference.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $dependency = ScheduleDependency::create([
                'dep_project_id'     => $projectId,
                'dep_predecessor_id' => $request->predecessor_id,
                'dep_successor_id'   => $request->successor_id,
                'dep_type'           => $request->type ?? ScheduleDependency::TYPE_FS,
                'dep_lag_minutes'    => $request->lag_minutes ?? 0,
                'company_id'         => session('company_id'),
            ]);

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => 'Dependency created successfully.',
                'dependency' => $dependency,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ScheduleController::storeDependency failed', [
                'project_id' => $projectId,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create dependency.',
            ], 500);
        }
    }

    /**
     * DELETE /schedule/{projectId}/dependencies/{depId}
     */
    public function deleteDependency($projectId, $depId)
    {
        $project = $this->loadProject($projectId);

        $dependency = ScheduleDependency::where('dep_id', $depId)
            ->where('dep_project_id', $projectId)
            ->first();

        if (!$dependency) {
            return response()->json([
                'success' => false,
                'message' => 'Dependency not found.',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $dependency->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dependency deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ScheduleController::deleteDependency failed', [
                'project_id' => $projectId,
                'dep_id'     => $depId,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete dependency.',
            ], 500);
        }
    }

    // ================================================================
    //  Schedule Calculation
    // ================================================================

    /**
     * POST /schedule/{projectId}/calculate
     */
    public function calculate($projectId)
    {
        $project = $this->loadProject($projectId);

        try {
            $result = $this->cpmService->calculateSchedule($projectId);

            if (!$result['success']) {
                return response()->json([
                    'success'    => false,
                    'message'    => $result['error'] ?? 'Schedule calculation failed.',
                    'violations' => $result['violations'] ?? [],
                ], 422);
            }

            $run = $result['run'];
            $healthSummary = $this->cpmService->getHealthSummary($projectId);

            return response()->json([
                'success' => true,
                'message' => 'Schedule calculated successfully.',
                'run'     => [
                    'run_id'               => $run->run_id,
                    'run_project_finish'   => $run->run_project_finish ? Carbon::parse($run->run_project_finish)->toIso8601String() : null,
                    'run_total_activities' => $run->run_total_activities,
                    'run_critical_count'   => $run->run_critical_count,
                    'run_near_critical_count' => $run->run_near_critical_count,
                    'run_computation_ms'   => $run->run_computation_ms,
                    'run_created_at'       => $run->run_created_at ? Carbon::parse($run->run_created_at)->toIso8601String() : null,
                ],
                'violations'     => $result['violations'] ?? [],
                'health_summary' => $healthSummary,
            ]);
        } catch (\Throwable $e) {
            Log::error('ScheduleController::calculate failed', [
                'project_id' => $projectId,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during schedule calculation.',
            ], 500);
        }
    }

    /**
     * GET /schedule/{projectId}/critical-path
     */
    public function criticalPath($projectId)
    {
        $project = $this->loadProject($projectId);

        try {
            $path = $this->cpmService->getCriticalPath($projectId);

            return response()->json([
                'success'       => true,
                'critical_path' => $path,
                'count'         => count($path),
            ]);
        } catch (\Throwable $e) {
            Log::error('ScheduleController::criticalPath failed', [
                'project_id' => $projectId,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve critical path.',
            ], 500);
        }
    }

    /**
     * GET /schedule/{projectId}/health
     */
    public function health($projectId)
    {
        $project = $this->loadProject($projectId);

        try {
            $summary = $this->cpmService->getHealthSummary($projectId);

            return response()->json([
                'success' => true,
                'health'  => $summary,
            ]);
        } catch (\Throwable $e) {
            Log::error('ScheduleController::health failed', [
                'project_id' => $projectId,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve health summary.',
            ], 500);
        }
    }

    // ================================================================
    //  Gantt Data API
    // ================================================================

    /**
     * GET /schedule/{projectId}/gantt-data
     */
    public function ganttData($projectId)
    {
        $project = $this->loadProject($projectId);

        try {
            $activities = ScheduleActivity::where('act_project_id', $projectId)
                ->with(['predecessorDeps', 'successorDeps', 'actuals', 'wbsNode', 'calendar'])
                ->orderBy('act_sort_order')
                ->get();

            $dependencies = ScheduleDependency::where('dep_project_id', $projectId)->get();

            $healthSummary = $this->cpmService->getHealthSummary($projectId);

            $data = $this->buildGanttJson($project, $activities, $dependencies, $healthSummary);

            return response()->json($data);
        } catch (\Throwable $e) {
            Log::error('ScheduleController::ganttData failed', [
                'project_id' => $projectId,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load Gantt data.',
            ], 500);
        }
    }

    // ================================================================
    //  Actuals / Progress
    // ================================================================

    /**
     * POST /schedule/{projectId}/activities/{activityId}/actuals
     */
    public function updateActuals(Request $request, $projectId, $activityId)
    {
        $project = $this->loadProject($projectId);

        $request->validate([
            'actual_start'               => 'nullable|date',
            'actual_finish'              => 'nullable|date',
            'remaining_duration_minutes' => 'nullable|integer|min:0',
            'note'                       => 'nullable|string|max:1000',
        ]);

        $activity = ScheduleActivity::where('act_id', $activityId)
            ->where('act_project_id', $projectId)
            ->first();

        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Activity not found.',
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Update or create actuals record
            $actuals = ScheduleActivityActual::where('aca_activity_id', $activityId)->first();

            $actualsData = [
                'aca_activity_id'              => $activityId,
                'aca_actual_start'             => $request->actual_start,
                'aca_actual_finish'            => $request->actual_finish,
                'aca_remaining_duration_minutes' => $request->remaining_duration_minutes,
                'aca_note'                     => $request->note,
                'aca_updated_by'               => auth()->id(),
                'aca_updated_at'               => Carbon::now(),
            ];

            if ($actuals) {
                $actuals->update($actualsData);
            } else {
                $actuals = ScheduleActivityActual::create($actualsData);
            }

            // Update activity status based on actuals
            $newStatus = $activity->act_status;
            $newPercentComplete = $activity->act_percent_complete;

            if ($request->actual_finish) {
                $newStatus = ScheduleActivity::STATUS_COMPLETE;
                $newPercentComplete = 100;
            } elseif ($request->actual_start) {
                $newStatus = ScheduleActivity::STATUS_IN_PROGRESS;
            }

            $activity->update([
                'act_status'           => $newStatus,
                'act_percent_complete' => $newPercentComplete,
                'act_modifyby'         => auth()->id(),
                'act_modifydate'       => Carbon::now(),
            ]);

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'Actuals updated successfully.',
                'actuals'  => $actuals,
                'activity' => $activity->fresh(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ScheduleController::updateActuals failed', [
                'project_id'  => $projectId,
                'activity_id' => $activityId,
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update actuals.',
            ], 500);
        }
    }

    // ================================================================
    //  Calendars
    // ================================================================

    /**
     * GET /schedule/calendars
     */
    public function calendars()
    {
        $companyId = session('company_id');

        $calendars = ScheduleCalendar::where('company_id', $companyId)
            ->with('exceptions')
            ->orderBy('cal_name')
            ->get();

        $projects = Project::where('company_id', $companyId)
            ->where('proj_status', 1)
            ->orderBy('proj_name')
            ->get();

        return view('admin.schedule.calendars', compact('calendars', 'projects'));
    }

    /**
     * POST /schedule/calendars
     */
    public function storeCalendar(Request $request)
    {
        $request->validate([
            'cal_name'       => 'required|string|max:100',
            'cal_work_week'  => 'required|string|max:100',
            'cal_work_start' => 'required|string|max:10',
            'cal_work_end'   => 'required|string|max:10',
            'cal_timezone'   => 'sometimes|string|max:50',
            'cal_project_id' => 'nullable|integer',
            'cal_is_default' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();
        try {
            // If setting as default, clear any existing defaults for same scope
            if ($request->cal_is_default) {
                $query = ScheduleCalendar::where('company_id', session('company_id'));
                if ($request->cal_project_id) {
                    $query->where('cal_project_id', $request->cal_project_id);
                } else {
                    $query->whereNull('cal_project_id');
                }
                $query->update(['cal_is_default' => 0]);
            }

            $calendar = ScheduleCalendar::create([
                'cal_name'       => $request->cal_name,
                'cal_work_week'  => $request->cal_work_week,
                'cal_work_start' => $request->cal_work_start,
                'cal_work_end'   => $request->cal_work_end,
                'cal_timezone'   => $request->cal_timezone ?? 'America/New_York',
                'cal_project_id' => $request->cal_project_id,
                'cal_is_default' => $request->cal_is_default ?? 0,
                'cal_status'     => 1,
                'cal_createby'   => auth()->id(),
                'cal_createdate' => Carbon::now(),
                'company_id'     => session('company_id'),
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success'  => true,
                    'message'  => 'Calendar created successfully.',
                    'calendar' => $calendar,
                ], 201);
            }

            return redirect()->route('admin.schedule.calendars')
                ->with('success', 'Calendar created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ScheduleController::storeCalendar failed', [
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create calendar.',
                ], 500);
            }

            return redirect()->route('admin.schedule.calendars')
                ->with('error', 'Failed to create calendar.');
        }
    }

    /**
     * PUT /schedule/calendars/{calId}
     */
    public function updateCalendar(Request $request, $calId)
    {
        $calendar = ScheduleCalendar::where('cal_id', $calId)
            ->where('company_id', session('company_id'))
            ->first();

        if (!$calendar) {
            return response()->json([
                'success' => false,
                'message' => 'Calendar not found.',
            ], 404);
        }

        $request->validate([
            'cal_name'       => 'sometimes|required|string|max:100',
            'cal_work_week'  => 'sometimes|required|string|max:100',
            'cal_work_start' => 'sometimes|required|string|max:10',
            'cal_work_end'   => 'sometimes|required|string|max:10',
            'cal_timezone'   => 'sometimes|string|max:50',
            'cal_is_default' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();
        try {
            // If setting as default, clear any existing defaults for same scope
            if ($request->has('cal_is_default') && $request->cal_is_default) {
                $query = ScheduleCalendar::where('company_id', session('company_id'))
                    ->where('cal_id', '!=', $calId);
                if ($calendar->cal_project_id) {
                    $query->where('cal_project_id', $calendar->cal_project_id);
                } else {
                    $query->whereNull('cal_project_id');
                }
                $query->update(['cal_is_default' => 0]);
            }

            $fillable = ['cal_name', 'cal_work_week', 'cal_work_start', 'cal_work_end', 'cal_timezone', 'cal_is_default'];
            foreach ($fillable as $field) {
                if ($request->has($field)) {
                    $calendar->{$field} = $request->{$field};
                }
            }

            $calendar->cal_modifyby = auth()->id();
            $calendar->cal_modifydate = Carbon::now();
            $calendar->save();

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'Calendar updated successfully.',
                'calendar' => $calendar,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ScheduleController::updateCalendar failed', [
                'cal_id' => $calId,
                'error'  => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update calendar.',
            ], 500);
        }
    }

    /**
     * DELETE /schedule/calendars/{calId}
     */
    public function deleteCalendar($calId)
    {
        $calendar = ScheduleCalendar::where('cal_id', $calId)
            ->where('company_id', session('company_id'))
            ->first();

        if (!$calendar) {
            return response()->json([
                'success' => false,
                'message' => 'Calendar not found.',
            ], 404);
        }

        // Check if the calendar is in use by any activities
        $inUseCount = ScheduleActivity::where('act_calendar_id', $calId)->count();
        if ($inUseCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete calendar: it is assigned to {$inUseCount} activit" . ($inUseCount === 1 ? 'y' : 'ies') . '.',
            ], 422);
        }

        // Check if the calendar is set as a project default
        $projDefault = Project::where('proj_default_calendar_id', $calId)->count();
        if ($projDefault > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete calendar: it is set as a project default calendar.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Delete exceptions first
            ScheduleCalendarException::where('cex_calendar_id', $calId)->delete();

            $calendar->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Calendar deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ScheduleController::deleteCalendar failed', [
                'cal_id' => $calId,
                'error'  => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete calendar.',
            ], 500);
        }
    }

    /**
     * POST /schedule/calendars/{calId}/exceptions
     */
    public function storeCalendarException(Request $request, $calId)
    {
        $calendar = ScheduleCalendar::where('cal_id', $calId)
            ->where('company_id', session('company_id'))
            ->first();

        if (!$calendar) {
            return response()->json([
                'success' => false,
                'message' => 'Calendar not found.',
            ], 404);
        }

        $request->validate([
            'cex_date'       => 'required|date',
            'cex_type'       => 'required|in:holiday,shutdown,workday',
            'cex_name'       => 'required|string|max:100',
            'cex_work_start' => 'nullable|string|max:10',
            'cex_work_end'   => 'nullable|string|max:10',
        ]);

        try {
            $exception = ScheduleCalendarException::create([
                'cex_calendar_id' => $calId,
                'cex_date'        => $request->cex_date,
                'cex_type'        => $request->cex_type,
                'cex_name'        => $request->cex_name,
                'cex_work_start'  => $request->cex_work_start,
                'cex_work_end'    => $request->cex_work_end,
            ]);

            return response()->json([
                'success'   => true,
                'message'   => 'Calendar exception added successfully.',
                'exception' => $exception,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('ScheduleController::storeCalendarException failed', [
                'cal_id' => $calId,
                'error'  => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add calendar exception.',
            ], 500);
        }
    }

    /**
     * DELETE /schedule/calendars/{calId}/exceptions/{exId}
     */
    public function deleteCalendarException($calId, $exId)
    {
        $calendar = ScheduleCalendar::where('cal_id', $calId)
            ->where('company_id', session('company_id'))
            ->first();

        if (!$calendar) {
            return response()->json([
                'success' => false,
                'message' => 'Calendar not found.',
            ], 404);
        }

        $exception = ScheduleCalendarException::where('cex_id', $exId)
            ->where('cex_calendar_id', $calId)
            ->first();

        if (!$exception) {
            return response()->json([
                'success' => false,
                'message' => 'Calendar exception not found.',
            ], 404);
        }

        try {
            $exception->delete();

            return response()->json([
                'success' => true,
                'message' => 'Calendar exception deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('ScheduleController::deleteCalendarException failed', [
                'cal_id' => $calId,
                'ex_id'  => $exId,
                'error'  => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete calendar exception.',
            ], 500);
        }
    }

    // ================================================================
    //  Drivers / Constraints
    // ================================================================

    /**
     * POST /schedule/{projectId}/drivers
     */
    public function storeDriver(Request $request, $projectId)
    {
        $project = $this->loadProject($projectId);

        $request->validate([
            'drv_type'            => 'required|in:PERMIT,INSPECTION,PROCUREMENT,ACCESS_WINDOW,UTILITY_CUTOVER,OWNER_DECISION',
            'drv_name'            => 'required|string|max:255',
            'drv_activity_id'     => 'nullable|integer',
            'drv_wbs_id'          => 'nullable|integer',
            'drv_constraint_type' => 'sometimes|in:NONE,SNET,FNLT,MSO,MFO',
            'drv_constraint_date' => 'nullable|date',
            'drv_window_start'    => 'nullable|date',
            'drv_window_end'      => 'nullable|date',
            'drv_status'          => 'sometimes|in:OPEN,CLEARED,AT_RISK,FAILED',
            'drv_confidence'      => 'nullable|integer|min:0|max:100',
            'drv_evidence_link'   => 'nullable|string|max:500',
        ]);

        // Verify activity belongs to this project if provided
        if ($request->drv_activity_id) {
            $actExists = ScheduleActivity::where('act_id', $request->drv_activity_id)
                ->where('act_project_id', $projectId)
                ->exists();

            if (!$actExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Linked activity not found in this project.',
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $driver = ScheduleDriver::create([
                'drv_project_id'      => $projectId,
                'drv_type'            => $request->drv_type,
                'drv_name'            => $request->drv_name,
                'drv_activity_id'     => $request->drv_activity_id,
                'drv_wbs_id'          => $request->drv_wbs_id,
                'drv_constraint_type' => $request->drv_constraint_type ?? 'NONE',
                'drv_constraint_date' => $request->drv_constraint_date,
                'drv_window_start'    => $request->drv_window_start,
                'drv_window_end'      => $request->drv_window_end,
                'drv_status'          => $request->drv_status ?? ScheduleDriver::STATUS_OPEN,
                'drv_confidence'      => $request->drv_confidence,
                'drv_evidence_link'   => $request->drv_evidence_link,
                'drv_createby'        => auth()->id(),
                'drv_createdate'      => Carbon::now(),
                'company_id'          => session('company_id'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Driver created successfully.',
                'driver'  => $driver,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ScheduleController::storeDriver failed', [
                'project_id' => $projectId,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create driver.',
            ], 500);
        }
    }

    /**
     * PUT /schedule/{projectId}/drivers/{driverId}
     */
    public function updateDriver(Request $request, $projectId, $driverId)
    {
        $project = $this->loadProject($projectId);

        $driver = ScheduleDriver::where('drv_id', $driverId)
            ->where('drv_project_id', $projectId)
            ->first();

        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver not found.',
            ], 404);
        }

        $request->validate([
            'drv_type'            => 'sometimes|in:PERMIT,INSPECTION,PROCUREMENT,ACCESS_WINDOW,UTILITY_CUTOVER,OWNER_DECISION',
            'drv_name'            => 'sometimes|required|string|max:255',
            'drv_activity_id'     => 'nullable|integer',
            'drv_wbs_id'          => 'nullable|integer',
            'drv_constraint_type' => 'sometimes|in:NONE,SNET,FNLT,MSO,MFO',
            'drv_constraint_date' => 'nullable|date',
            'drv_window_start'    => 'nullable|date',
            'drv_window_end'      => 'nullable|date',
            'drv_status'          => 'sometimes|in:OPEN,CLEARED,AT_RISK,FAILED',
            'drv_confidence'      => 'nullable|integer|min:0|max:100',
            'drv_evidence_link'   => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $fillable = [
                'drv_type', 'drv_name', 'drv_activity_id', 'drv_wbs_id',
                'drv_constraint_type', 'drv_constraint_date', 'drv_window_start',
                'drv_window_end', 'drv_status', 'drv_confidence', 'drv_evidence_link',
            ];

            foreach ($fillable as $field) {
                if ($request->has($field)) {
                    $driver->{$field} = $request->{$field};
                }
            }

            $driver->drv_modifyby = auth()->id();
            $driver->drv_modifydate = Carbon::now();
            $driver->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Driver updated successfully.',
                'driver'  => $driver,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ScheduleController::updateDriver failed', [
                'project_id' => $projectId,
                'driver_id'  => $driverId,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update driver.',
            ], 500);
        }
    }

    /**
     * DELETE /schedule/{projectId}/drivers/{driverId}
     */
    public function deleteDriver($projectId, $driverId)
    {
        $project = $this->loadProject($projectId);

        $driver = ScheduleDriver::where('drv_id', $driverId)
            ->where('drv_project_id', $projectId)
            ->first();

        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver not found.',
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Delete related constraint logs
            ScheduleConstraintLog::where('cl_driver_id', $driverId)->delete();

            $driver->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Driver deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ScheduleController::deleteDriver failed', [
                'project_id' => $projectId,
                'driver_id'  => $driverId,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete driver.',
            ], 500);
        }
    }

    // ================================================================
    //  Lookahead
    // ================================================================

    /**
     * GET /schedule/{projectId}/lookahead
     */
    public function lookahead(Request $request, $projectId)
    {
        $project = $this->loadProject($projectId);

        $weeks = (int) $request->get('weeks', 3);
        $weeks = max(1, min($weeks, 12)); // clamp between 1 and 12

        $from = Carbon::now()->startOfDay();
        $to = $from->copy()->addWeeks($weeks)->endOfDay();

        $lookaheadData = $this->cpmService->getLookahead($projectId, $from, $to);
        $constraintLog = $this->cpmService->getConstraintLog($projectId, $from, $to);

        return view('admin.schedule.lookahead', compact(
            'project',
            'lookaheadData',
            'constraintLog',
            'weeks',
            'from',
            'to'
        ));
    }

    // ================================================================
    //  Baselines
    // ================================================================

    /**
     * POST /schedule/{projectId}/baselines
     */
    public function createBaseline(Request $request, $projectId)
    {
        $project = $this->loadProject($projectId);

        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        try {
            $result = $this->cpmService->createBaseline($projectId, $request->name);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Failed to create baseline.',
                ], 500);
            }

            $baseline = $result['baseline'];

            return response()->json([
                'success'  => true,
                'message'  => 'Baseline created successfully.',
                'baseline' => [
                    'bl_id'         => $baseline->bl_id,
                    'bl_name'       => $baseline->bl_name,
                    'bl_created_at' => $baseline->bl_created_at ? Carbon::parse($baseline->bl_created_at)->toIso8601String() : null,
                ],
            ], 201);
        } catch (\Throwable $e) {
            Log::error('ScheduleController::createBaseline failed', [
                'project_id' => $projectId,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create baseline.',
            ], 500);
        }
    }

    /**
     * GET /schedule/{projectId}/baselines/{baselineId}/variance
     */
    public function baselineVariance($projectId, $baselineId)
    {
        $project = $this->loadProject($projectId);

        try {
            $result = $this->cpmService->getBaselineVariance($projectId, (int) $baselineId);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Failed to compute baseline variance.',
                ], 404);
            }

            return response()->json([
                'success'    => true,
                'baseline'   => $result['baseline'],
                'activities' => $result['activities'],
            ]);
        } catch (\Throwable $e) {
            Log::error('ScheduleController::baselineVariance failed', [
                'project_id'  => $projectId,
                'baseline_id' => $baselineId,
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve baseline variance.',
            ], 500);
        }
    }

    // ================================================================
    //  Project Schedule Settings
    // ================================================================

    /**
     * PUT /schedule/{projectId}/settings
     */
    public function updateSettings(Request $request, $projectId)
    {
        $project = $this->loadProject($projectId);

        $request->validate([
            'scheduling_mode'       => 'sometimes|in:forward,backward,retained_logic',
            'default_calendar_id'   => 'nullable|integer',
            'progress_date'         => 'nullable|date',
            'target_finish_date'    => 'nullable|date',
        ]);

        // Verify calendar belongs to this company if provided
        if ($request->filled('default_calendar_id')) {
            $calExists = ScheduleCalendar::where('cal_id', $request->default_calendar_id)
                ->where('company_id', session('company_id'))
                ->where('cal_status', 1)
                ->exists();

            if (!$calExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected calendar not found or inactive.',
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            if ($request->has('scheduling_mode')) {
                $project->proj_scheduling_mode = $request->scheduling_mode;
            }
            if ($request->has('default_calendar_id')) {
                $project->proj_default_calendar_id = $request->default_calendar_id;
            }
            if ($request->has('progress_date')) {
                $project->proj_progress_date = $request->progress_date;
            }
            if ($request->has('target_finish_date')) {
                $project->proj_target_finish_date = $request->target_finish_date;
            }

            $project->proj_modifyby = auth()->id();
            $project->proj_modifydate = Carbon::now();
            $project->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Schedule settings updated successfully.',
                'project' => [
                    'proj_scheduling_mode'       => $project->proj_scheduling_mode,
                    'proj_default_calendar_id'   => $project->proj_default_calendar_id,
                    'proj_progress_date'         => $project->proj_progress_date ? Carbon::parse($project->proj_progress_date)->toDateString() : null,
                    'proj_target_finish_date'    => $project->proj_target_finish_date ? Carbon::parse($project->proj_target_finish_date)->toDateString() : null,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ScheduleController::updateSettings failed', [
                'project_id' => $projectId,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update schedule settings.',
            ], 500);
        }
    }

    // ================================================================
    //  Private Helpers
    // ================================================================

    /**
     * Build the JSON data structure consumed by the Gantt chart JS.
     *
     * @param  Project     $project
     * @param  \Illuminate\Support\Collection  $activities
     * @param  \Illuminate\Support\Collection  $dependencies
     * @param  array       $healthSummary
     * @return array
     */
    protected function buildGanttJson(Project $project, $activities, $dependencies, array $healthSummary): array
    {
        // Index dependencies by activity for quick lookup
        $depsBySuccessor = $dependencies->groupBy('dep_successor_id');

        $ganttActivities = [];

        foreach ($activities as $activity) {
            // Build dependency list for this activity
            $actDeps = [];
            $predDeps = $depsBySuccessor->get($activity->act_id, collect());

            foreach ($predDeps as $dep) {
                $actDeps[] = [
                    'from' => $dep->dep_predecessor_id,
                    'to'   => $dep->dep_successor_id,
                    'type' => $dep->dep_type,
                    'lag'  => $dep->dep_lag_minutes,
                ];
            }

            $durationDays = $activity->getDurationDays();

            $ganttActivities[] = [
                'id'              => $activity->act_id,
                'name'            => $activity->act_name,
                'start'           => $activity->act_early_start ? Carbon::parse($activity->act_early_start)->toIso8601String() : null,
                'end'             => $activity->act_early_finish ? Carbon::parse($activity->act_early_finish)->toIso8601String() : null,
                'duration_days'   => $durationDays,
                'duration_minutes' => $activity->act_duration_minutes,
                'progress'        => (float) $activity->act_percent_complete,
                'type'            => $activity->act_type,
                'is_critical'     => (bool) $activity->act_is_critical,
                'status'          => $activity->act_status,
                'wbs'             => $activity->wbsNode ? $activity->wbsNode->wbs_code : null,
                'wbs_name'        => $activity->wbsNode ? $activity->wbsNode->wbs_name : null,
                'dependencies'    => $actDeps,
                'float_days'      => $activity->act_total_float_minutes !== null
                    ? round($activity->act_total_float_minutes / max($activity->calendar ? $activity->calendar->getWorkMinutesPerDay() : 510, 1), 2)
                    : null,
                'float_minutes'   => $activity->act_total_float_minutes,
                'constraint_type' => $activity->act_constraint_type,
                'constraint_date' => $activity->act_constraint_date ? Carbon::parse($activity->act_constraint_date)->toIso8601String() : null,
                'sort_order'      => $activity->act_sort_order,
                'color'           => $activity->act_color,
                'late_start'      => $activity->act_late_start ? Carbon::parse($activity->act_late_start)->toIso8601String() : null,
                'late_finish'     => $activity->act_late_finish ? Carbon::parse($activity->act_late_finish)->toIso8601String() : null,
            ];
        }

        // Determine project-level date range
        $projectStart = null;
        $projectFinish = null;
        foreach ($activities as $act) {
            if ($act->act_early_start) {
                $es = Carbon::parse($act->act_early_start);
                if ($projectStart === null || $es->lt($projectStart)) {
                    $projectStart = $es;
                }
            }
            if ($act->act_early_finish) {
                $ef = Carbon::parse($act->act_early_finish);
                if ($projectFinish === null || $ef->gt($projectFinish)) {
                    $projectFinish = $ef;
                }
            }
        }

        return [
            'activities' => $ganttActivities,
            'project'    => [
                'id'            => $project->proj_id,
                'name'          => $project->proj_name,
                'start'         => $projectStart ? $projectStart->toIso8601String() : null,
                'finish'        => $projectFinish ? $projectFinish->toIso8601String() : null,
                'progress_date' => $project->proj_progress_date ? Carbon::parse($project->proj_progress_date)->toIso8601String() : null,
                'target_finish' => $project->proj_target_finish_date ? Carbon::parse($project->proj_target_finish_date)->toIso8601String() : null,
            ],
            'summary'    => [
                'total'         => $healthSummary['total_activities'] ?? 0,
                'critical'      => $healthSummary['critical_count'] ?? 0,
                'near_critical' => $healthSummary['near_critical_count'] ?? 0,
                'complete'      => $healthSummary['complete_activities'] ?? 0,
                'overdue'       => $healthSummary['overdue_count'] ?? 0,
                'health_grade'  => $healthSummary['health_grade'] ?? 'N/A',
                'completion_pct' => $healthSummary['completion_pct'] ?? 0,
            ],
        ];
    }

    /**
     * Check whether adding a set of dependencies would create a cycle.
     *
     * Uses Kahn's algorithm: if topological sort cannot visit all nodes,
     * a cycle exists.
     *
     * @param  array  $activityIds
     * @param  array  $dependencies  Array of records with dep_predecessor_id, dep_successor_id
     * @return bool   True if a cycle is detected
     */
    protected function hasCycle(array $activityIds, array $dependencies): bool
    {
        $inDegree = array_fill_keys($activityIds, 0);
        $adj = [];
        foreach ($activityIds as $id) {
            $adj[$id] = [];
        }

        foreach ($dependencies as $dep) {
            $pred = $dep['dep_predecessor_id'];
            $succ = $dep['dep_successor_id'];

            if (!isset($inDegree[$pred]) || !isset($inDegree[$succ])) {
                continue;
            }

            $adj[$pred][] = $succ;
            $inDegree[$succ]++;
        }

        $queue = [];
        foreach ($inDegree as $id => $deg) {
            if ($deg === 0) {
                $queue[] = $id;
            }
        }

        $visited = 0;
        while (!empty($queue)) {
            $node = array_shift($queue);
            $visited++;

            foreach ($adj[$node] as $neighbor) {
                $inDegree[$neighbor]--;
                if ($inDegree[$neighbor] === 0) {
                    $queue[] = $neighbor;
                }
            }
        }

        return $visited !== count($activityIds);
    }
}
