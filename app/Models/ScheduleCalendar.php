<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class ScheduleCalendar extends Model
{
    use CompanyScope;

    protected $table = 'schedule_calendars';
    protected $primaryKey = 'cal_id';
    public $timestamps = false;

    protected $fillable = [
        'cal_project_id',
        'cal_name',
        'cal_timezone',
        'cal_work_week',
        'cal_work_start',
        'cal_work_end',
        'cal_is_default',
        'cal_status',
        'cal_createby',
        'cal_createdate',
        'cal_modifyby',
        'cal_modifydate',
        'company_id',
    ];

    /**
     * Get the project that owns this calendar.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'cal_project_id', 'proj_id');
    }

    /**
     * Get the exceptions for this calendar.
     */
    public function exceptions()
    {
        return $this->hasMany(ScheduleCalendarException::class, 'cex_calendar_id', 'cal_id');
    }

    /**
     * Get the activities using this calendar.
     */
    public function activities()
    {
        return $this->hasMany(ScheduleActivity::class, 'act_calendar_id', 'cal_id');
    }

    /**
     * Scope for active calendars.
     */
    public function scopeActive($query)
    {
        return $query->where('cal_status', 1);
    }

    /**
     * Scope for default calendars.
     */
    public function scopeDefault($query)
    {
        return $query->where('cal_is_default', 1);
    }

    /**
     * Get work days as an array from the CSV stored in cal_work_week.
     *
     * @return array e.g. ['Mon','Tue','Wed','Thu','Fri']
     */
    public function getWorkDaysArray(): array
    {
        if (empty($this->cal_work_week)) {
            return [];
        }

        return array_map('trim', explode(',', $this->cal_work_week));
    }

    /**
     * Calculate the number of work minutes per day based on cal_work_start and cal_work_end.
     *
     * @return int
     */
    public function getWorkMinutesPerDay(): int
    {
        if (empty($this->cal_work_start) || empty($this->cal_work_end)) {
            return 510; // default 8.5 hours
        }

        $start = strtotime($this->cal_work_start);
        $end = strtotime($this->cal_work_end);

        if ($start === false || $end === false || $end <= $start) {
            return 510;
        }

        return (int) (($end - $start) / 60);
    }
}
