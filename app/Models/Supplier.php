<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier_master';
    protected $primaryKey = 'sup_id';
    public $timestamps = false;

    protected $fillable = [
        'sup_name',
        'sup_code',
        'sup_email',
        'sup_phone',
        'sup_mobile',
        'sup_address',
        'sup_city',
        'sup_state',
        'sup_zip',
        'sup_country',
        'sup_contact_person',
        'sup_status',
        'sup_created_by',
        'sup_created_at',
        'sup_modified_by',
        'sup_modified_at',
        'procore_supplier_id',
    ];

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
}
