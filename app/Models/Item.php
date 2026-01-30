<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'item_master';
    protected $primaryKey = 'item_id';
    public $timestamps = false;

    protected $fillable = [
        'item_code',
        'item_name',
        'item_description',
        'item_cat_ms',
        'item_ccode_ms',
        'item_uom_ms',
        'item_price',
        'item_is_rentable',
        'item_status',
        'item_created_by',
        'item_created_at',
        'item_modified_by',
        'item_modified_at',
        'company_id',
    ];

    /**
     * Boot method to apply global scope.
     * Temporarily disabled during initial migration.
     */
    // protected static function booted()
    // {
    //     static::addGlobalScope(new CompanyScope);
    // }

    /**
     * Get the company that owns the item.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * Get the category for the item.
     */
    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'item_cat_ms', 'icat_id');
    }

    /**
     * Get the cost code for the item.
     */
    public function costCode()
    {
        return $this->belongsTo(CostCode::class, 'item_ccode_ms', 'cc_id');
    }

    /**
     * Get the unit of measure for the item.
     */
    public function unitOfMeasure()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'item_uom_ms', 'uom_id');
    }

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('item_status', 1);
    }

    /**
     * Scope for non-rentable items
     */
    public function scopeNonRentable($query)
    {
        return $query->where('item_is_rentable', 0);
    }

    /**
     * Scope for rentable items
     */
    public function scopeRentable($query)
    {
        return $query->where('item_is_rentable', 1);
    }

    /**
     * Scope for ordering by name
     */
    public function scopeOrderByName($query)
    {
        return $query->orderBy('item_name', 'ASC');
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        if ($categoryId) {
            return $query->where('item_cat_ms', $categoryId);
        }
        return $query;
    }

    /**
     * Scope for filtering by cost code
     */
    public function scopeByCostCode($query, $costCodeId)
    {
        if ($costCodeId) {
            return $query->where('item_ccode_ms', $costCodeId);
        }
        return $query;
    }
}
