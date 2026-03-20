<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use CompanyScope;

    protected $table = 'contract_master';
    protected $primaryKey = 'contract_id';
    public $timestamps = false;

    const STATUS_DRAFT = 1;
    const STATUS_PENDING = 2;
    const STATUS_APPROVED = 3;
    const STATUS_ACTIVE = 4;
    const STATUS_COMPLETED = 5;
    const STATUS_CANCELLED = 6;
    const STATUS_CLOSED = 7;

    protected $fillable = [
        'contract_number',
        'contract_title',
        'contract_description',
        'contract_project_id',
        'contract_supplier_id',
        'contract_cost_code_id',
        'contract_original_value',
        'contract_approved_cos',
        'contract_pending_cos',
        'contract_invoiced_amount',
        'contract_paid_amount',
        'contract_retention_pct',
        'contract_retention_held',
        'contract_retention_released',
        'contract_start_date',
        'contract_end_date',
        'contract_status',
        'contract_scope',
        'contract_terms',
        'contract_created_by',
        'contract_created_at',
        'contract_modified_by',
        'contract_modified_at',
        'company_id',
        'procore_contract_id',
    ];

    protected $casts = [
        'contract_original_value' => 'decimal:2',
        'contract_approved_cos' => 'decimal:2',
        'contract_pending_cos' => 'decimal:2',
        'contract_invoiced_amount' => 'decimal:2',
        'contract_paid_amount' => 'decimal:2',
        'contract_retention_pct' => 'decimal:2',
        'contract_retention_held' => 'decimal:2',
        'contract_retention_released' => 'decimal:2',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'contract_created_at' => 'datetime',
        'contract_modified_at' => 'datetime',
    ];

    // ── Relationships ──

    public function project()
    {
        return $this->belongsTo(Project::class, 'contract_project_id', 'proj_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'contract_supplier_id', 'sup_id');
    }

    public function costCode()
    {
        return $this->belongsTo(CostCode::class, 'contract_cost_code_id', 'cc_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'contract_created_by', 'id');
    }

    public function changeOrders()
    {
        return $this->hasMany(ContractChangeOrder::class, 'contract_id', 'contract_id');
    }

    public function documents()
    {
        return $this->hasMany(ContractDocument::class, 'cdoc_contract_id', 'contract_id');
    }

    public function invoices()
    {
        return $this->hasMany(ContractInvoice::class, 'cinv_contract_id', 'contract_id');
    }

    public function complianceItems()
    {
        return $this->hasMany(SupplierCompliance::class, 'compliance_contract_id', 'contract_id');
    }

    // ── Accessors ──

    public function getRevisedValueAttribute()
    {
        return $this->contract_original_value + $this->contract_approved_cos;
    }

    public function getRemainingToInvoiceAttribute()
    {
        return $this->revised_value - $this->contract_invoiced_amount;
    }

    public function getRemainingToPayAttribute()
    {
        return $this->contract_invoiced_amount - $this->contract_retention_held - $this->contract_paid_amount;
    }

    public function getTotalRetentionBalanceAttribute()
    {
        return $this->contract_retention_held - $this->contract_retention_released;
    }

    public function getCompletionPercentAttribute()
    {
        if ($this->revised_value == 0) return 0;
        return round(($this->contract_invoiced_amount / $this->revised_value) * 100, 2);
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_CLOSED => 'Closed',
        ];
        return $statuses[$this->contract_status] ?? 'Unknown';
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ((int) $this->contract_status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'info',
            self::STATUS_ACTIVE => 'success',
            self::STATUS_COMPLETED => 'primary',
            self::STATUS_CANCELLED => 'danger',
            self::STATUS_CLOSED => 'dark',
            default => 'secondary',
        };
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('contract_status', self::STATUS_ACTIVE);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('contract_project_id', $projectId);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('contract_supplier_id', $supplierId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('contract_status', $status);
    }

    // ── Business Methods ──

    public static function generateContractNumber()
    {
        $year = date('Y');
        $prefix = "SC-{$year}-";
        $last = static::where('contract_number', 'like', $prefix . '%')
            ->orderBy('contract_id', 'desc')->first();
        $next = $last ? intval(substr($last->contract_number, -4)) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function isEditable(): bool
    {
        return in_array($this->contract_status, [self::STATUS_DRAFT, self::STATUS_APPROVED]);
    }

    public function activate($userId = null)
    {
        $this->contract_status = self::STATUS_ACTIVE;
        $this->contract_modified_by = $userId ?? auth()->id();
        $this->contract_modified_at = now();
        $this->save();
        return $this;
    }

    public function complete($userId = null)
    {
        $this->contract_status = self::STATUS_COMPLETED;
        $this->contract_modified_by = $userId ?? auth()->id();
        $this->contract_modified_at = now();
        $this->save();
        return $this;
    }
}
