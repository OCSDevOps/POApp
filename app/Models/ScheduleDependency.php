<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class ScheduleDependency extends Model
{
    use CompanyScope;

    protected $table = 'schedule_dependencies';
    protected $primaryKey = 'dep_id';
    public $timestamps = false;

    // Dependency types
    const TYPE_FS = 'FS';
    const TYPE_SS = 'SS';
    const TYPE_FF = 'FF';
    const TYPE_SF = 'SF';

    protected $fillable = [
        'dep_project_id',
        'dep_predecessor_id',
        'dep_successor_id',
        'dep_type',
        'dep_lag_minutes',
        'dep_lag_calendar_mode',
        'company_id',
    ];

    /**
     * Get the project that owns this dependency.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'dep_project_id', 'proj_id');
    }

    /**
     * Get the predecessor activity.
     */
    public function predecessor()
    {
        return $this->belongsTo(ScheduleActivity::class, 'dep_predecessor_id', 'act_id');
    }

    /**
     * Get the successor activity.
     */
    public function successor()
    {
        return $this->belongsTo(ScheduleActivity::class, 'dep_successor_id', 'act_id');
    }

    /**
     * Get lag expressed in working days (assumes 510 min = 8.5 hr default).
     */
    public function getLagDays(): float
    {
        return $this->dep_lag_minutes / 510;
    }

    /**
     * Get a human-readable label, e.g. "FS+2d", "SS", "FS-1d".
     */
    public function getLabel(): string
    {
        $label = $this->dep_type ?? 'FS';

        if ($this->dep_lag_minutes && $this->dep_lag_minutes != 0) {
            $days = $this->getLagDays();
            $sign = $days >= 0 ? '+' : '';
            $label .= $sign . round($days, 1) . 'd';
        }

        return $label;
    }
}
