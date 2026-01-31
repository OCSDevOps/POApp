<?php

namespace App\Services;

use App\Models\ApprovalWorkflow;
use App\Models\ApprovalRequest;
use App\Models\BudgetChangeOrder;
use App\Models\PoChangeOrder;
use App\Models\PurchaseOrder;
use App\Models\Budget;
use App\Models\ProjectRole;
use App\Models\User;
use App\Notifications\ApprovalPendingNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Approval Workflow Service
 * 
 * Handles:
 * - Routing approval requests based on amount thresholds
 * - Processing approvals/rejections
 * - Multi-level approval workflows
 * - Approval notifications
 */
class ApprovalService
{
    /**
     * Submit an entity for approval with role-based routing.
     * 
     * @param string $entityType (po, budget_co, po_co, receive_order)
     * @param int $entityId
     * @param float $amount
     * @param int $requestedBy User ID
     * @param int $projectId Project ID for role resolution
     */
    public function submitForApproval($entityType, $entityId, $amount, $requestedBy, $projectId): array
    {
        try {
            DB::beginTransaction();
            
            // Find applicable workflows - check project-specific first, then company-wide
            $workflows = ApprovalWorkflow::byType($entityType)
                ->active()
                ->forAmount($amount)
                ->where(function($query) use ($projectId) {
                    $query->where('project_id', $projectId)
                        ->orWhereNull('project_id');
                })
                ->orderBy('project_id', 'DESC') // Project-specific workflows take precedence
                ->orderBy('approval_level')
                ->get();
            
            // Remove duplicate levels if project workflow overrides company workflow
            if ($workflows->count() > 0 && $workflows->first()->project_id) {
                $workflows = $workflows->where('project_id', $projectId);
            }
            
            if ($workflows->isEmpty()) {
                // No approval required - auto-approve
                $this->autoApprove($entityType, $entityId, $requestedBy);
                
                DB::commit();
                return [
                    'success' => true,
                    'auto_approved' => true,
                    'message' => 'No approval workflow required - automatically approved',
                ];
            }
            
            // Get entity number for display
            $entityNumber = $this->getEntityNumber($entityType, $entityId);
            
            // Create approval request
            $request = ApprovalRequest::create([
                'company_id' => session('company_id'),
                'workflow_id' => $workflows->first()->workflow_id,
                'request_type' => $entityType,
                'entity_id' => $entityId,
                'entity_number' => $entityNumber,
                'request_amount' => $amount,
                'current_level' => 1,
                'required_levels' => $workflows->count(),
                'request_status' => 'pending',
                'requested_by' => $requestedBy,
                'submitted_at' => now(),
            ]);
            
            // Update entity status to pending approval
            $this->updateEntityStatus($entityType, $entityId, 'pending_approval');
            
            // Assign to first level approvers using role-based resolution
            $firstWorkflow = $workflows->first();
            $approvers = $this->resolveApproversFromWorkflow($firstWorkflow, $projectId, $amount);
            
            if ($approvers->isNotEmpty()) {
                $request->current_approver_id = $approvers->first()->user_id;
                $request->save();
                
                // Send notifications to approvers
                $approverUsers = $approvers->map->user->filter();
                if ($approverUsers->isNotEmpty()) {
                    Notification::send($approverUsers, new ApprovalPendingNotification(
                        $request,
                        $entityType,
                        $entityNumber,
                        $amount,
                        1 // Level 1
                    ));
                    
                    Log::info('Approval notifications sent', [
                        'request_id' => $request->ar_id,
                        'recipients' => $approverUsers->count(),
                    ]);
                }
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'request' => $request,
                'approvers' => $approvers,
                'message' => 'Submitted for approval - Level 1 of ' . $workflows->count(),
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Approval submission failed', [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Resolve approvers from workflow based on roles or user IDs.
     */
    protected function resolveApproversFromWorkflow(ApprovalWorkflow $workflow, $projectId, $amount)
    {
        // Check if workflow uses role-based approvers
        if ($workflow->approver_roles) {
            $roles = is_string($workflow->approver_roles) 
                ? json_decode($workflow->approver_roles, true) 
                : $workflow->approver_roles;
            
            return $this->getUsersByRoles($projectId, $roles, $amount);
        }
        
        // Fallback to legacy user ID based approvers
        if ($workflow->approver_user_ids) {
            $userIds = is_string($workflow->approver_user_ids) 
                ? json_decode($workflow->approver_user_ids, true) 
                : $workflow->approver_user_ids;
            
            return User::whereIn('user_id', $userIds)->get();
        }
        
        return collect();
    }
    
    /**
     * Get users by their project roles.
     */
    protected function getUsersByRoles($projectId, array $roleNames, $amount)
    {
        $query = ProjectRole::byProject($projectId)
            ->whereIn('role_name', $roleNames)
            ->active()
            ->with('user');
        
        // Filter by approval limit if checking PO/CO approvals
        if ($amount) {
            $query->where(function($q) use ($amount) {
                $q->whereNull('approval_limit') // No limit = can approve any amount
                    ->orWhere('approval_limit', '>=', $amount);
            });
        }
        
        $projectRoles = $query->get();
        
        return $projectRoles->pluck('user')->unique('user_id');
    }

    /**
     * Process an approval action.
     */
    public function processApproval($requestId, $action, $userId, $userName, $comments = null): array
    {
        try {
            DB::beginTransaction();
            
            $request = ApprovalRequest::findOrFail($requestId);
            
            if (!$request->isPending()) {
                throw new \Exception('Approval request is not in pending status');
            }
            
            // Verify user can approve
            $workflow = ApprovalWorkflow::byType($request->request_type)
                ->active()
                ->where('approval_level', $request->current_level)
                ->forAmount($request->request_amount)
                ->first();
            
            if (!$workflow || !$workflow->isApprover($userId)) {
                throw new \Exception('User is not authorized to approve this request');
            }
            
            // Add to approval history
            $request->addApprovalAction($action, $userId, $userName, $comments);
            
            if ($action === 'approved') {
                return $this->handleApproval($request, $workflow, $userId);
            } elseif ($action === 'rejected') {
                return $this->handleRejection($request, $userId, $comments);
            } else {
                throw new \Exception('Invalid approval action');
            }
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Approval processing failed', [
                'request_id' => $requestId,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle approval action.
     */
    private function handleApproval($request, $workflow, $userId): array
    {
        if ($request->needsMoreApprovals()) {
            // Move to next level
            $request->current_level++;
            $request->save();
            
            // Find next level workflow
            $nextWorkflow = ApprovalWorkflow::byType($request->request_type)
                ->active()
                ->where('approval_level', $request->current_level)
                ->forAmount($request->request_amount)
                ->first();
            
            if ($nextWorkflow) {
                $approvers = $nextWorkflow->getApprovers();
                if ($approvers->isNotEmpty()) {
                    $request->current_approver_id = $approvers->first()->user_id;
                    $request->save();
                    
                    // Send notifications to next level approvers
                    // TODO: Notification::send($approvers, new ApprovalRequestNotification($request));
                }
                
                DB::commit();
                return [
                    'success' => true,
                    'next_level' => $request->current_level,
                    'total_levels' => $request->required_levels,
                    'message' => 'Approved - Moved to Level ' . $request->current_level,
                ];
            }
        }
        
        // Final approval - complete the request
        $request->request_status = 'approved';
        $request->completed_at = now();
        $request->save();
        
        // Execute entity-specific approval logic
        $this->executeApproval($request->request_type, $request->entity_id, $userId);
        
        DB::commit();
        return [
            'success' => true,
            'final_approval' => true,
            'message' => 'Request fully approved',
        ];
    }

    /**
     * Handle rejection action.
     */
    private function handleRejection($request, $userId, $comments): array
    {
        $request->request_status = 'rejected';
        $request->completed_at = now();
        $request->save();
        
        // Update entity status
        $this->updateEntityStatus($request->request_type, $request->entity_id, 'rejected', $comments);
        
        DB::commit();
        return [
            'success' => true,
            'message' => 'Request rejected',
        ];
    }

    /**
     * Execute approval logic for specific entity types.
     */
    private function executeApproval($entityType, $entityId, $userId): void
    {
        $budgetService = app(BudgetService::class);
        $poChangeService = app(PoChangeOrderService::class);
        
        switch ($entityType) {
            case 'budget_co':
                $budgetService->approveBudgetChangeOrder($entityId, $userId);
                break;
                
            case 'po_co':
                $poChangeService->approvePoChangeOrder($entityId, $userId);
                break;
                
            case 'po':
                $po = PurchaseOrder::findOrFail($entityId);
                $po->porder_status = 5; // Approved status
                $po->save();
                break;
                
            case 'budget':
                $budget = Budget::findOrFail($entityId);
                // Budget approval logic if needed
                break;
        }
    }

    /**
     * Auto-approve when no workflow exists.
     */
    private function autoApprove($entityType, $entityId, $userId): void
    {
        $this->executeApproval($entityType, $entityId, $userId);
    }

    /**
     * Update entity status to reflect approval state.
     */
    private function updateEntityStatus($entityType, $entityId, $status, $reason = null): void
    {
        switch ($entityType) {
            case 'budget_co':
                $bco = BudgetChangeOrder::findOrFail($entityId);
                $bco->bco_status = $status;
                if ($reason) $bco->rejection_reason = $reason;
                $bco->save();
                break;
                
            case 'po_co':
                $poco = PoChangeOrder::findOrFail($entityId);
                $poco->poco_status = $status;
                if ($reason) $poco->rejection_reason = $reason;
                $poco->save();
                break;
                
            case 'po':
                $po = PurchaseOrder::findOrFail($entityId);
                $po->porder_status = $status === 'pending_approval' ? 4 : ($status === 'approved' ? 5 : 6);
                $po->save();
                break;
        }
    }

    /**
     * Get human-readable entity number.
     */
    private function getEntityNumber($entityType, $entityId): ?string
    {
        return match ($entityType) {
            'budget_co' => BudgetChangeOrder::find($entityId)->bco_number ?? null,
            'po_co' => PoChangeOrder::find($entityId)->poco_number ?? null,
            'po' => PurchaseOrder::find($entityId)->porder_no ?? null,
            default => null,
        };
    }

    /**
     * Get pending approvals for a user.
     */
    public function getPendingApprovalsForUser($userId): array
    {
        $requests = ApprovalRequest::forApprover($userId)
            ->with(['requester'])
            ->orderBy('submitted_at', 'desc')
            ->get();
        
        return [
            'requests' => $requests,
            'count' => $requests->count(),
        ];
    }

    /**
     * Get approval history for an entity.
     */
    public function getApprovalHistory($entityType, $entityId): array
    {
        $request = ApprovalRequest::byType($entityType)
            ->where('entity_id', $entityId)
            ->with(['requester', 'workflow'])
            ->orderBy('created_at', 'desc')
            ->first();
        
        return [
            'request' => $request,
            'history' => $request->approval_history ?? [],
            'current_status' => $request->request_status ?? 'N/A',
        ];
    }
}
