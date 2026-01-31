<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyScope;

class PoChangeOrder extends Model
{
    use CompanyScope;
    protected $table = 'po_change_orders';
    protected $primaryKey = 'poco_id';
    
    protected $fillable = [
        'company_id',
        'poco_number',
        'purchase_order_id',
        'poco_type',
        'poco_amount',
        'previous_total',
        'new_total',
        'poco_description',
        'poco_notes',
        'poco_reference',
        'poco_details',
        'poco_status',
        'created_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'poco_amount' => 'decimal:2',
        'previous_total' => 'decimal:2',
        'new_total' => 'decimal:2',
        'poco_details' => 'array',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        // Auto-generate PCO number on creation
        static::creating(function ($model) {
            if (empty($model->poco_number)) {
                $model->poco_number = static::generatePocoNumber();
            }
        });
    }

    /**
     * Get the purchase order this change order belongs to.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'porder_id');
    }

    /**
     * Get the user who created this change order.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Get the user who approved this change order.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    /**
     * Get the approval request for this change order.
     */
    public function approvalRequest()
    {
        return $this->hasOne(ApprovalRequest::class, 'entity_id', 'poco_id')
            ->where('request_type', 'po_co');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('poco_status', $status);
    }

    /**
     * Scope to filter by purchase order.
     */
    public function scopeByPurchaseOrder($query, $poId)
    {
        return $query->where('purchase_order_id', $poId);
    }

    /**
     * Scope to get pending change orders.
     */
    public function scopePending($query)
    {
        return $query->whereIn('poco_status', ['draft', 'pending_approval']);
    }

    /**
     * Scope to get approved change orders.
     */
    public function scopeApproved($query)
    {
        return $query->where('poco_status', 'approved');
    }

    /**
     * Check if change order can be edited.
     */
    public function isEditable(): bool
    {
        return in_array($this->poco_status, ['draft', 'rejected']);
    }

    /**
     * Check if change order can be submitted for approval.
     */
    public function canSubmit(): bool
    {
        return $this->poco_status === 'draft';
    }

    /**
     * Check if change order is approved.
     */
    public function isApproved(): bool
    {
        return $this->poco_status === 'approved';
    }

    /**
     * Generate unique PCO number.
     */
    public static function generatePocoNumber(): string
    {
        $year = date('Y');
        $prefix = "PCO-{$year}-";
        
        $lastPoco = static::where('poco_number', 'like', $prefix . '%')
            ->orderBy('poco_id', 'desc')
            ->first();
        
        if ($lastPoco) {
            $lastNumber = intval(substr($lastPoco->poco_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
