<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class ScheduleActivity extends Model
{
    use CompanyScope;

    protected $table = 'schedule_activities';
    protected $primaryKey = 'act_id';
    public $timestamps = false;

    // Activity types
    const TYPE_TASK = 'TASK';
    const TYPE_MILESTONE = 'MILESTONE';
    const TYPE_SUMMARY = 'SUMMARY';

    // Activity statuses
    const STATUS_NOT_STARTED = 'NOT_STARTED';
    const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    const STATUS_COMPLETE = 'COMPLETE';
    const STATUS_BLOCKED = 'BLOCKED';

    // Constraint types
    const CONSTRAINT_NONE = 'NONE';
    const CONSTRAINT_SNET = 'SNET';
    const CONSTRAINT_FNLT = 'FNLT';
    const CONSTRAINT_MSO = 'MSO';
    const CONSTRAINT_MFO = 'MFO';

    const STATUS_BADGES = [
        self::STATUS_NOT_STARTED => 'secondary',
        self::STATUS_IN_PROGRESS => 'info',
        self::STATUS_COMPLETE    => 'success',
        self::STATUS_BLOCKED     => 'danger',
    ];

    protected $fillable = [
        'act_project_id',
        'act_wbs_id',
        'act_name',
        'act_description',
        'act_type',
        'act_duration_minutes',
        'act_calendar_id',
        'act_status',
        'act_percent_complete',
        'act_is_locked',
        'act_priority',
        'act_constraint_type',
        'act_constraint_date',
        'act_early_start',
        'act_early_finish',
        'act_late_start',
        'act_late_finish',
        'act_total_float_minutes',
        'act_free_float_minutes',
        'act_is_critical',
        'act_driving_predecessor_id',
        'act_driving_constraint_id',
        'act_sort_order',
        'act_color',
        'act_createby',
        'act_createdate',
        'act_modifyby',
        'act_modifydate',
        'company_id',
    ];

    protected $casts = [
        'act_constraint_date' => 'datetime',
        'act_early_start'    => 'datetime',
        'act_early_finish'   => 'datetime',
        'act_late_start'     => 'datetime',
        'act_late_finish'    => 'datetime',
        'act_percent_complete' => 'decimal:2',
    ];

    /**
     * Get the project that owns this activity.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'act_project_id', 'proj_id');
    }

    /**
     * Get the WBS node for this activity.
     */
    public function wbsNode()
    {
        return $this->belongsTo(ScheduleWbsNode::class, 'act_wbs_id', 'wbs_id');
    }

    /**
     * Get the calendar used by this activity.
     */
    public function calendar()
    {
        return $this->belongsTo(ScheduleCalendar::class, 'act_calendar_id', 'cal_id');
    }

    /**
     * Get dependencies where this activity is the successor (i.e. its predecessors).
     */
    public function predecessorDeps()
    {
        return $this->hasMany(ScheduleDependency::class, 'dep_successor_id', 'act_id');
    }

    /**
     * Get dependencies where this activity is the predecessor (i.e. its successors).
     */
    public function successorDeps()
    {
        return $this->hasMany(ScheduleDependency::class, 'dep_predecessor_id', 'act_id');
    }

    /**
     * Get the actuals record for this activity.
     */
    public function actuals()
    {
        return $this->hasOne(ScheduleActivityActual::class, 'aca_activity_id', 'act_id');
    }

    /**
     * Get the drivers associated with this activity.
     */
    public function drivers()
    {
        return $this->hasMany(ScheduleDriver::class, 'drv_activity_id', 'act_id');
    }

    /**
     * Get the driving predecessor activity.
     */
    public function drivingPredecessor()
    {
        return $this->belongsTo(ScheduleActivity::class, 'act_driving_predecessor_id', 'act_id');
    }

    /**
     * Scope by project.
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('act_project_id', $projectId);
    }

    /**
     * Scope by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('act_status', $status);
    }

    /**
     * Scope for critical-path activities.
     */
    public function scopeCritical($query)
    {
        return $query->where('act_is_critical', 1);
    }

    /**
     * Scope for milestones.
     */
    public function scopeMilestones($query)
    {
        return $query->where('act_type', self::TYPE_MILESTONE);
    }

    /**
     * Check whether this activity is a milestone.
     */
    public function isMilestone(): bool
    {
        return $this->act_type === self::TYPE_MILESTONE;
    }

    /**
     * Get duration expressed in working days (assumes 510 min = 8.5 hr default).
     */
    public function getDurationDays(): float
    {
        $minutesPerDay = 510;

        if ($this->calendar) {
            $calMinutes = $this->calendar->getWorkMinutesPerDay();
            if ($calMinutes > 0) {
                $minutesPerDay = $calMinutes;
            }
        }

        return $minutesPerDay > 0 ? round($this->act_duration_minutes / $minutesPerDay, 2) : 0;
    }

    /**
     * Get the Bootstrap badge class for the current status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return self::STATUS_BADGES[$this->act_status] ?? 'secondary';
    }
}
