<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CompanyScope;

class BudgetChangeOrder extends Model
{
    protected $table = 'budget_change_orders';
    protected $primaryKey = 'bco_id';
    
    protected $fillable = [
        'company_id',
        'bco_number',
        'budget_id',
        'project_id',
        'cost_code_id',
        'bco_type',
        'bco_amount',
        'previous_budget',
        'new_budget',
        'bco_reason',
        'bco_notes',
        'bco_reference',
        'bco_status',
        'created_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'bco_amount' => 'decimal:2',
        'previous_budget' => 'decimal:2',
        'new_budget' => 'decimal:2',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model and apply global scope.
     */
    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
        
        // Auto-generate BCO number on creation
        static::creating(function ($model) {
            if (empty($model->bco_number)) {
                $model->bco_number = static::generateBcoNumber();
            }
        });
    }

    /**
     * Get the budget this change order belongs to.
     */
    public function budget()
    {
        return $this->belongsTo(Budget::class, 'budget_id', 'budget_id');
    }

    /**
     * Get the project this change order belongs to.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'proj_id');
    }

    /**
     * Get the cost code for this change order.
     */
    public function costCode()
    {
        return $this->belongsTo(CostCode::class, 'cost_code_id', 'cc_id');
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
        return $this->hasOne(ApprovalRequest::class, 'entity_id', 'bco_id')
            ->where('request_type', 'budget_co');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('bco_status', $status);
    }

    /**
     * Scope to filter by project.
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope to get pending change orders.
     */
    public function scopePending($query)
    {
        return $query->whereIn('bco_status', ['draft', 'pending_approval']);
    }

    /**
     * Scope to get approved change orders.
     */
    public function scopeApproved($query)
    {
        return $query->where('bco_status', 'approved');
    }

    /**
     * Check if change order can be edited.
     */
    public function isEditable(): bool
    {
        return in_array($this->bco_status, ['draft', 'rejected']);
    }

    /**
     * Check if change order can be submitted for approval.
     */
    public function canSubmit(): bool
    {
        return $this->bco_status === 'draft';
    }

    /**
     * Check if change order is approved.
     */
    public function isApproved(): bool
    {
        return $this->bco_status === 'approved';
    }

    /**
     * Generate unique BCO number.
     */
    public static function generateBcoNumber(): string
    {
        $year = date('Y');
        $prefix = "BCO-{$year}-";
        
        $lastBco = static::where('bco_number', 'like', $prefix . '%')
            ->orderBy('bco_id', 'desc')
            ->first();
        
        if ($lastBco) {
            $lastNumber = intval(substr($lastBco->bco_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
