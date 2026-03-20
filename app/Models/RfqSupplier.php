<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfqSupplier extends Model
{
    use HasFactory;

    protected $table = 'rfq_suppliers';
    protected $primaryKey = 'rfqs_id';
    public $timestamps = false;

    // Status constants
    const STATUS_PENDING = 1;
    const STATUS_SENT = 2;
    const STATUS_RESPONDED = 3;
    const STATUS_SELECTED = 4;
    const STATUS_REJECTED = 5;

    protected $fillable = [
        'rfqs_rfq_id',
        'rfqs_supplier_id',
        'rfqs_sent_date',
        'rfqs_response_date',
        'rfqs_status',
        'rfqs_notes',
        'rfqs_created_at',
    ];

    protected $casts = [
        'rfqs_sent_date' => 'datetime',
        'rfqs_response_date' => 'datetime',
        'rfqs_created_at' => 'datetime',
    ];

    /**
     * Get the RFQ for this supplier entry.
     */
    public function rfq()
    {
        return $this->belongsTo(Rfq::class, 'rfqs_rfq_id', 'rfq_id');
    }

    /**
     * Get the supplier.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'rfqs_supplier_id', 'sup_id');
    }

    /**
     * Get the quotes from this supplier.
     */
    public function quotes()
    {
        return $this->hasMany(RfqQuote::class, 'rfqq_rfqs_id', 'rfqs_id');
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SENT => 'Sent',
            self::STATUS_RESPONDED => 'Responded',
            self::STATUS_SELECTED => 'Selected',
            self::STATUS_REJECTED => 'Rejected',
        ];
        return $statuses[$this->rfqs_status] ?? 'Unknown';
    }

    /**
     * Mark as responded
     */
    public function markAsResponded()
    {
        $this->rfqs_status = self::STATUS_RESPONDED;
        $this->rfqs_response_date = now();
        $this->save();
        return $this;
    }

    /**
     * Select this supplier
     */
    public function select()
    {
        // Reject all other suppliers for this RFQ
        self::where('rfqs_rfq_id', $this->rfqs_rfq_id)
            ->where('rfqs_id', '!=', $this->rfqs_id)
            ->update(['rfqs_status' => self::STATUS_REJECTED]);

        $this->rfqs_status = self::STATUS_SELECTED;
        $this->save();
        return $this;
    }

    /**
     * Scope for responded suppliers
     */
    public function scopeResponded($query)
    {
        return $query->where('rfqs_status', self::STATUS_RESPONDED);
    }

    /**
     * Scope for selected suppliers
     */
    public function scopeSelected($query)
    {
        return $query->where('rfqs_status', self::STATUS_SELECTED);
    }

    /**
     * Get total quoted amount
     */
    public function getTotalQuotedAmountAttribute()
    {
        return $this->quotes->sum(function ($quote) {
            $rfqItem = RfqItem::find($quote->rfqq_rfqi_id);
            return $quote->rfqq_quoted_price * ($rfqItem ? $rfqItem->rfqi_quantity : 0);
        });
    }
}
