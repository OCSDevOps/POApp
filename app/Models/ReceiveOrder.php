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
        'rorder_porder_ms',
        'rorder_slip_no',
        'rorder_infoset',
        'rorder_date',
        'rorder_totalitem',
        'rorder_totalamount',
        'rorder_createdate',
        'rorder_createby',
        'rorder_modifydate',
        'rorder_modifyby',
        'rorder_status',
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
        return $this->hasMany(ReceiveOrderItem::class, 'ro_detail_rorder_ms', 'rorder_id');
    }

    /**
     * Scope for active receive orders
     */
    public function scopeActive($query)
    {
        return $query->where('rorder_status', 1);
    }
}
