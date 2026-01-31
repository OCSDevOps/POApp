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
        'proj_name',
        'proj_code',
        'proj_address',
        'proj_city',
        'proj_state',
        'proj_zip',
        'proj_country',
        'proj_start_date',
        'proj_end_date',
        'proj_status',
        'proj_created_by',
        'proj_created_at',
        'proj_modified_by',
        'proj_modified_at',
        'procore_project_id',
        'company_id',
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
}
