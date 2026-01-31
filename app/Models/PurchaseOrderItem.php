<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory, CompanyScope;

    protected $table = 'purchase_order_items';
    protected $primaryKey = 'porder_item_id';
    public $timestamps = false;

    protected $fillable = [
        'porder_item_porder_ms',
        'porder_item_code',
        'porder_item_name',
        'porder_item_qty',
        'porder_item_price',
        'porder_item_tax',
        'porder_item_total',
        'porder_item_ccode',
        'company_id',
    ];

    protected $casts = [
        'porder_item_price' => 'decimal:2',
        'porder_item_tax' => 'decimal:2',
        'porder_item_total' => 'decimal:2',
    ];

    /**
     * Get the purchase order for this item.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'porder_item_porder_ms', 'porder_id');
    }

    /**
     * Get the item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'porder_item_code', 'item_code');
    }
}
