<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class ContractChangeOrder extends Model
{
    use CompanyScope;

    protected $table = 'contract_change_orders';
    protected $primaryKey = 'cco_id';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'cco_number',
        'contract_id',
        'cco_amount',
        'cco_description',
        'cco_reason',
        'cco_status',
        'submitted_at',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'created_by',
        'company_id',
    ];

    protected $casts = [
        'cco_amount' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ── Relationships ──

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'contract_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function approvalRequest()
    {
        return $this->hasOne(ApprovalRequest::class, 'entity_id', 'cco_id')
            ->where('request_type', 'contract_co');
    }

    // ── Scopes ──

    public function scopeByStatus($query, $status)
    {
        return $query->where('cco_status', $status);
    }

    public function scopePending($query)
    {
        return $query->whereIn('cco_status', ['draft', 'pending_approval']);
    }

    public function scopeApproved($query)
    {
        return $query->where('cco_status', 'approved');
    }

    public function scopeByContract($query, $contractId)
    {
        return $query->where('contract_id', $contractId);
    }

    // ── Accessors ──

    public function getStatusTextAttribute()
    {
        $statuses = [
            'draft' => 'Draft',
            'pending_approval' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
        ];
        return $statuses[$this->cco_status] ?? 'Unknown';
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->cco_status) {
            'draft' => 'secondary',
            'pending_approval' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'dark',
            default => 'secondary',
        };
    }

    // ── Business Methods ──

    public function isEditable(): bool
    {
        return in_array($this->cco_status, ['draft', 'rejected']);
    }

    public function canSubmit(): bool
    {
        return $this->cco_status === 'draft';
    }

    public static function generateCcoNumber(): string
    {
        $year = date('Y');
        $prefix = "CCO-{$year}-";
        $last = static::where('cco_number', 'like', $prefix . '%')
            ->orderBy('cco_id', 'desc')->first();
        $next = $last ? intval(substr($last->cco_number, -4)) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
