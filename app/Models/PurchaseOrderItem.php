<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_details';
    protected $primaryKey = 'po_detail_id';
    public $timestamps = false;

    protected $fillable = [
        'po_detail_autogen',
        'po_detail_porder_ms',
        'po_detail_item',
        'po_detail_sku',
        'po_detail_taxcode',
        'po_detail_quantity',
        'backordered_qty',
        'expected_backorder_date',
        'backorder_status',
        'po_detail_unitprice',
        'po_detail_subtotal',
        'po_detail_taxamount',
        'po_detail_total',
        'po_detail_createdate',
        'po_detail_status',
        'po_detail_tax_group',
        'company_id',
    ];

    protected $casts = [
        'po_detail_unitprice' => 'decimal:2',
        'po_detail_subtotal' => 'decimal:2',
        'po_detail_taxamount' => 'decimal:2',
        'po_detail_total' => 'decimal:2',
        'po_detail_createdate' => 'datetime',
        'expected_backorder_date' => 'date',
    ];

    /**
     * Boot the model and apply global scope.
     */
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }

    /**
     * Get the purchase order for this item.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_detail_porder_ms', 'porder_id');
    }

    /**
     * Get the item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'po_detail_item', 'item_code');
    }

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('po_detail_status', 1);
    }

    /**
     * Get received quantity for this item
     */
    public function getReceivedQuantityAttribute()
    {
        $po = $this->purchaseOrder;
        if (!$po) return 0;

        return ReceiveOrderItem::whereHas('receiveOrder', function ($q) use ($po) {
            $q->where('rorder_porder_ms', $po->porder_id);
        })
        ->where('ro_detail_item', $this->po_detail_item)
        ->where('ro_detail_status', 1)
        ->sum('ro_detail_quantity');
    }

    /**
     * Get back order quantity
     */
    public function getBackOrderQuantityAttribute()
    {
        return max(0, $this->po_detail_quantity - $this->received_quantity);
    }

    /**
     * Check if fully received
     */
    public function getIsFullyReceivedAttribute()
    {
        return $this->received_quantity >= $this->po_detail_quantity;
    }
}
