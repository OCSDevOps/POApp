<?php

namespace App\Services\Scheduling;

use App\Models\ScheduleCalendar;
use App\Models\ScheduleCalendarException;
use Carbon\Carbon;

class CalendarService
{
    /**
     * Maximum iterations when searching for next/previous working day.
     * Guards against infinite loops if calendar has no working days configured.
     */
    protected const MAX_DAY_SEARCH = 365;

    /**
     * Cache of exception dates per calendar to avoid repeated DB queries.
     * Key: cal_id, Value: ['holidays' => [date_strings], 'workdays' => [date_string => [start, end]]]
     */
    protected array $exceptionCache = [];

    /**
     * Map of day abbreviations to Carbon day-of-week integers.
     * Carbon: Monday=1 ... Sunday=7 (ISO standard).
     */
    protected const DAY_MAP = [
        'Mon' => Carbon::MONDAY,
        'Tue' => Carbon::TUESDAY,
        'Wed' => Carbon::WEDNESDAY,
        'Thu' => Carbon::THURSDAY,
        'Fri' => Carbon::FRIDAY,
        'Sat' => Carbon::SATURDAY,
        'Sun' => Carbon::SUNDAY,
    ];

    /**
     * Load and cache exceptions for a calendar.
     *
     * Queries the database once per calendar and stores the results keyed by
     * calendar ID. Subsequent calls for the same calendar return cached data.
     *
     * @param ScheduleCalendar $calendar
     * @return array ['holidays' => [date_strings], 'workdays' => [date_string => ['start' => HH:MM, 'end' => HH:MM]]]
     */
    public function loadExceptions(ScheduleCalendar $calendar): array
    {
        $calId = $calendar->cal_id;

        if (isset($this->exceptionCache[$calId])) {
            return $this->exceptionCache[$calId];
        }

        $exceptions = ScheduleCalendarException::where('cex_calendar_id', $calId)->get();

        $holidays = [];
        $workdays = [];

        foreach ($exceptions as $exception) {
            $dateKey = Carbon::parse($exception->cex_date)->format('Y-m-d');

            if ($exception->cex_type === ScheduleCalendarException::TYPE_HOLIDAY
                || $exception->cex_type === ScheduleCalendarException::TYPE_SHUTDOWN) {
                $holidays[] = $dateKey;
            } elseif ($exception->cex_type === ScheduleCalendarException::TYPE_WORKDAY) {
                $workdays[$dateKey] = [
                    'start' => $exception->cex_work_start ?? $calendar->cal_work_start,
                    'end'   => $exception->cex_work_end ?? $calendar->cal_work_end,
                ];
            }
        }

        $this->exceptionCache[$calId] = [
            'holidays' => $holidays,
            'workdays' => $workdays,
        ];

        return $this->exceptionCache[$calId];
    }

    /**
     * Check if a given date is a working day according to the calendar.
     *
     * Evaluation order:
     *  1. If the date has a holiday or shutdown exception, it is NOT a working day.
     *  2. If the date has a workday exception, it IS a working day (overrides weekends).
     *  3. Otherwise, check if the day-of-week is in the calendar's work week.
     *
     * @param ScheduleCalendar $calendar
     * @param Carbon $date
     * @return bool
     */
    public function isWorkingDay(ScheduleCalendar $calendar, Carbon $date): bool
    {
        $exceptions = $this->loadExceptions($calendar);
        $dateKey = $date->copy()->setTimezone($calendar->cal_timezone ?? 'UTC')->format('Y-m-d');

        // 1. Holiday/shutdown exception overrides everything
        if (in_array($dateKey, $exceptions['holidays'], true)) {
            return false;
        }

        // 2. Workday exception makes a non-work day into a work day
        if (isset($exceptions['workdays'][$dateKey])) {
            return true;
        }

        // 3. Check if the day-of-week is in the calendar's work week
        $workDays = $calendar->getWorkDaysArray();
        $dayOfWeek = $date->copy()->setTimezone($calendar->cal_timezone ?? 'UTC')->dayOfWeekIso; // 1=Mon..7=Sun

        foreach ($workDays as $dayName) {
            $dayName = trim($dayName);
            if (isset(self::DAY_MAP[$dayName]) && self::DAY_MAP[$dayName] === $dayOfWeek) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get work hours for a specific date.
     *
     * Returns [start_time, end_time] as strings like ["07:00", "15:30"].
     * If it's a workday exception, use custom hours from the exception.
     * Otherwise use calendar default hours.
     * Returns null if not a working day.
     *
     * @param ScheduleCalendar $calendar
     * @param Carbon $date
     * @return array|null [start_time_string, end_time_string] or null
     */
    public function getWorkHours(ScheduleCalendar $calendar, Carbon $date): ?array
    {
        if (!$this->isWorkingDay($calendar, $date)) {
            return null;
        }

        $exceptions = $this->loadExceptions($calendar);
        $dateKey = $date->copy()->setTimezone($calendar->cal_timezone ?? 'UTC')->format('Y-m-d');

        // Workday exception with custom hours
        if (isset($exceptions['workdays'][$dateKey])) {
            return [
                $exceptions['workdays'][$dateKey]['start'],
                $exceptions['workdays'][$dateKey]['end'],
            ];
        }

        // Default calendar hours
        return [
            $calendar->cal_work_start,
            $calendar->cal_work_end,
        ];
    }

    /**
     * Get the number of working minutes for a specific date.
     *
     * @param ScheduleCalendar $calendar
     * @param Carbon $date
     * @return int Working minutes (0 if not a working day)
     */
    public function getWorkMinutesForDate(ScheduleCalendar $calendar, Carbon $date): int
    {
        $hours = $this->getWorkHours($calendar, $date);

        if ($hours === null) {
            return 0;
        }

        [$startStr, $endStr] = $hours;
        [$startH, $startM] = $this->parseTime($startStr);
        [$endH, $endM] = $this->parseTime($endStr);

        $startMinutes = ($startH * 60) + $startM;
        $endMinutes = ($endH * 60) + $endM;

        return max(0, $endMinutes - $startMinutes);
    }

    /**
     * Get default work minutes per day for this calendar.
     *
     * Uses the calendar's cal_work_start and cal_work_end to compute default
     * daily work minutes, independent of any exceptions.
     *
     * @param ScheduleCalendar $calendar
     * @return int
     */
    public function getDefaultWorkMinutesPerDay(ScheduleCalendar $calendar): int
    {
        if (empty($calendar->cal_work_start) || empty($calendar->cal_work_end)) {
            return 510; // default 8.5 hours
        }

        [$startH, $startM] = $this->parseTime($calendar->cal_work_start);
        [$endH, $endM] = $this->parseTime($calendar->cal_work_end);

        $startMinutes = ($startH * 60) + $startM;
        $endMinutes = ($endH * 60) + $endM;

        $diff = $endMinutes - $startMinutes;

        return $diff > 0 ? $diff : 510;
    }

    /**
     * (A) Snap a datetime forward to the next working minute.
     *
     * If dt is outside work hours or on a non-working day, advance to the start
     * of the next work period. If dt is already within work hours on a working
     * day, return dt unchanged.
     *
     * @param Carbon $dt
     * @param ScheduleCalendar $calendar
     * @return Carbon
     */
    public function nextWorkTime(Carbon $dt, ScheduleCalendar $calendar): Carbon
    {
        $tz = $calendar->cal_timezone ?? 'UTC';
        $result = $dt->copy()->setTimezone($tz);

        for ($i = 0; $i < self::MAX_DAY_SEARCH; $i++) {
            if ($this->isWorkingDay($calendar, $result)) {
                $hours = $this->getWorkHours($calendar, $result);

                if ($hours !== null) {
                    [$startStr, $endStr] = $hours;
                    $dayStart = $this->setTime($result->copy(), $startStr);
                    $dayEnd = $this->setTime($result->copy(), $endStr);

                    // If before work start, snap to work start
                    if ($result->lt($dayStart)) {
                        return $dayStart;
                    }

                    // If within work hours (before end), already valid
                    if ($result->lt($dayEnd)) {
                        return $result;
                    }

                    // Past end of work for today, fall through to next day
                }
            }

            // Move to the start of the next day
            $result = $result->copy()->addDay()->startOfDay();
        }

        // Fallback: should never reach here with a valid calendar
        return $result;
    }

    /**
     * (B) Add working minutes to a datetime, moving forward through work time only.
     *
     * The input dt should already be at a valid work time (use nextWorkTime first).
     * Skips non-working periods (weekends, holidays, outside work hours).
     *
     * @param Carbon $dt
     * @param int $minutes Number of working minutes to add
     * @param ScheduleCalendar $calendar
     * @return Carbon
     */
    public function addWorkMinutes(Carbon $dt, int $minutes, ScheduleCalendar $calendar): Carbon
    {
        // Edge case: 0 minutes returns same time
        if ($minutes === 0) {
            return $dt->copy();
        }

        // Negative minutes delegates to subtractWorkMinutes
        if ($minutes < 0) {
            return $this->subtractWorkMinutes($dt, abs($minutes), $calendar);
        }

        $tz = $calendar->cal_timezone ?? 'UTC';
        $result = $this->nextWorkTime($dt, $calendar);
        $remaining = $minutes;

        for ($i = 0; $i < self::MAX_DAY_SEARCH * 2 && $remaining > 0; $i++) {
            // Ensure we are on a working day at a valid time
            $result = $this->nextWorkTime($result, $calendar);

            $hours = $this->getWorkHours($calendar, $result);
            if ($hours === null) {
                // nextWorkTime should prevent this, but guard just in case
                $result = $result->copy()->addDay()->startOfDay();
                continue;
            }

            [$startStr, $endStr] = $hours;
            $dayEnd = $this->setTime($result->copy(), $endStr);

            // Minutes remaining in the current work day from current position
            $minutesLeftToday = (int) $result->diffInMinutes($dayEnd, false);

            if ($minutesLeftToday <= 0) {
                // Past end of day, move to next day
                $result = $result->copy()->addDay()->startOfDay();
                continue;
            }

            if ($remaining <= $minutesLeftToday) {
                // All remaining minutes fit within today
                $result = $result->copy()->addMinutes($remaining);
                $remaining = 0;
            } else {
                // Consume the rest of today, then continue to next day
                $remaining -= $minutesLeftToday;
                $result = $result->copy()->addDay()->startOfDay();
            }
        }

        return $result;
    }

    /**
     * (C) Subtract working minutes from a datetime, moving backward through work time only.
     *
     * Skips non-working periods going backward.
     *
     * @param Carbon $dt
     * @param int $minutes Number of working minutes to subtract
     * @param ScheduleCalendar $calendar
     * @return Carbon
     */
    public function subtractWorkMinutes(Carbon $dt, int $minutes, ScheduleCalendar $calendar): Carbon
    {
        // Edge case: 0 minutes returns same time
        if ($minutes === 0) {
            return $dt->copy();
        }

        // Negative minutes delegates to addWorkMinutes
        if ($minutes < 0) {
            return $this->addWorkMinutes($dt, abs($minutes), $calendar);
        }

        $tz = $calendar->cal_timezone ?? 'UTC';
        $result = $this->previousWorkEnd($dt, $calendar);
        $remaining = $minutes;

        for ($i = 0; $i < self::MAX_DAY_SEARCH * 2 && $remaining > 0; $i++) {
            if (!$this->isWorkingDay($calendar, $result)) {
                // Move to previous day's end
                $result = $result->copy()->subDay();
                $result = $this->findPreviousWorkingDayEnd($result, $calendar);
                continue;
            }

            $hours = $this->getWorkHours($calendar, $result);
            if ($hours === null) {
                $result = $result->copy()->subDay();
                $result = $this->findPreviousWorkingDayEnd($result, $calendar);
                continue;
            }

            [$startStr, $endStr] = $hours;
            $dayStart = $this->setTime($result->copy(), $startStr);
            $dayEnd = $this->setTime($result->copy(), $endStr);

            // Clamp result to be within [dayStart, dayEnd]
            if ($result->gt($dayEnd)) {
                $result = $dayEnd->copy();
            }
            if ($result->lt($dayStart)) {
                // Before work start, move to previous day
                $result = $result->copy()->subDay();
                $result = $this->findPreviousWorkingDayEnd($result, $calendar);
                continue;
            }

            // Minutes available from day start to current position
            $minutesFromStart = (int) $dayStart->diffInMinutes($result, false);

            if ($minutesFromStart <= 0) {
                // At day start, move to previous day
                $result = $result->copy()->subDay();
                $result = $this->findPreviousWorkingDayEnd($result, $calendar);
                continue;
            }

            if ($remaining <= $minutesFromStart) {
                // All remaining minutes fit within today going backward
                $result = $result->copy()->subMinutes($remaining);
                $remaining = 0;
            } else {
                // Consume all of today's time from start, then move to previous day
                $remaining -= $minutesFromStart;
                $result = $result->copy()->subDay();
                $result = $this->findPreviousWorkingDayEnd($result, $calendar);
            }
        }

        return $result;
    }

    /**
     * Get the previous work time end.
     *
     * If dt is within a working period, return dt unchanged.
     * If dt is on a non-working period (non-work day, before work start, or
     * after work end), move backward to find the previous working day's end time.
     *
     * @param Carbon $dt
     * @param ScheduleCalendar $calendar
     * @return Carbon
     */
    public function previousWorkEnd(Carbon $dt, ScheduleCalendar $calendar): Carbon
    {
        $tz = $calendar->cal_timezone ?? 'UTC';
        $result = $dt->copy()->setTimezone($tz);

        // If currently within working hours, return as-is
        if ($this->isWorkingDay($calendar, $result)) {
            $hours = $this->getWorkHours($calendar, $result);
            if ($hours !== null) {
                [$startStr, $endStr] = $hours;
                $dayStart = $this->setTime($result->copy(), $startStr);
                $dayEnd = $this->setTime($result->copy(), $endStr);

                // Within work hours
                if ($result->gte($dayStart) && $result->lte($dayEnd)) {
                    return $result;
                }

                // After work end, return this day's end time
                if ($result->gt($dayEnd)) {
                    return $dayEnd;
                }

                // Before work start, fall through to find previous day end
            }
        }

        // Move backward to find the previous working day's end
        return $this->findPreviousWorkingDayEnd($result->copy()->subDay(), $calendar);
    }

    /**
     * Calculate the number of working minutes between two datetimes.
     *
     * Counts only time that falls within working periods as defined by the
     * calendar. Useful for float calculations in CPM scheduling.
     *
     * @param Carbon $start
     * @param Carbon $end
     * @param ScheduleCalendar $calendar
     * @return int Working minutes between start and end (0 if end <= start)
     */
    public function workMinutesBetween(Carbon $start, Carbon $end, ScheduleCalendar $calendar): int
    {
        $tz = $calendar->cal_timezone ?? 'UTC';
        $s = $start->copy()->setTimezone($tz);
        $e = $end->copy()->setTimezone($tz);

        if ($e->lte($s)) {
            return 0;
        }

        $totalMinutes = 0;
        $current = $s->copy();

        for ($i = 0; $i < self::MAX_DAY_SEARCH && $current->lt($e); $i++) {
            if (!$this->isWorkingDay($calendar, $current)) {
                $current = $current->copy()->addDay()->startOfDay();
                continue;
            }

            $hours = $this->getWorkHours($calendar, $current);
            if ($hours === null) {
                $current = $current->copy()->addDay()->startOfDay();
                continue;
            }

            [$startStr, $endStr] = $hours;
            $dayStart = $this->setTime($current->copy(), $startStr);
            $dayEnd = $this->setTime($current->copy(), $endStr);

            // Determine the effective start within this day
            $effectiveStart = $current->gt($dayStart) ? $current->copy() : $dayStart->copy();

            // Determine the effective end within this day
            $effectiveEnd = $e->lt($dayEnd) ? $e->copy() : $dayEnd->copy();

            // Only count if effective start is before effective end and within work hours
            if ($effectiveStart->lt($effectiveEnd) && $effectiveStart->lt($dayEnd) && $effectiveEnd->gt($dayStart)) {
                // Clamp to work boundaries
                if ($effectiveStart->lt($dayStart)) {
                    $effectiveStart = $dayStart->copy();
                }
                if ($effectiveEnd->gt($dayEnd)) {
                    $effectiveEnd = $dayEnd->copy();
                }

                $totalMinutes += (int) $effectiveStart->diffInMinutes($effectiveEnd, false);
            }

            // Move to the next day
            $current = $current->copy()->addDay()->startOfDay();
        }

        return max(0, $totalMinutes);
    }

    /**
     * Parse a time string "HH:MM" into hours and minutes.
     *
     * @param string $time Time string in "HH:MM" format
     * @return array [hours, minutes] as integers
     */
    protected function parseTime(string $time): array
    {
        $parts = explode(':', $time);

        return [
            (int) ($parts[0] ?? 0),
            (int) ($parts[1] ?? 0),
        ];
    }

    /**
     * Set the time component of a Carbon date from a "HH:MM" string.
     *
     * Preserves the date and timezone, only changes hours, minutes, and zeroes
     * out seconds and microseconds.
     *
     * @param Carbon $date
     * @param string $time Time string in "HH:MM" format
     * @return Carbon Modified Carbon instance
     */
    protected function setTime(Carbon $date, string $time): Carbon
    {
        [$hours, $minutes] = $this->parseTime($time);

        return $date->setTime($hours, $minutes, 0, 0);
    }

    /**
     * Find the end of the most recent working day at or before the given date.
     *
     * Searches backward from the given date to find a working day, then returns
     * that day's work end time.
     *
     * @param Carbon $date Starting date to search backward from
     * @param ScheduleCalendar $calendar
     * @return Carbon The work end time of the found working day
     */
    protected function findPreviousWorkingDayEnd(Carbon $date, ScheduleCalendar $calendar): Carbon
    {
        $tz = $calendar->cal_timezone ?? 'UTC';
        $search = $date->copy()->setTimezone($tz);

        for ($i = 0; $i < self::MAX_DAY_SEARCH; $i++) {
            if ($this->isWorkingDay($calendar, $search)) {
                $hours = $this->getWorkHours($calendar, $search);
                if ($hours !== null) {
                    return $this->setTime($search->copy(), $hours[1]);
                }
            }
            $search = $search->copy()->subDay();
        }

        // Fallback: should never reach here with a valid calendar
        return $date->copy();
    }
}
