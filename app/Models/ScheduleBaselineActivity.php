<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleBaselineActivity extends Model
{
    protected $table = 'schedule_baseline_activities';
    protected $primaryKey = 'bla_id';
    public $timestamps = false;

    protected $fillable = [
        'bla_baseline_id',
        'bla_activity_id',
        'bla_start',
        'bla_finish',
        'bla_duration_minutes',
    ];

    protected $casts = [
        'bla_start'  => 'datetime',
        'bla_finish' => 'datetime',
    ];

    /**
     * Get the baseline that owns this activity snapshot.
     */
    public function baseline()
    {
        return $this->belongsTo(ScheduleBaseline::class, 'bla_baseline_id', 'bl_id');
    }

    /**
     * Get the original schedule activity.
     */
    public function activity()
    {
        return $this->belongsTo(ScheduleActivity::class, 'bla_activity_id', 'act_id');
    }
}
