<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractChangeOrder;
use App\Models\Project;
use App\Services\ContractService;
use App\Services\ApprovalService;
use Illuminate\Http\Request;

class ContractChangeOrderController extends Controller
{
    protected $contractService;
    protected $approvalService;

    public function __construct(ContractService $contractService, ApprovalService $approvalService)
    {
        $this->contractService = $contractService;
        $this->approvalService = $approvalService;
    }

    /**
     * List all contract change orders.
     */
    public function index(Request $request)
    {
        $query = ContractChangeOrder::with(['contract.project', 'contract.supplier', 'creator', 'approver']);

        if ($request->contract_id) {
            $query->byContract($request->contract_id);
        }
        if ($request->status) {
            $query->byStatus($request->status);
        }

        $changeOrders = $query->orderBy('created_at', 'desc')->get();
        $contracts = Contract::orderBy('contract_number')->get();

        return view('admin.contract-change-orders.index', compact('changeOrders', 'contracts'));
    }

    /**
     * Show create form.
     */
    public function create($contractId)
    {
        $contract = Contract::with(['project', 'supplier'])->findOrFail($contractId);

        return view('admin.contract-change-orders.create', compact('contract'));
    }

    /**
     * Store a new CCO.
     */
    public function store(Request $request, $contractId)
    {
        $request->validate([
            'cco_amount' => 'required|numeric',
            'cco_description' => 'required|string',
            'cco_reason' => 'nullable|string|max:500',
        ]);

        $result = $this->contractService->createChangeOrder($contractId, $request->only([
            'cco_amount', 'cco_description', 'cco_reason',
        ]));

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['error']);
        }

        return redirect()->route('admin.contract-change-orders.show', $result['change_order']->cco_id)
            ->with('success', "Change Order {$result['change_order']->cco_number} created.");
    }

    /**
     * Show CCO detail.
     */
    public function show($id)
    {
        $changeOrder = ContractChangeOrder::with([
            'contract.project', 'contract.supplier',
            'creator', 'approver', 'approvalRequest',
        ])->findOrFail($id);

        $approvalHistory = $this->approvalService->getApprovalHistory('contract_co', $id);

        return view('admin.contract-change-orders.show', compact('changeOrder', 'approvalHistory'));
    }

    /**
     * Submit CCO for approval.
     */
    public function submit($id)
    {
        $cco = ContractChangeOrder::with('contract')->findOrFail($id);

        if (!$cco->canSubmit()) {
            return back()->with('error', 'This change order cannot be submitted.');
        }

        $result = $this->approvalService->submitForApproval(
            'contract_co',
            $cco->cco_id,
            abs($cco->cco_amount),
            auth()->id(),
            $cco->contract->contract_project_id
        );

        if (!$result['success']) {
            return back()->with('error', $result['error'] ?? 'Submission failed.');
        }

        if (!empty($result['auto_approved'])) {
            return back()->with('success', 'Change order auto-approved (no approval workflow configured).');
        }

        return back()->with('success', $result['message'] ?? 'Submitted for approval.');
    }

    /**
     * Approve CCO.
     */
    public function approve(Request $request, $id)
    {
        $cco = ContractChangeOrder::findOrFail($id);

        if ($cco->cco_status !== 'pending_approval') {
            return back()->with('error', 'This change order is not pending approval.');
        }

        $approvalRequest = $cco->approvalRequest;
        if (!$approvalRequest) {
            // Direct approval without workflow
            $result = $this->contractService->approveChangeOrder($id, auth()->id());
            return back()->with($result['success'] ? 'success' : 'error',
                $result['success'] ? 'Change order approved.' : $result['error']);
        }

        $result = $this->approvalService->processApproval(
            $approvalRequest->request_id,
            'approved',
            auth()->id(),
            auth()->user()->name ?? 'Admin',
            $request->comments
        );

        return back()->with($result['success'] ? 'success' : 'error',
            $result['message'] ?? ($result['error'] ?? 'Unknown error'));
    }

    /**
     * Reject CCO.
     */
    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $cco = ContractChangeOrder::findOrFail($id);
        $approvalRequest = $cco->approvalRequest;

        if ($approvalRequest) {
            $result = $this->approvalService->processApproval(
                $approvalRequest->request_id,
                'rejected',
                auth()->id(),
                auth()->user()->name ?? 'Admin',
                $request->reason
            );
        } else {
            $cco->cco_status = 'rejected';
            $cco->rejection_reason = $request->reason;
            $cco->save();
            $result = ['success' => true, 'message' => 'Change order rejected.'];
        }

        return back()->with($result['success'] ? 'success' : 'error',
            $result['message'] ?? ($result['error'] ?? 'Unknown error'));
    }

    /**
     * Cancel CCO.
     */
    public function cancel($id)
    {
        $cco = ContractChangeOrder::findOrFail($id);

        if (!in_array($cco->cco_status, ['draft', 'rejected'])) {
            return back()->with('error', 'Only draft or rejected change orders can be cancelled.');
        }

        // Remove from pending COs on contract
        $contract = $cco->contract;
        if ($cco->cco_status === 'draft') {
            $contract->contract_pending_cos -= $cco->cco_amount;
            $contract->save();
        }

        $cco->cco_status = 'cancelled';
        $cco->updated_at = now();
        $cco->save();

        return back()->with('success', 'Change order cancelled.');
    }
}
