<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleCalendarException extends Model
{
    protected $table = 'schedule_calendar_exceptions';
    protected $primaryKey = 'cex_id';
    public $timestamps = false;

    const TYPE_HOLIDAY = 'holiday';
    const TYPE_SHUTDOWN = 'shutdown';
    const TYPE_WORKDAY = 'workday';

    protected $fillable = [
        'cex_calendar_id',
        'cex_date',
        'cex_type',
        'cex_name',
        'cex_work_start',
        'cex_work_end',
    ];

    protected $casts = [
        'cex_date' => 'date',
    ];

    /**
     * Get the calendar that owns this exception.
     */
    public function calendar()
    {
        return $this->belongsTo(ScheduleCalendar::class, 'cex_calendar_id', 'cal_id');
    }

    /**
     * Scope for holiday exceptions.
     */
    public function scopeHolidays($query)
    {
        return $query->where('cex_type', self::TYPE_HOLIDAY);
    }

    /**
     * Scope for workday override exceptions.
     */
    public function scopeWorkdays($query)
    {
        return $query->where('cex_type', self::TYPE_WORKDAY);
    }
}
