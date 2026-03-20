<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleRunActivity extends Model
{
    protected $table = 'schedule_run_activities';
    protected $primaryKey = 'ra_id';
    public $timestamps = false;

    protected $fillable = [
        'ra_run_id',
        'ra_activity_id',
        'ra_early_start',
        'ra_early_finish',
        'ra_late_start',
        'ra_late_finish',
        'ra_total_float_minutes',
        'ra_free_float_minutes',
        'ra_is_critical',
        'ra_driving_predecessor_id',
        'ra_driving_constraint_id',
    ];

    protected $casts = [
        'ra_early_start'  => 'datetime',
        'ra_early_finish' => 'datetime',
        'ra_late_start'   => 'datetime',
        'ra_late_finish'  => 'datetime',
    ];

    /**
     * Get the schedule run that owns this activity result.
     */
    public function run()
    {
        return $this->belongsTo(ScheduleRun::class, 'ra_run_id', 'run_id');
    }

    /**
     * Get the original schedule activity.
     */
    public function activity()
    {
        return $this->belongsTo(ScheduleActivity::class, 'ra_activity_id', 'act_id');
    }
}
