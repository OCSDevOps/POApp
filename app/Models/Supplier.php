<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory, CompanyScope;

    protected $table = 'supplier_master';
    protected $primaryKey = 'sup_id';
    public $timestamps = false;

    protected $fillable = [
        'sup_name',
        'sup_email',
        'sup_phone',
        'sup_address',
        'sup_contact_person',
        'sup_details',
        'sup_type',
        'sup_status',
        'sup_createby',
        'sup_createdate',
        'sup_modifyby',
        'sup_modifydate',
        'procore_supplier_id',
        'company_id',
    ];

    /**
     * Get the company that owns the supplier.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * Get the purchase orders for the supplier.
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'porder_supplier_ms', 'sup_id');
    }

    /**
     * Get the catalog items for the supplier.
     */
    public function catalogItems()
    {
        return $this->hasMany(SupplierCatalog::class, 'supcat_supplier', 'sup_id');
    }

    /**
     * Scope for active suppliers
     */
    public function scopeActive($query)
    {
        return $query->where('sup_status', 1);
    }

    /**
     * Scope for ordering by name
     */
    public function scopeOrderByName($query)
    {
        return $query->orderBy('sup_name', 'ASC');
    }

    public function scopeSubcontractors($query)
    {
        return $query->whereIn('sup_type', [2, 3]);
    }

    public function scopeSupplierOnly($query)
    {
        return $query->where('sup_type', 1);
    }

    public function getTypeTextAttribute()
    {
        return match ((int) ($this->sup_type ?? 1)) {
            1 => 'Supplier',
            2 => 'Subcontractor',
            3 => 'Both',
            default => 'Supplier',
        };
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'contract_supplier_id', 'sup_id');
    }

    public function complianceItems()
    {
        return $this->hasMany(SupplierCompliance::class, 'compliance_supplier_id', 'sup_id');
    }
}
