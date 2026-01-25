<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_master';
    protected $primaryKey = 'porder_id';
    public $timestamps = false;

    protected $fillable = [
        'porder_no',
        'porder_project_ms',
        'porder_supplier_ms',
        'porder_type',
        'porder_date',
        'porder_delivery_date',
        'porder_delivery_status',
        'porder_general_status',
        'porder_total',
        'porder_tax',
        'porder_grand_total',
        'porder_notes',
        'porder_terms',
        'porder_created_by',
        'porder_created_at',
        'porder_modified_by',
        'porder_modified_at',
        'integration_status',
        'procore_po_id',
    ];

    /**
     * Get the project associated with the purchase order.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'porder_project_ms', 'proj_id');
    }

    /**
     * Get the supplier associated with the purchase order.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'porder_supplier_ms', 'sup_id');
    }

    /**
     * Get the items for the purchase order.
     */
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'porder_item_porder_ms', 'porder_id');
    }

    /**
     * Get the receive orders for this purchase order.
     */
    public function receiveOrders()
    {
        return $this->hasMany(ReceiveOrder::class, 'rorder_porder_ms', 'porder_id');
    }

    /**
     * Scope for filtering by project
     */
    public function scopeByProject($query, $projectId)
    {
        if ($projectId) {
            return $query->where('porder_project_ms', $projectId);
        }
        return $query;
    }

    /**
     * Scope for filtering by supplier
     */
    public function scopeBySupplier($query, $supplierId)
    {
        if ($supplierId) {
            return $query->where('porder_supplier_ms', $supplierId);
        }
        return $query;
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('porder_general_status', $status);
        }
        return $query;
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('porder_general_status', 'pending');
    }

    /**
     * Scope for submitted orders
     */
    public function scopeSubmitted($query)
    {
        return $query->where('porder_general_status', 'submitted');
    }

    /**
     * Scope for RTE (Ready to Export) orders
     */
    public function scopeRte($query)
    {
        return $query->where('integration_status', 'rte');
    }

    /**
     * Scope for synced orders
     */
    public function scopeSynced($query)
    {
        return $query->where('integration_status', 'synced');
    }

    /**
     * Scope for partially received orders
     */
    public function scopePartiallyReceived($query)
    {
        return $query->where('porder_delivery_status', '2');
    }

    /**
     * Scope for fully received orders
     */
    public function scopeFullyReceived($query)
    {
        return $query->where('porder_delivery_status', '1');
    }

    /**
     * Scope for not received orders
     */
    public function scopeNotReceived($query)
    {
        return $query->where('porder_delivery_status', '0');
    }

    /**
     * Scope for filtering by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('porder_type', $type);
    }
}
