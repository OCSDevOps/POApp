<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class ScheduleBaseline extends Model
{
    use CompanyScope;

    protected $table = 'schedule_baselines';
    protected $primaryKey = 'bl_id';
    public $timestamps = false;

    protected $fillable = [
        'bl_project_id',
        'bl_name',
        'bl_created_by',
        'bl_created_at',
        'company_id',
    ];

    protected $casts = [
        'bl_created_at' => 'datetime',
    ];

    /**
     * Get the project that owns this baseline.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'bl_project_id', 'proj_id');
    }

    /**
     * Get the baseline activity snapshots.
     */
    public function activities()
    {
        return $this->hasMany(ScheduleBaselineActivity::class, 'bla_baseline_id', 'bl_id');
    }
}
