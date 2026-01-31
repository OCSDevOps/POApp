<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyScope;

class ApprovalRequest extends Model
{
    use CompanyScope;
    protected $table = 'approval_requests';
    protected $primaryKey = 'request_id';
    
    protected $fillable = [
        'company_id',
        'workflow_id',
        'request_type',
        'entity_id',
        'entity_number',
        'request_amount',
        'current_level',
        'required_levels',
        'request_status',
        'requested_by',
        'approval_history',
        'current_approver_id',
        'request_notes',
        'submitted_at',
        'completed_at',
    ];

    protected $casts = [
        'request_amount' => 'decimal:2',
        'approval_history' => 'array',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the workflow this request is using.
     */
    public function workflow()
    {
        return $this->belongsTo(ApprovalWorkflow::class, 'workflow_id', 'workflow_id');
    }

    /**
     * Get the user who requested approval.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'user_id');
    }

    /**
     * Get the current approver.
     */
    public function currentApprover()
    {
        return $this->belongsTo(User::class, 'current_approver_id', 'user_id');
    }

    /**
     * Get the related entity (polymorphic-like lookup).
     */
    public function getEntity()
    {
        return match ($this->request_type) {
            'budget' => Budget::find($this->entity_id),
            'budget_co' => BudgetChangeOrder::find($this->entity_id),
            'po' => PurchaseOrder::find($this->entity_id),
            'po_co' => PoChangeOrder::find($this->entity_id),
            'receive_order' => ReceiveOrder::find($this->entity_id),
            default => null,
        };
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('request_status', $status);
    }

    /**
     * Scope to get pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('request_status', 'pending');
    }

    /**
     * Scope to get requests for a specific approver.
     */
    public function scopeForApprover($query, $approverId)
    {
        return $query->where('current_approver_id', $approverId)
            ->where('request_status', 'pending');
    }

    /**
     * Scope to filter by request type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('request_type', $type);
    }

    /**
     * Add an approval action to history.
     */
    public function addApprovalAction($action, $userId, $userName, $comments = null)
    {
        $history = $this->approval_history ?? [];
        
        $history[] = [
            'action' => $action, // approved, rejected, cancelled
            'user_id' => $userId,
            'user_name' => $userName,
            'level' => $this->current_level,
            'comments' => $comments,
            'timestamp' => now()->toIso8601String(),
        ];
        
        $this->approval_history = $history;
        $this->save();
    }

    /**
     * Check if request is pending.
     */
    public function isPending(): bool
    {
        return $this->request_status === 'pending';
    }

    /**
     * Check if request is approved.
     */
    public function isApproved(): bool
    {
        return $this->request_status === 'approved';
    }

    /**
     * Check if request is rejected.
     */
    public function isRejected(): bool
    {
        return $this->request_status === 'rejected';
    }

    /**
     * Check if more approval levels are required.
     */
    public function needsMoreApprovals(): bool
    {
        return $this->current_level < $this->required_levels;
    }
}
