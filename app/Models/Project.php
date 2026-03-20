<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, CompanyScope;

    protected $table = 'project_master';
    protected $primaryKey = 'proj_id';
    public $timestamps = false;

    protected $fillable = [
        'proj_number',
        'proj_name',
        'proj_address',
        'proj_description',
        'proj_contact',
        'proj_status',
        'proj_createby',
        'proj_createdate',
        'proj_modifyby',
        'proj_modifydate',
        'procore_project_id',
        'company_id',
        'proj_default_calendar_id',
        'proj_scheduling_mode',
        'proj_progress_date',
        'proj_target_finish_date',
    ];

    protected $casts = [
        'proj_progress_date'      => 'datetime',
        'proj_target_finish_date' => 'datetime',
    ];

    /**
     * Get the company that owns the project.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * Get the purchase orders for the project.
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'porder_project_ms', 'proj_id');
    }

    /**
     * Get the project details.
     */
    public function details()
    {
        return $this->hasMany(ProjectDetail::class, 'pdetail_proj_ms', 'proj_id');
    }

    /**
     * Scope for active projects
     */
    public function scopeActive($query)
    {
        return $query->where('proj_status', 1);
    }

    /**
     * Scope for ordering by name
     */
    public function scopeOrderByName($query)
    {
        return $query->orderBy('proj_name', 'ASC');
    }

    public function takeoffs()
    {
        return $this->hasMany(Takeoff::class, 'to_project_id', 'proj_id');
    }

    /**
     * Get the default schedule calendar for this project.
     */
    public function defaultCalendar()
    {
        return $this->belongsTo(ScheduleCalendar::class, 'proj_default_calendar_id', 'cal_id');
    }

    /**
     * Get the schedule activities for this project.
     */
    public function scheduleActivities()
    {
        return $this->hasMany(ScheduleActivity::class, 'act_project_id', 'proj_id');
    }

    /**
     * Get the schedule calendars for this project.
     */
    public function scheduleCalendars()
    {
        return $this->hasMany(ScheduleCalendar::class, 'cal_project_id', 'proj_id');
    }

    /**
     * Get the schedule runs for this project.
     */
    public function scheduleRuns()
    {
        return $this->hasMany(ScheduleRun::class, 'run_project_id', 'proj_id');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'contract_project_id', 'proj_id');
    }
}
