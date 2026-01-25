<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    use HasFactory;

    protected $table = 'item_category_tab';
    protected $primaryKey = 'icat_id';
    public $timestamps = false;

    protected $fillable = [
        'icat_name',
        'icat_description',
        'icat_status',
        'icat_created_by',
        'icat_created_at',
    ];

    /**
     * Get the items for the category.
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'item_cat_ms', 'icat_id');
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('icat_status', 1);
    }

    /**
     * Scope for ordering by name
     */
    public function scopeOrderByName($query)
    {
        return $query->orderBy('icat_name', 'ASC');
    }
}
