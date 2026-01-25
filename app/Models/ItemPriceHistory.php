<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPriceHistory extends Model
{
    use HasFactory;

    protected $table = 'item_price_history';
    protected $primaryKey = 'iph_id';
    public $timestamps = false;

    protected $fillable = [
        'iph_item_id',
        'iph_supplier_id',
        'iph_old_price',
        'iph_new_price',
        'iph_effective_date',
        'iph_notes',
        'iph_created_by',
        'iph_created_at',
    ];

    protected $casts = [
        'iph_old_price' => 'decimal:2',
        'iph_new_price' => 'decimal:2',
        'iph_effective_date' => 'date',
        'iph_created_at' => 'datetime',
    ];

    /**
     * Get the item for this price history.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'iph_item_id', 'item_id');
    }

    /**
     * Get the supplier for this price history.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'iph_supplier_id', 'sup_id');
    }

    /**
     * Get the user who created this record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'iph_created_by', 'id');
    }

    /**
     * Calculate price change percentage
     */
    public function getPriceChangePercentAttribute()
    {
        if ($this->iph_old_price == 0) return 0;
        return round((($this->iph_new_price - $this->iph_old_price) / $this->iph_old_price) * 100, 2);
    }

    /**
     * Scope for filtering by item
     */
    public function scopeByItem($query, $itemId)
    {
        return $query->where('iph_item_id', $itemId);
    }

    /**
     * Scope for filtering by supplier
     */
    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('iph_supplier_id', $supplierId);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('iph_effective_date', [$startDate, $endDate]);
    }

    /**
     * Record a price change
     */
    public static function recordPriceChange($itemId, $supplierId, $oldPrice, $newPrice, $effectiveDate, $notes = null, $createdBy = null)
    {
        return self::create([
            'iph_item_id' => $itemId,
            'iph_supplier_id' => $supplierId,
            'iph_old_price' => $oldPrice,
            'iph_new_price' => $newPrice,
            'iph_effective_date' => $effectiveDate,
            'iph_notes' => $notes,
            'iph_created_by' => $createdBy ?? auth()->id(),
            'iph_created_at' => now(),
        ]);
    }
}
