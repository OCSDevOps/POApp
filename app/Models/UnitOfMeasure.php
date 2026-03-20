<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitOfMeasure extends Model
{
    use HasFactory;

    protected $table = 'unit_of_measure_tab';
    protected $primaryKey = 'uom_id';
    public $timestamps = false;

    protected $fillable = [
        'uom_name',
        'uom_detail',
        'uom_createdate',
        'uom_createby',
        'uom_modifydate',
        'uom_status',
    ];

    /**
     * Get the items using this unit of measure.
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'item_unit_ms', 'uom_id');
    }

    /**
     * Get the supplier catalog items using this unit of measure.
     */
    public function supplierCatalogItems()
    {
        return $this->hasMany(SupplierCatalog::class, 'supcat_uom', 'uom_id');
    }

    /**
     * Scope for active units of measure
     */
    public function scopeActive($query)
    {
        return $query->where('uom_status', 1);
    }

    /**
     * Scope for ordering by name
     */
    public function scopeOrderByName($query)
    {
        return $query->orderBy('uom_name', 'ASC');
    }
}
