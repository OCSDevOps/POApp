<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class ScheduleWbsNode extends Model
{
    use CompanyScope;

    protected $table = 'schedule_wbs_nodes';
    protected $primaryKey = 'wbs_id';
    public $timestamps = false;

    protected $fillable = [
        'wbs_project_id',
        'wbs_parent_id',
        'wbs_code',
        'wbs_name',
        'wbs_sort_order',
        'wbs_status',
        'company_id',
    ];

    /**
     * Get the project that owns this WBS node.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'wbs_project_id', 'proj_id');
    }

    /**
     * Get the parent WBS node.
     */
    public function parent()
    {
        return $this->belongsTo(ScheduleWbsNode::class, 'wbs_parent_id', 'wbs_id');
    }

    /**
     * Get the child WBS nodes.
     */
    public function children()
    {
        return $this->hasMany(ScheduleWbsNode::class, 'wbs_parent_id', 'wbs_id');
    }

    /**
     * Get the activities under this WBS node.
     */
    public function activities()
    {
        return $this->hasMany(ScheduleActivity::class, 'act_wbs_id', 'wbs_id');
    }

    /**
     * Scope for active WBS nodes.
     */
    public function scopeActive($query)
    {
        return $query->where('wbs_status', 1);
    }

    /**
     * Scope for root-level WBS nodes (no parent).
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('wbs_parent_id');
    }
}
