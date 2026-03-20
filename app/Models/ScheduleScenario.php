<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class ScheduleScenario extends Model
{
    use CompanyScope;

    protected $table = 'schedule_scenarios';
    protected $primaryKey = 'scn_id';
    public $timestamps = false;

    // Scenario reason codes
    const REASON_CO = 'CO';
    const REASON_RFI = 'RFI';
    const REASON_PROCUREMENT = 'PROCUREMENT';
    const REASON_WHAT_IF = 'WHAT_IF';

    protected $fillable = [
        'scn_project_id',
        'scn_name',
        'scn_reason',
        'scn_modifications',
        'scn_is_active',
        'scn_createby',
        'scn_createdate',
        'company_id',
    ];

    protected $casts = [
        'scn_modifications' => 'array',
        'scn_createdate'    => 'datetime',
    ];

    /**
     * Get the project that owns this scenario.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'scn_project_id', 'proj_id');
    }

    /**
     * Get the schedule runs for this scenario.
     */
    public function runs()
    {
        return $this->hasMany(ScheduleRun::class, 'run_scenario_id', 'scn_id');
    }

    /**
     * Scope for active scenarios.
     */
    public function scopeActive($query)
    {
        return $query->where('scn_is_active', 1);
    }
}
