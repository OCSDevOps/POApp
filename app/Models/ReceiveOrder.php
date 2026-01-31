<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiveOrder extends Model
{
    use HasFactory, CompanyScope;

    protected $table = 'receive_order_master';
    protected $primaryKey = 'rorder_id';
    public $timestamps = false;

    protected $fillable = [
        'rorder_no',
        'rorder_porder_ms',
        'rorder_date',
        'rorder_notes',
        'rorder_status',
        'rorder_created_by',
        'rorder_created_at',
        'rorder_modified_by',
        'rorder_modified_at',
        'company_id',
    ];

    /**
     * Get the purchase order associated with the receive order.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'rorder_porder_ms', 'porder_id');
    }

    /**
     * Get the items for the receive order.
     */
    public function items()
    {
        return $this->hasMany(ReceiveOrderItem::class, 'rorder_item_rorder_ms', 'rorder_id');
    }

    /**
     * Scope for active receive orders
     */
    public function scopeActive($query)
    {
        return $query->where('rorder_status', 1);
    }
}
