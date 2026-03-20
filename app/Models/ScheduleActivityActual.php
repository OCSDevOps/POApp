<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleActivityActual extends Model
{
    protected $table = 'schedule_activity_actuals';
    protected $primaryKey = 'aca_id';
    public $timestamps = false;

    protected $fillable = [
        'aca_activity_id',
        'aca_actual_start',
        'aca_actual_finish',
        'aca_remaining_duration_minutes',
        'aca_note',
        'aca_updated_by',
        'aca_updated_at',
    ];

    protected $casts = [
        'aca_actual_start'  => 'datetime',
        'aca_actual_finish' => 'datetime',
        'aca_updated_at'    => 'datetime',
    ];

    /**
     * Get the activity that owns these actuals.
     */
    public function activity()
    {
        return $this->belongsTo(ScheduleActivity::class, 'aca_activity_id', 'act_id');
    }
}
