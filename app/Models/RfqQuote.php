<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfqQuote extends Model
{
    use HasFactory, CompanyScope;

    protected $table = 'rfq_quotes';
    protected $primaryKey = 'rfqq_id';
    public $timestamps = false;

    protected $fillable = [
        'rfqq_rfqs_id',
        'rfqq_rfqi_id',
        'rfqq_quoted_price',
        'rfqq_lead_time_days',
        'rfqq_valid_until',
        'rfqq_notes',
        'rfqq_created_at',
        'company_id',
    ];

    protected $casts = [
        'rfqq_quoted_price' => 'decimal:2',
        'rfqq_valid_until' => 'date',
        'rfqq_created_at' => 'datetime',
        'company_id' => 'integer',
    ];

    /**
     * Get the RFQ supplier entry.
     */
    public function rfqSupplier()
    {
        return $this->belongsTo(RfqSupplier::class, 'rfqq_rfqs_id', 'rfqs_id');
    }

    /**
     * Get the RFQ item.
     */
    public function rfqItem()
    {
        return $this->belongsTo(RfqItem::class, 'rfqq_rfqi_id', 'rfqi_id');
    }

    /**
     * Check if quote is still valid
     */
    public function getIsValidAttribute()
    {
        if (!$this->rfqq_valid_until) return true;
        return $this->rfqq_valid_until >= now()->toDateString();
    }

    /**
     * Get total amount for this quote
     */
    public function getTotalAmountAttribute()
    {
        $rfqItem = $this->rfqItem;
        return $rfqItem ? $this->rfqq_quoted_price * $rfqItem->rfqi_quantity : 0;
    }

    /**
     * Get variance from target price
     */
    public function getVarianceFromTargetAttribute()
    {
        $rfqItem = $this->rfqItem;
        if (!$rfqItem || !$rfqItem->rfqi_target_price) return null;
        return $this->rfqq_quoted_price - $rfqItem->rfqi_target_price;
    }

    /**
     * Get variance percentage from target price
     */
    public function getVariancePercentAttribute()
    {
        $rfqItem = $this->rfqItem;
        if (!$rfqItem || !$rfqItem->rfqi_target_price || $rfqItem->rfqi_target_price == 0) return null;
        return round((($this->rfqq_quoted_price - $rfqItem->rfqi_target_price) / $rfqItem->rfqi_target_price) * 100, 2);
    }
}
