<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfqItem extends Model
{
    use HasFactory, CompanyScope;

    protected $table = 'rfq_items';
    protected $primaryKey = 'rfqi_id';
    public $timestamps = false;

    protected $fillable = [
        'rfqi_rfq_id',
        'rfqi_item_id',
        'rfqi_quantity',
        'rfqi_uom_id',
        'rfqi_target_price',
        'rfqi_notes',
        'rfqi_created_at',
        'project_id',
        'company_id',
    ];

    protected $casts = [
        'rfqi_target_price' => 'decimal:2',
        'rfqi_created_at' => 'datetime',
        'company_id' => 'integer',
    ];

    /**
     * Get the RFQ for this item.
     */
    public function rfq()
    {
        return $this->belongsTo(Rfq::class, 'rfqi_rfq_id', 'rfq_id');
    }

    /**
     * Get the item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'rfqi_item_id', 'item_id');
    }

    /**
     * Get the unit of measure.
     */
    public function unitOfMeasure()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'rfqi_uom_id', 'uom_id');
    }

    /**
     * Get the quotes for this item.
     */
    public function quotes()
    {
        return $this->hasMany(RfqQuote::class, 'rfqq_rfqi_id', 'rfqi_id');
    }

    /**
     * Get the best quote for this item
     */
    public function getBestQuoteAttribute()
    {
        return $this->quotes()->orderBy('rfqq_quoted_price', 'ASC')->first();
    }

    /**
     * Get target total
     */
    public function getTargetTotalAttribute()
    {
        return $this->rfqi_quantity * ($this->rfqi_target_price ?? 0);
    }
}
