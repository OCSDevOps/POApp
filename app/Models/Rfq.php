<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CompanyScope;

class Rfq extends Model
{
    use HasFactory;

    protected $table = 'rfq_master';
    protected $primaryKey = 'rfq_id';
    public $timestamps = false;

    // Status constants
    const STATUS_DRAFT = 1;
    const STATUS_SENT = 2;
    const STATUS_RECEIVED = 3;
    const STATUS_CONVERTED = 4;
    const STATUS_CANCELLED = 5;

    protected $fillable = [
        'rfq_no',
        'rfq_project_id',
        'rfq_title',
        'rfq_description',
        'rfq_due_date',
        'rfq_status',
        'rfq_created_by',
        'rfq_created_at',
        'rfq_modified_by',
        'rfq_modified_at',
        'company_id',
    ];

    protected $casts = [
        'rfq_due_date' => 'date',
        'rfq_created_at' => 'datetime',
        'rfq_modified_at' => 'datetime',
        'company_id' => 'integer',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
    }

    /**
     * Get the project for this RFQ.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'rfq_project_id', 'proj_id');
    }

    /**
     * Get the suppliers for this RFQ.
     */
    public function suppliers()
    {
        return $this->hasMany(RfqSupplier::class, 'rfqs_rfq_id', 'rfq_id');
    }

    /**
     * Get the items for this RFQ.
     */
    public function items()
    {
        return $this->hasMany(RfqItem::class, 'rfqi_rfq_id', 'rfq_id');
    }

    /**
     * Get the user who created this RFQ.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'rfq_created_by', 'id');
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SENT => 'Sent',
            self::STATUS_RECEIVED => 'Received',
            self::STATUS_CONVERTED => 'Converted',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
        return $statuses[$this->rfq_status] ?? 'Unknown';
    }

    /**
     * Scope for draft RFQs
     */
    public function scopeDraft($query)
    {
        return $query->where('rfq_status', self::STATUS_DRAFT);
    }

    /**
     * Scope for sent RFQs
     */
    public function scopeSent($query)
    {
        return $query->where('rfq_status', self::STATUS_SENT);
    }

    /**
     * Scope for filtering by project
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('rfq_project_id', $projectId);
    }

    /**
     * Scope for overdue RFQs
     */
    public function scopeOverdue($query)
    {
        return $query->where('rfq_due_date', '<', now())
            ->whereNotIn('rfq_status', [self::STATUS_CONVERTED, self::STATUS_CANCELLED]);
    }

    /**
     * Generate next RFQ number
     */
    public static function generateRfqNumber()
    {
        $lastRfq = self::orderBy('rfq_id', 'DESC')->first();
        $nextNumber = $lastRfq ? intval(substr($lastRfq->rfq_no, 3)) + 1 : 1;
        return 'RFQ' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send RFQ to suppliers
     */
    public function send()
    {
        $this->rfq_status = self::STATUS_SENT;
        $this->rfq_modified_at = now();
        $this->save();

        // Update supplier statuses
        $this->suppliers()->update([
            'rfqs_sent_date' => now(),
            'rfqs_status' => 2, // Sent
        ]);

        return $this;
    }

    /**
     * Convert RFQ to Purchase Order
     */
    public function convertToPurchaseOrder($supplierId)
    {
        // Get the selected supplier's quotes
        $rfqSupplier = $this->suppliers()
            ->where('rfqs_supplier_id', $supplierId)
            ->where('rfqs_status', 4) // Selected
            ->first();

        if (!$rfqSupplier) {
            throw new \Exception('Supplier not selected for this RFQ');
        }

        // Create PO from RFQ
        $po = PurchaseOrder::createFromRfq($this, $supplierId);

        // Update RFQ status
        $this->rfq_status = self::STATUS_CONVERTED;
        $this->rfq_modified_at = now();
        $this->save();

        return $po;
    }
}
