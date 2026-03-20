<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class ScheduleConstraintLog extends Model
{
    use CompanyScope;

    protected $table = 'schedule_constraint_log';
    protected $primaryKey = 'cl_id';
    public $timestamps = false;

    protected $fillable = [
        'cl_project_id',
        'cl_activity_id',
        'cl_driver_id',
        'cl_needed_by_date',
        'cl_owner_role',
        'cl_status',
        'cl_notes',
        'cl_createby',
        'cl_createdate',
        'cl_modifyby',
        'cl_modifydate',
        'company_id',
    ];

    protected $casts = [
        'cl_needed_by_date' => 'datetime',
        'cl_createdate'     => 'datetime',
    ];

    /**
     * Get the project that owns this constraint log.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'cl_project_id', 'proj_id');
    }

    /**
     * Get the activity associated with this constraint log.
     */
    public function activity()
    {
        return $this->belongsTo(ScheduleActivity::class, 'cl_activity_id', 'act_id');
    }

    /**
     * Get the driver associated with this constraint log.
     */
    public function driver()
    {
        return $this->belongsTo(ScheduleDriver::class, 'cl_driver_id', 'drv_id');
    }

    /**
     * Scope for open constraint logs.
     */
    public function scopeOpen($query)
    {
        return $query->where('cl_status', 'OPEN');
    }

    /**
     * Scope by project.
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('cl_project_id', $projectId);
    }
}
