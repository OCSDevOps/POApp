<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetChangeOrder;
use App\Models\Project;
use App\Models\Budget;
use App\Models\CostCode;
use App\Services\BudgetService;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BudgetChangeOrderController extends Controller
{
    protected $budgetService;
    protected $approvalService;

    public function __construct(
        BudgetService $budgetService,
        ApprovalService $approvalService
    ) {
        $this->budgetService = $budgetService;
        $this->approvalService = $approvalService;
    }

    /**
     * List all budget change orders for a project.
     */
    public function index($projectId)
    {
        $project = Project::findOrFail($projectId);
        
        $changeOrders = BudgetChangeOrder::with([
            'project',
            'costCode',
            'creator',
            'approver',
            'approvalRequest'
        ])
        ->byProject($projectId)
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        return view('admin.budget-change-orders.index', compact(
            'project',
            'changeOrders'
        ));
    }

    /**
     * Show form to create new budget change order.
     */
    public function create($projectId)
    {
        $project = Project::findOrFail($projectId);
        
        // Get budgets for this project
        $budgets = Budget::where('budget_project_id', $projectId)
            ->with('costCode')
            ->get();
        
        // Get cost codes assigned to project
        $costCodes = CostCode::whereIn('cc_id', $budgets->pluck('budget_cost_code_id'))
            ->orderBy('cc_full_code')
            ->get();

        return view('admin.budget-change-orders.create', compact(
            'project',
            'budgets',
            'costCodes'
        ));
    }

    /**
     * Store new budget change order.
     */
    public function store(Request $request, $projectId)
    {
        $request->validate([
            'cost_code_id' => 'required|exists:cost_code_master,cc_id',
            'bco_type' => 'required|in:increase,decrease,transfer',
            'new_budget' => 'required|numeric|min:0',
            'reason' => 'required|string|max:1000',
            'transfer_from_cost_code_id' => 'required_if:bco_type,transfer|exists:cost_code_master,cc_id',
            'transfer_amount' => 'required_if:bco_type,transfer|numeric|min:0',
        ]);

        try {
            $result = $this->budgetService->createBudgetChangeOrder(
                $projectId,
                $request->cost_code_id,
                $request->new_budget,
                $request->bco_type,
                $request->reason,
                session('user_id'),
                $request->transfer_from_cost_code_id,
                $request->transfer_amount
            );

            if ($result['success']) {
                return redirect()
                    ->route('admin.budget-change-orders.show', [
                        'projectId' => $projectId,
                        'id' => $result['change_order']->bco_id
                    ])
                    ->with('success', 'Budget change order created successfully');
            }

            return back()->with('error', $result['message']);
        } catch (\Exception $e) {
            Log::error('BCO creation failed', [
                'project_id' => $projectId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to create budget change order');
        }
    }

    /**
     * Show budget change order details.
     */
    public function show($projectId, $id)
    {
        $changeOrder = BudgetChangeOrder::with([
            'project',
            'budget.costCode',
            'creator',
            'approver',
            'approvalRequest'
        ])->findOrFail($id);

        // Get approval history if exists
        $approvalHistory = null;
        if ($changeOrder->approvalRequest) {
            $approvalHistory = $this->approvalService->getApprovalHistory(
                $changeOrder->approvalRequest->approval_request_id
            );
        }

        return view('admin.budget-change-orders.show', compact(
            'changeOrder',
            'approvalHistory'
        ));
    }

    /**
     * Submit budget change order for approval.
     */
    public function submit($projectId, $id)
    {
        $changeOrder = BudgetChangeOrder::findOrFail($id);

        if (!$changeOrder->canSubmit()) {
            return back()->with('error', 'This change order cannot be submitted');
        }

        try {
            $result = $this->approvalService->submitForApproval(
                'budget_co',
                $changeOrder->bco_id,
                abs($changeOrder->bco_amount),
                session('user_id'),
                $projectId
            );

            if ($result['success']) {
                $changeOrder->bco_status = 'pending_approval';
                $changeOrder->submitted_at = now();
                $changeOrder->save();

                return redirect()
                    ->route('admin.budget-change-orders.show', [
                        'projectId' => $projectId,
                        'id' => $id
                    ])
                    ->with('success', $result['message']);
            }

            return back()->with('error', $result['error'] ?? 'Failed to submit for approval');
        } catch (\Exception $e) {
            Log::error('BCO submission failed', [
                'bco_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to submit change order');
        }
    }

    /**
     * Approve budget change order.
     */
    public function approve(Request $request, $projectId, $id)
    {
        $request->validate([
            'comments' => 'nullable|string|max:500',
        ]);

        $changeOrder = BudgetChangeOrder::with('approvalRequest')->findOrFail($id);

        if (!$changeOrder->approvalRequest) {
            return back()->with('error', 'No approval request found');
        }

        try {
            $result = $this->approvalService->processApproval(
                $changeOrder->approvalRequest->approval_request_id,
                'approve',
                session('user_id'),
                session('user_name'),
                $request->comments
            );

            if ($result['success']) {
                return redirect()
                    ->route('admin.budget-change-orders.show', [
                        'projectId' => $projectId,
                        'id' => $id
                    ])
                    ->with('success', 'Budget change order approved');
            }

            return back()->with('error', $result['message']);
        } catch (\Exception $e) {
            Log::error('BCO approval failed', [
                'bco_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to approve change order');
        }
    }

    /**
     * Reject budget change order.
     */
    public function reject(Request $request, $projectId, $id)
    {
        $request->validate([
            'comments' => 'required|string|max:500',
        ]);

        $changeOrder = BudgetChangeOrder::with('approvalRequest')->findOrFail($id);

        if (!$changeOrder->approvalRequest) {
            return back()->with('error', 'No approval request found');
        }

        try {
            $result = $this->approvalService->processApproval(
                $changeOrder->approvalRequest->approval_request_id,
                'reject',
                session('user_id'),
                session('user_name'),
                $request->comments
            );

            if ($result['success']) {
                return redirect()
                    ->route('admin.budget-change-orders.show', [
                        'projectId' => $projectId,
                        'id' => $id
                    ])
                    ->with('success', 'Budget change order rejected');
            }

            return back()->with('error', $result['message']);
        } catch (\Exception $e) {
            Log::error('BCO rejection failed', [
                'bco_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to reject change order');
        }
    }

    /**
     * Cancel budget change order (before submission).
     */
    public function cancel($projectId, $id)
    {
        $changeOrder = BudgetChangeOrder::findOrFail($id);

        if (!$changeOrder->isEditable()) {
            return back()->with('error', 'This change order cannot be cancelled');
        }

        try {
            $changeOrder->bco_status = 'cancelled';
            $changeOrder->save();

            return redirect()
                ->route('admin.budget-change-orders.index', $projectId)
                ->with('success', 'Budget change order cancelled');
        } catch (\Exception $e) {
            Log::error('BCO cancellation failed', [
                'bco_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to cancel change order');
        }
    }

    /**
     * Get budget details for AJAX.
     */
    public function getBudgetDetails(Request $request, $projectId)
    {
        $request->validate([
            'cost_code_id' => 'required|exists:cost_code_master,cc_id',
        ]);

        $budget = Budget::where('budget_project_id', $projectId)
            ->where('budget_cost_code_id', $request->cost_code_id)
            ->with('costCode')
            ->first();

        if (!$budget) {
            return response()->json(['error' => 'Budget not found'], 404);
        }

        return response()->json([
            'current_budget' => $budget->budget_amount,
            'original_budget' => $budget->budget_original_amount,
            'change_orders_total' => $budget->budget_change_orders_total,
            'committed' => $budget->budget_committed,
            'actual' => $budget->budget_actual,
            'available' => $budget->budget_amount - $budget->budget_committed,
        ]);
    }
}
