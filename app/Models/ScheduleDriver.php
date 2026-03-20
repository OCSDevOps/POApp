<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class ScheduleDriver extends Model
{
    use CompanyScope;

    protected $table = 'schedule_drivers';
    protected $primaryKey = 'drv_id';
    public $timestamps = false;

    // Driver types
    const TYPE_PERMIT = 'PERMIT';
    const TYPE_INSPECTION = 'INSPECTION';
    const TYPE_PROCUREMENT = 'PROCUREMENT';
    const TYPE_ACCESS_WINDOW = 'ACCESS_WINDOW';
    const TYPE_UTILITY_CUTOVER = 'UTILITY_CUTOVER';
    const TYPE_OWNER_DECISION = 'OWNER_DECISION';

    // Driver statuses
    const STATUS_OPEN = 'OPEN';
    const STATUS_CLEARED = 'CLEARED';
    const STATUS_AT_RISK = 'AT_RISK';
    const STATUS_FAILED = 'FAILED';

    const STATUS_BADGES = [
        self::STATUS_OPEN     => 'secondary',
        self::STATUS_CLEARED  => 'success',
        self::STATUS_AT_RISK  => 'warning',
        self::STATUS_FAILED   => 'danger',
    ];

    protected $fillable = [
        'drv_project_id',
        'drv_type',
        'drv_name',
        'drv_activity_id',
        'drv_wbs_id',
        'drv_constraint_type',
        'drv_constraint_date',
        'drv_window_start',
        'drv_window_end',
        'drv_status',
        'drv_confidence',
        'drv_evidence_link',
        'drv_createby',
        'drv_createdate',
        'drv_modifyby',
        'drv_modifydate',
        'company_id',
    ];

    protected $casts = [
        'drv_constraint_date' => 'datetime',
        'drv_window_start'    => 'datetime',
        'drv_window_end'      => 'datetime',
        'drv_createdate'      => 'datetime',
    ];

    /**
     * Get the project that owns this driver.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'drv_project_id', 'proj_id');
    }

    /**
     * Get the activity associated with this driver.
     */
    public function activity()
    {
        return $this->belongsTo(ScheduleActivity::class, 'drv_activity_id', 'act_id');
    }

    /**
     * Get the constraint logs for this driver.
     */
    public function constraintLogs()
    {
        return $this->hasMany(ScheduleConstraintLog::class, 'cl_driver_id', 'drv_id');
    }

    /**
     * Scope for open drivers.
     */
    public function scopeOpen($query)
    {
        return $query->where('drv_status', self::STATUS_OPEN);
    }

    /**
     * Scope by driver type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('drv_type', $type);
    }

    /**
     * Scope by project.
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('drv_project_id', $projectId);
    }

    /**
     * Get the Bootstrap badge class for the current status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return self::STATUS_BADGES[$this->drv_status] ?? 'secondary';
    }
}
