<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory, CompanyScope;

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
        'po_detail_unitprice',
        'po_detail_subtotal',
        'po_detail_taxamount',
        'po_detail_total',
        'po_detail_createdate',
        'po_detail_status',
        'po_detail_tax_group',
        'backordered_qty',
        'expected_backorder_date',
        'backorder_status',
        'company_id',
    ];

    protected $casts = [
        'po_detail_unitprice' => 'decimal:2',
        'po_detail_subtotal' => 'decimal:2',
        'po_detail_taxamount' => 'decimal:2',
        'po_detail_total' => 'decimal:2',
        'po_detail_createdate' => 'datetime',
    ];

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
}
