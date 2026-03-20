<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class SupplierCompliance extends Model
{
    use CompanyScope;

    protected $table = 'supplier_compliance';
    protected $primaryKey = 'compliance_id';
    public $timestamps = false;

    const TYPE_GENERAL_LIABILITY = 'general_liability';
    const TYPE_WORKERS_COMP = 'workers_comp';
    const TYPE_AUTO = 'auto';
    const TYPE_UMBRELLA = 'umbrella';
    const TYPE_LICENSE = 'license';
    const TYPE_W9 = 'w9';
    const TYPE_BOND = 'bond';
    const TYPE_OTHER = 'other';

    const STATUS_CURRENT = 1;
    const STATUS_EXPIRING_SOON = 2;
    const STATUS_EXPIRED = 3;
    const STATUS_INACTIVE = 0;

    protected $fillable = [
        'compliance_supplier_id',
        'compliance_type',
        'compliance_name',
        'compliance_number',
        'compliance_issuer',
        'compliance_amount',
        'compliance_issue_date',
        'compliance_expiry_date',
        'compliance_warning_days',
        'compliance_document_path',
        'compliance_document_name',
        'compliance_status',
        'compliance_required',
        'compliance_contract_id',
        'compliance_notes',
        'compliance_created_by',
        'compliance_created_at',
        'compliance_modified_by',
        'compliance_modified_at',
        'company_id',
    ];

    protected $casts = [
        'compliance_amount' => 'decimal:2',
        'compliance_issue_date' => 'date',
        'compliance_expiry_date' => 'date',
        'compliance_required' => 'boolean',
        'compliance_created_at' => 'datetime',
        'compliance_modified_at' => 'datetime',
    ];

    // ── Relationships ──

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'compliance_supplier_id', 'sup_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'compliance_contract_id', 'contract_id');
    }

    // ── Accessors ──

    public function getIsExpiredAttribute(): bool
    {
        return $this->compliance_expiry_date && $this->compliance_expiry_date->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->compliance_expiry_date) return false;
        return $this->compliance_expiry_date->isFuture()
            && $this->compliance_expiry_date->lte(now()->addDays($this->compliance_warning_days));
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->compliance_expiry_date) return null;
        return (int) now()->startOfDay()->diffInDays($this->compliance_expiry_date->startOfDay(), false);
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            self::STATUS_CURRENT => 'Current',
            self::STATUS_EXPIRING_SOON => 'Expiring Soon',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_INACTIVE => 'Inactive',
        ];
        return $statuses[$this->compliance_status] ?? 'Unknown';
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ((int) $this->compliance_status) {
            self::STATUS_CURRENT => 'success',
            self::STATUS_EXPIRING_SOON => 'warning',
            self::STATUS_EXPIRED => 'danger',
            self::STATUS_INACTIVE => 'secondary',
            default => 'secondary',
        };
    }

    // ── Scopes ──

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('compliance_supplier_id', $supplierId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('compliance_type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('compliance_status', '!=', self::STATUS_INACTIVE);
    }

    public function scopeExpired($query)
    {
        return $query->where('compliance_expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('compliance_expiry_date', '>=', now())
                     ->where('compliance_expiry_date', '<=', now()->addDays($days));
    }

    public function scopeRequired($query)
    {
        return $query->where('compliance_required', 1);
    }

    public function scopeByContract($query, $contractId)
    {
        return $query->where('compliance_contract_id', $contractId);
    }

    // ── Helpers ──

    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_GENERAL_LIABILITY => 'General Liability Insurance',
            self::TYPE_WORKERS_COMP => "Workers' Compensation",
            self::TYPE_AUTO => 'Commercial Auto Insurance',
            self::TYPE_UMBRELLA => 'Umbrella/Excess Liability',
            self::TYPE_LICENSE => 'License/Certification',
            self::TYPE_W9 => 'W-9 Tax Document',
            self::TYPE_BOND => 'Performance/Payment Bond',
            self::TYPE_OTHER => 'Other',
        ];
    }

    public function getTypeTextAttribute()
    {
        return self::getTypeOptions()[$this->compliance_type] ?? 'Other';
    }
}
