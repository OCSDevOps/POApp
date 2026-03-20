<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class ScheduleRun extends Model
{
    use CompanyScope;

    protected $table = 'schedule_runs';
    protected $primaryKey = 'run_id';
    public $timestamps = false;

    protected $fillable = [
        'run_project_id',
        'run_scenario_id',
        'run_progress_date',
        'run_project_finish',
        'run_total_activities',
        'run_critical_count',
        'run_near_critical_count',
        'run_violations',
        'run_health_summary',
        'run_status',
        'run_computation_ms',
        'run_created_by',
        'run_created_at',
        'company_id',
    ];

    protected $casts = [
        'run_progress_date'  => 'datetime',
        'run_project_finish' => 'datetime',
        'run_created_at'     => 'datetime',
        'run_violations'     => 'array',
        'run_health_summary' => 'array',
    ];

    /**
     * Get the project that owns this run.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'run_project_id', 'proj_id');
    }

    /**
     * Get the scenario that produced this run.
     */
    public function scenario()
    {
        return $this->belongsTo(ScheduleScenario::class, 'run_scenario_id', 'scn_id');
    }

    /**
     * Get the computed activity results for this run.
     */
    public function activities()
    {
        return $this->hasMany(ScheduleRunActivity::class, 'ra_run_id', 'run_id');
    }

    /**
     * Scope to order by most recent first.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('run_created_at', 'desc');
    }

    /**
     * Scope for completed runs.
     */
    public function scopeCompleted($query)
    {
        return $query->where('run_status', 'COMPLETED');
    }
}
