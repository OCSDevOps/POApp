<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class ContractInvoice extends Model
{
    use CompanyScope;

    protected $table = 'contract_invoices';
    protected $primaryKey = 'cinv_id';
    public $timestamps = false;

    const STATUS_DRAFT = 1;
    const STATUS_SUBMITTED = 2;
    const STATUS_APPROVED = 3;
    const STATUS_PAID = 4;
    const STATUS_PARTIALLY_PAID = 5;
    const STATUS_REJECTED = 6;
    const STATUS_CANCELLED = 0;

    protected $fillable = [
        'cinv_contract_id',
        'cinv_number',
        'cinv_description',
        'cinv_gross_amount',
        'cinv_retention_held',
        'cinv_net_amount',
        'cinv_paid_amount',
        'cinv_invoice_date',
        'cinv_due_date',
        'cinv_paid_date',
        'cinv_period_from',
        'cinv_period_to',
        'cinv_status',
        'cinv_created_by',
        'cinv_created_at',
        'cinv_modified_by',
        'cinv_modified_at',
        'company_id',
    ];

    protected $casts = [
        'cinv_gross_amount' => 'decimal:2',
        'cinv_retention_held' => 'decimal:2',
        'cinv_net_amount' => 'decimal:2',
        'cinv_paid_amount' => 'decimal:2',
        'cinv_invoice_date' => 'date',
        'cinv_due_date' => 'date',
        'cinv_paid_date' => 'date',
        'cinv_period_from' => 'date',
        'cinv_period_to' => 'date',
        'cinv_created_at' => 'datetime',
        'cinv_modified_at' => 'datetime',
    ];

    // ── Relationships ──

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'cinv_contract_id', 'contract_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'cinv_created_by', 'id');
    }

    // ── Accessors ──

    public function getBalanceDueAttribute()
    {
        return $this->cinv_net_amount - $this->cinv_paid_amount;
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PAID => 'Paid',
            self::STATUS_PARTIALLY_PAID => 'Partially Paid',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
        return $statuses[$this->cinv_status] ?? 'Unknown';
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ((int) $this->cinv_status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_SUBMITTED => 'info',
            self::STATUS_APPROVED => 'primary',
            self::STATUS_PAID => 'success',
            self::STATUS_PARTIALLY_PAID => 'warning',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_CANCELLED => 'dark',
            default => 'secondary',
        };
    }

    // ── Scopes ──

    public function scopeByContract($query, $contractId)
    {
        return $query->where('cinv_contract_id', $contractId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('cinv_status', $status);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('cinv_status', [self::STATUS_APPROVED, self::STATUS_PARTIALLY_PAID]);
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('cinv_due_date')
            ->where('cinv_due_date', '<', now())
            ->whereIn('cinv_status', [self::STATUS_APPROVED, self::STATUS_PARTIALLY_PAID]);
    }

    // ── Business Methods ──

    public static function generateInvoiceNumber($contractNumber)
    {
        $prefix = "INV-{$contractNumber}-";
        $last = static::where('cinv_number', 'like', $prefix . '%')
            ->orderBy('cinv_id', 'desc')->first();
        $next = $last ? intval(substr($last->cinv_number, -3)) + 1 : 1;
        return $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
