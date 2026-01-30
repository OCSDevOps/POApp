<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PoChangeOrder;
use App\Models\PurchaseOrder;
use App\Models\Project;
use App\Services\PoChangeOrderService;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PoChangeOrderController extends Controller
{
    protected $poChangeOrderService;
    protected $approvalService;

    public function __construct(
        PoChangeOrderService $poChangeOrderService,
        ApprovalService $approvalService
    ) {
        $this->poChangeOrderService = $poChangeOrderService;
        $this->approvalService = $approvalService;
    }

    /**
     * List all PO change orders.
     */
    public function index(Request $request)
    {
        $query = PoChangeOrder::with([
            'purchaseOrder.project',
            'purchaseOrder.supplier',
            'creator',
            'approver',
            'approvalRequest'
        ]);

        // Filter by project
        if ($request->project_id) {
            $query->whereHas('purchaseOrder', function($q) use ($request) {
                $q->where('porder_project_ms', $request->project_id);
            });
        }

        // Filter by status
        if ($request->status) {
            $query->byStatus($request->status);
        }

        $changeOrders = $query->orderBy('created_at', 'desc')->paginate(20);

        $projects = Project::active()->orderBy('proj_name')->get();

        return view('admin.po-change-orders.index', compact(
            'changeOrders',
            'projects'
        ));
    }

    /**
     * Show form to create PO change order.
     */
    public function create($poId)
    {
        $purchaseOrder = PurchaseOrder::with([
            'project',
            'supplier',
            'items.item'
        ])->findOrFail($poId);

        return view('admin.po-change-orders.create', compact('purchaseOrder'));
    }

    /**
     * Store new PO change order.
     */
    public function store(Request $request, $poId)
    {
        $request->validate([
            'poco_type' => 'required|in:amount_change,item_change,date_change,other',
            'new_total' => 'required|numeric|min:0',
            'reason' => 'required|string|max:1000',
            'details' => 'nullable|array',
        ]);

        try {
            $result = $this->poChangeOrderService->createPoChangeOrder(
                $poId,
                $request->new_total,
                $request->poco_type,
                $request->reason,
                session('user_id'),
                $request->details
            );

            if ($result['success']) {
                return redirect()
                    ->route('admin.po-change-orders.show', $result['change_order']->poco_id)
                    ->with('success', 'PO change order created successfully');
            }

            return back()->with('error', $result['message']);
        } catch (\Exception $e) {
            Log::error('PCO creation failed', [
                'po_id' => $poId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to create PO change order');
        }
    }

    /**
     * Show PO change order details.
     */
    public function show($id)
    {
        $changeOrder = PoChangeOrder::with([
            'purchaseOrder.project',
            'purchaseOrder.supplier',
            'purchaseOrder.items.item',
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

        return view('admin.po-change-orders.show', compact(
            'changeOrder',
            'approvalHistory'
        ));
    }

    /**
     * Submit PO change order for approval.
     */
    public function submit($id)
    {
        $changeOrder = PoChangeOrder::with('purchaseOrder')->findOrFail($id);

        if (!$changeOrder->canSubmit()) {
            return back()->with('error', 'This change order cannot be submitted');
        }

        try {
            // Validate budget if amount is increasing
            if ($changeOrder->poco_amount > 0) {
                $validation = $this->poChangeOrderService->validatePoChangeOrder(
                    $changeOrder->poco_id
                );

                if (!$validation['valid'] && !$validation['can_override']) {
                    return back()->with('error', $validation['message']);
                }
            }

            // Submit for approval
            $result = $this->approvalService->submitForApproval(
                'po_co',
                $changeOrder->poco_id,
                abs($changeOrder->poco_amount),
                session('user_id'),
                $changeOrder->purchaseOrder->porder_project_ms
            );

            if ($result['success']) {
                $changeOrder->poco_status = 'pending_approval';
                $changeOrder->submitted_at = now();
                $changeOrder->save();

                return redirect()
                    ->route('admin.po-change-orders.show', $id)
                    ->with('success', $result['message']);
            }

            return back()->with('error', $result['error'] ?? 'Failed to submit for approval');
        } catch (\Exception $e) {
            Log::error('PCO submission failed', [
                'poco_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to submit change order');
        }
    }

    /**
     * Approve PO change order.
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'comments' => 'nullable|string|max:500',
        ]);

        $changeOrder = PoChangeOrder::with('approvalRequest')->findOrFail($id);

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
                    ->route('admin.po-change-orders.show', $id)
                    ->with('success', 'PO change order approved');
            }

            return back()->with('error', $result['message']);
        } catch (\Exception $e) {
            Log::error('PCO approval failed', [
                'poco_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to approve change order');
        }
    }

    /**
     * Reject PO change order.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'comments' => 'required|string|max:500',
        ]);

        $changeOrder = PoChangeOrder::with('approvalRequest')->findOrFail($id);

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
                    ->route('admin.po-change-orders.show', $id)
                    ->with('success', 'PO change order rejected');
            }

            return back()->with('error', $result['message']);
        } catch (\Exception $e) {
            Log::error('PCO rejection failed', [
                'poco_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to reject change order');
        }
    }

    /**
     * Cancel PO change order (before submission).
     */
    public function cancel($id)
    {
        $changeOrder = PoChangeOrder::findOrFail($id);

        if (!$changeOrder->isEditable()) {
            return back()->with('error', 'This change order cannot be cancelled');
        }

        try {
            $changeOrder->poco_status = 'cancelled';
            $changeOrder->save();

            return redirect()
                ->route('admin.po-change-orders.index')
                ->with('success', 'PO change order cancelled');
        } catch (\Exception $e) {
            Log::error('PCO cancellation failed', [
                'poco_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to cancel change order');
        }
    }

    /**
     * Check budget availability for PO change.
     */
    public function checkBudgetAvailability($id)
    {
        try {
            $validation = $this->poChangeOrderService->validatePoChangeOrder($id);
            
            return response()->json($validation);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Failed to check budget availability',
            ], 500);
        }
    }
}
