<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory, CompanyScope, \App\Traits\HasAttachments;

    protected $table = 'purchase_order_master';
    protected $primaryKey = 'porder_id';
    public $timestamps = false;

    protected $fillable = [
        'porder_no',
        'porder_project_ms',
        'porder_supplier_ms',
        'porder_address',
        'porder_delivery_note',
        'porder_description',
        'porder_total_item',
        'porder_total_amount',
        'porder_delivery_status',
        'porder_status',
        'porder_total_tax',
        'porder_createdate',
        'porder_createby',
        'porder_modifydate',
        'porder_modifyby',
        'porder_original_total',
        'porder_change_orders_total',
        'integration_status',
        'procore_po_id',
        'company_id',
    ];

    /**
     * Get the company that owns the purchase order.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

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
        return $this->hasMany(PurchaseOrderItem::class, 'po_detail_porder_ms', 'porder_id');
    }

    /**
     * Get the attachments for this purchase order.
     */
    public function attachments()
    {
        return $this->hasMany(PurchaseOrderAttachment::class, 'po_attachment_porder_ms', 'porder_id');
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
        if ($status !== null && $status !== '') {
            return $query->where('porder_status', $status);
        }
        return $query;
    }

    /**
     * Scope for active orders (porder_status = 1)
     */
    public function scopeActive($query)
    {
        return $query->where('porder_status', 1);
    }

    /**
     * Scope for inactive orders (porder_status = 0)
     */
    public function scopeInactive($query)
    {
        return $query->where('porder_status', 0);
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
     * Accessor for grand total (computed: porder_total_amount + porder_total_tax).
     */
    public function getGrandTotalAttribute()
    {
        return ($this->porder_total_amount ?? 0) + ($this->porder_total_tax ?? 0);
    }
}
