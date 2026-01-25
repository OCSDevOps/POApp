<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierCatalog extends Model
{
    use HasFactory;

    protected $table = 'supplier_catalog_tab';
    protected $primaryKey = 'supcat_id';
    public $timestamps = false;

    protected $fillable = [
        'supcat_supplier',
        'supcat_item_code',
        'supcat_sku_no',
        'supcat_uom',
        'supcat_price',
        'supcat_lastdate',
        'supcat_details',
        'supcat_createdate',
        'supcat_createby',
        'supcat_modifydate',
        'supcat_modifyby',
        'supcat_status',
    ];

    protected $casts = [
        'supcat_price' => 'decimal:2',
        'supcat_lastdate' => 'date',
        'supcat_createdate' => 'datetime',
        'supcat_modifydate' => 'datetime',
    ];

    /**
     * Get the supplier for this catalog item.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supcat_supplier', 'sup_id');
    }

    /**
     * Get the item for this catalog entry.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'supcat_item_code', 'item_code');
    }

    /**
     * Get the unit of measure for this catalog item.
     */
    public function unitOfMeasure()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'supcat_uom', 'uom_id');
    }

    /**
     * Scope for active catalog items
     */
    public function scopeActive($query)
    {
        return $query->where('supcat_status', 1);
    }

    /**
     * Scope for filtering by supplier
     */
    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supcat_supplier', $supplierId);
    }

    /**
     * Scope for filtering by item
     */
    public function scopeByItem($query, $itemCode)
    {
        return $query->where('supcat_item_code', $itemCode);
    }

    /**
     * Get the best price for an item across all suppliers
     */
    public static function getBestPrice($itemCode)
    {
        return self::where('supcat_item_code', $itemCode)
            ->where('supcat_status', 1)
            ->orderBy('supcat_price', 'ASC')
            ->first();
    }
}
