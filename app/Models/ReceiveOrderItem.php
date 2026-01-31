<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiveOrderItem extends Model
{
    use HasFactory;

    protected $table = 'receive_order_details';
    protected $primaryKey = 'ro_detail_id';
    public $timestamps = false;

    protected $fillable = [
        'ro_detail_rorder_ms',
        'ro_detail_item',
        'ro_detail_quantity',
        'ro_detail_createdate',
        'ro_detail_status',
        'company_id',
    ];

    protected $casts = [
        'ro_detail_createdate' => 'datetime',
    ];

    /**
     * Boot the model and apply global scope.
     */
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }

    /**
     * Get the receive order for this item.
     */
    public function receiveOrder()
    {
        return $this->belongsTo(ReceiveOrder::class, 'ro_detail_rorder_ms', 'rorder_id');
    }

    /**
     * Get the item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'ro_detail_item', 'item_code');
    }

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('ro_detail_status', 1);
    }
}
