<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyScope;

class ProjectCostCode extends Model
{
    use CompanyScope;
    protected $table = 'project_cost_codes';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'company_id',
        'project_id',
        'cost_code_id',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the project this assignment belongs to.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'proj_id');
    }

    /**
     * Get the cost code for this assignment.
     */
    public function costCode()
    {
        return $this->belongsTo(CostCode::class, 'cost_code_id', 'cc_id');
    }

    /**
     * Get the company this assignment belongs to.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope to get only active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by project.
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope to filter by cost code.
     */
    public function scopeByCostCode($query, $costCodeId)
    {
        return $query->where('cost_code_id', $costCodeId);
    }
}
