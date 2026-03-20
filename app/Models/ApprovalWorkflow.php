<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyScope;

class ApprovalWorkflow extends Model
{
    use CompanyScope;
    protected $table = 'approval_workflows';
    protected $primaryKey = 'workflow_id';
    
    protected $fillable = [
        'company_id',
        'workflow_name',
        'workflow_type',
        'approval_level',
        'amount_threshold_min',
        'amount_threshold_max',
        'approver_user_ids',
        'approval_logic',
        'is_active',
        'sort_order',
        'workflow_notes',
        'approver_roles',
        'project_id',
    ];

    protected $casts = [
        'amount_threshold_min' => 'decimal:2',
        'amount_threshold_max' => 'decimal:2',
        'approver_user_ids' => 'array',
        'approver_roles' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company this workflow belongs to.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get approval requests that used this workflow.
     */
    public function approvalRequests()
    {
        return $this->hasMany(ApprovalRequest::class, 'workflow_id', 'workflow_id');
    }

    /**
     * Scope to filter by workflow type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('workflow_type', $type);
    }

    /**
     * Scope to get active workflows.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get workflows for a specific amount.
     */
    public function scopeForAmount($query, $amount)
    {
        return $query->where('amount_threshold_min', '<=', $amount)
            ->where(function ($q) use ($amount) {
                $q->whereNull('amount_threshold_max')
                  ->orWhere('amount_threshold_max', '>=', $amount);
            });
    }

    /**
     * Check if amount falls within this workflow's threshold.
     */
    public function matchesAmount($amount): bool
    {
        if ($amount < $this->amount_threshold_min) {
            return false;
        }
        
        if ($this->amount_threshold_max !== null && $amount > $this->amount_threshold_max) {
            return false;
        }
        
        return true;
    }

    /**
     * Get approvers for this workflow level.
     */
    public function getApprovers()
    {
        if (empty($this->approver_user_ids)) {
            return collect();
        }
        
        return User::whereIn('id', $this->approver_user_ids)->get();
    }

    /**
     * Check if a user is an approver for this workflow.
     */
    public function isApprover($userId): bool
    {
        return in_array($userId, $this->approver_user_ids ?? []);
    }
}
