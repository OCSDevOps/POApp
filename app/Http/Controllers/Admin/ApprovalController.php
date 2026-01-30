<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRequest;
use App\Models\BudgetChangeOrder;
use App\Models\PoChangeOrder;
use App\Models\PurchaseOrder;
use App\Models\ProjectRole;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalController extends Controller
{
    protected $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    /**
     * Show approval dashboard with pending requests.
     */
    public function dashboard(Request $request)
    {
        $userId = session('user_id');
        
        // Get pending approvals for current user
        $query = ApprovalRequest::with([
            'workflow',
            'requester',
            'currentApprover'
        ])
        ->pending()
        ->where(function($q) use ($userId) {
            $q->where('current_approver_id', $userId)
                ->orWhereHas('workflow', function($wq) use ($userId) {
                    // Check if user is in approver list
                    $wq->where('approver_user_ids', 'LIKE', '%"' . $userId . '"%');
                });
        });

        // Filter by type
        if ($request->type) {
            $query->byType($request->type);
        }

        // Order by priority (amount DESC) and date
        $pendingApprovals = $query
            ->orderBy('request_amount', 'desc')
            ->orderBy('submitted_at', 'asc')
            ->paginate(20);

        // Get approval counts by type
        $counts = ApprovalRequest::pending()
            ->where(function($q) use ($userId) {
                $q->where('current_approver_id', $userId);
            })
            ->select('request_type', DB::raw('COUNT(*) as count'))
            ->groupBy('request_type')
            ->pluck('count', 'request_type');

        return view('admin.approvals.dashboard', compact(
            'pendingApprovals',
            'counts'
        ));
    }

    /**
     * Show approval request details.
     */
    public function show($id)
    {
        $approvalRequest = ApprovalRequest::with([
            'workflow',
            'requester',
            'currentApprover'
        ])->findOrFail($id);

        // Get entity details
        $entity = $approvalRequest->getEntity();
        
        // Get approval history
        $history = $this->approvalService->getApprovalHistory($id);

        // Check if current user can approve
        $canApprove = $this->canUserApprove($approvalRequest, session('user_id'));

        return view('admin.approvals.show', compact(
            'approvalRequest',
            'entity',
            'history',
            'canApprove'
        ));
    }

    /**
     * Approve a request.
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'comments' => 'nullable|string|max:500',
        ]);

        try {
            $result = $this->approvalService->processApproval(
                $id,
                'approve',
                session('user_id'),
                session('user_name'),
                $request->comments
            );

            if ($result['success']) {
                return redirect()
                    ->route('admin.approvals.dashboard')
                    ->with('success', 'Request approved successfully');
            }

            return back()->with('error', $result['message']);
        } catch (\Exception $e) {
            Log::error('Approval processing failed', [
                'approval_request_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to process approval');
        }
    }

    /**
     * Reject a request.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'comments' => 'required|string|max:500',
        ]);

        try {
            $result = $this->approvalService->processApproval(
                $id,
                'reject',
                session('user_id'),
                session('user_name'),
                $request->comments
            );

            if ($result['success']) {
                return redirect()
                    ->route('admin.approvals.dashboard')
                    ->with('success', 'Request rejected');
            }

            return back()->with('error', $result['message']);
        } catch (\Exception $e) {
            Log::error('Rejection processing failed', [
                'approval_request_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to process rejection');
        }
    }

    /**
     * Override budget restrictions and approve.
     */
    public function override(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
            'comments' => 'nullable|string|max:500',
        ]);

        try {
            $approvalRequest = ApprovalRequest::findOrFail($id);
            
            // Check if user has override permission
            if (!$this->canUserOverride(session('user_id'), $approvalRequest)) {
                return back()->with('error', 'You do not have permission to override budget restrictions');
            }

            // Record override
            $approvalRequest->override_by = session('user_id');
            $approvalRequest->override_reason = $request->reason;
            $approvalRequest->override_at = now();
            $approvalRequest->save();

            // Process approval
            $result = $this->approvalService->processApproval(
                $id,
                'approve',
                session('user_id'),
                session('user_name'),
                $request->comments . ' [BUDGET OVERRIDE: ' . $request->reason . ']'
            );

            if ($result['success']) {
                return redirect()
                    ->route('admin.approvals.dashboard')
                    ->with('success', 'Budget override approved successfully');
            }

            return back()->with('error', $result['message']);
        } catch (\Exception $e) {
            Log::error('Override processing failed', [
                'approval_request_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to process override');
        }
    }

    /**
     * Get approval history for an entity.
     */
    public function getHistory(Request $request)
    {
        $request->validate([
            'entity_type' => 'required|in:po,budget_co,po_co,receive_order',
            'entity_id' => 'required|integer',
        ]);

        $approvalRequests = ApprovalRequest::byType($request->entity_type)
            ->where('entity_id', $request->entity_id)
            ->with(['requester', 'currentApprover'])
            ->orderBy('created_at', 'desc')
            ->get();

        $history = [];
        foreach ($approvalRequests as $approvalRequest) {
            $history[] = [
                'request' => $approvalRequest,
                'actions' => $this->approvalService->getApprovalHistory($approvalRequest->approval_request_id),
            ];
        }

        return response()->json($history);
    }

    /**
     * Check if user can approve a request.
     */
    protected function canUserApprove(ApprovalRequest $approvalRequest, $userId): bool
    {
        // Check if user is current approver
        if ($approvalRequest->current_approver_id == $userId) {
            return true;
        }

        // Check if user is in workflow approver list
        $workflow = $approvalRequest->workflow;
        if ($workflow && $workflow->approver_user_ids) {
            $approverIds = is_string($workflow->approver_user_ids) 
                ? json_decode($workflow->approver_user_ids, true) 
                : $workflow->approver_user_ids;
            
            if (in_array($userId, $approverIds)) {
                return true;
            }
        }

        // Check if user has appropriate role in project
        if ($approvalRequest->request_type == 'po' || $approvalRequest->request_type == 'po_co') {
            $po = PurchaseOrder::find($approvalRequest->entity_id);
            if ($po) {
                $projectRole = ProjectRole::byProject($po->porder_project_ms)
                    ->byUser($userId)
                    ->active()
                    ->canApprovePo()
                    ->first();
                
                if ($projectRole && $projectRole->canApproveAmount($approvalRequest->request_amount)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if user can override budget restrictions.
     */
    protected function canUserOverride($userId, ApprovalRequest $approvalRequest): bool
    {
        // Check user type (Admin = 1)
        if (session('u_type') == 1) {
            return true;
        }

        // Check if user has Finance or Executive role in the project
        $projectId = null;
        
        if ($approvalRequest->request_type == 'budget_co') {
            $bco = BudgetChangeOrder::find($approvalRequest->entity_id);
            $projectId = $bco ? $bco->bco_project_ms : null;
        } elseif (in_array($approvalRequest->request_type, ['po', 'po_co'])) {
            $entity = $approvalRequest->request_type == 'po' 
                ? PurchaseOrder::find($approvalRequest->entity_id)
                : PoChangeOrder::find($approvalRequest->entity_id)->purchaseOrder ?? null;
            $projectId = $entity ? $entity->porder_project_ms : null;
        }

        if ($projectId) {
            $hasOverrideRole = ProjectRole::byProject($projectId)
                ->byUser($userId)
                ->active()
                ->canOverrideBudget()
                ->whereIn('role_name', [
                    ProjectRole::ROLE_FINANCE,
                    ProjectRole::ROLE_EXECUTIVE,
                    ProjectRole::ROLE_ADMIN
                ])
                ->exists();
            
            return $hasOverrideRole;
        }

        return false;
    }

    /**
     * Get approval statistics for dashboard.
     */
    public function getStatistics()
    {
        $userId = session('user_id');

        $stats = [
            'pending_count' => ApprovalRequest::pending()
                ->where('current_approver_id', $userId)
                ->count(),
            
            'pending_high_value' => ApprovalRequest::pending()
                ->where('current_approver_id', $userId)
                ->where('request_amount', '>=', 25000)
                ->count(),
            
            'approved_this_month' => ApprovalRequest::where('current_approver_id', $userId)
                ->where('request_status', 'approved')
                ->whereMonth('approved_at', now()->month)
                ->count(),
            
            'rejected_this_month' => ApprovalRequest::where('current_approver_id', $userId)
                ->where('request_status', 'rejected')
                ->whereMonth('approved_at', now()->month)
                ->count(),
        ];

        return response()->json($stats);
    }
}
