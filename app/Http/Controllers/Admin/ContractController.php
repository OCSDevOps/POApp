<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractDocument;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\CostCode;
use App\Services\ContractService;
use App\Services\ComplianceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContractController extends Controller
{
    protected $contractService;
    protected $complianceService;

    public function __construct(ContractService $contractService, ComplianceService $complianceService)
    {
        $this->contractService = $contractService;
        $this->complianceService = $complianceService;
    }

    /**
     * List all contracts.
     */
    public function index(Request $request)
    {
        $query = Contract::with(['project', 'supplier', 'costCode']);

        if ($request->project_id) {
            $query->byProject($request->project_id);
        }
        if ($request->supplier_id) {
            $query->bySupplier($request->supplier_id);
        }
        if ($request->status) {
            $query->byStatus($request->status);
        }

        $contracts = $query->orderBy('contract_created_at', 'desc')->get();

        $projects = Project::active()->orderByName()->get();
        $suppliers = Supplier::active()->subcontractors()->orderByName()->get();

        return view('admin.contracts.index', compact('contracts', 'projects', 'suppliers'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $projects = Project::active()->orderByName()->get();
        $suppliers = Supplier::active()->subcontractors()->orderByName()->get();
        $costCodes = CostCode::active()->orderById()->get();

        return view('admin.contracts.create', compact('projects', 'suppliers', 'costCodes'));
    }

    /**
     * Store a new contract.
     */
    public function store(Request $request)
    {
        $request->validate([
            'contract_title' => 'required|string|max:255',
            'contract_project_id' => 'required|integer|exists:project_master,proj_id',
            'contract_supplier_id' => 'required|integer|exists:supplier_master,sup_id',
            'contract_cost_code_id' => 'nullable|integer|exists:cost_code_master,cc_id',
            'contract_original_value' => 'required|numeric|min:0',
            'contract_retention_pct' => 'nullable|numeric|min:0|max:100',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'contract_scope' => 'nullable|string',
            'contract_terms' => 'nullable|string',
            'contract_description' => 'nullable|string',
            'documents' => 'nullable|array|max:10',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,csv,txt|max:10240',
            'document_types' => 'nullable|array',
        ]);

        $result = $this->contractService->createContract($request->only([
            'contract_title', 'contract_description', 'contract_project_id',
            'contract_supplier_id', 'contract_cost_code_id', 'contract_original_value',
            'contract_retention_pct', 'contract_start_date', 'contract_end_date',
            'contract_scope', 'contract_terms',
        ]));

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['error']);
        }

        // Handle document uploads
        if ($request->hasFile('documents')) {
            $this->storeDocuments($result['contract'], $request->file('documents'), $request->input('document_types', []));
        }

        return redirect()->route('admin.contracts.show', $result['contract']->contract_id)
            ->with('success', "Contract {$result['contract']->contract_number} created successfully.");
    }

    /**
     * Show contract detail.
     */
    public function show($id)
    {
        $contract = Contract::with([
            'project', 'supplier', 'costCode', 'createdBy',
            'changeOrders.creator', 'changeOrders.approver',
            'invoices', 'documents',
        ])->findOrFail($id);

        $summary = $this->contractService->getContractSummary($id);
        $complianceStatus = $this->complianceService->getSupplierComplianceStatus($contract->contract_supplier_id);

        return view('admin.contracts.show', compact('contract', 'summary', 'complianceStatus'));
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $contract = Contract::findOrFail($id);

        if (!$contract->isEditable()) {
            return redirect()->route('admin.contracts.show', $id)
                ->with('error', 'This contract cannot be edited in its current status.');
        }

        $projects = Project::active()->orderByName()->get();
        $suppliers = Supplier::active()->subcontractors()->orderByName()->get();
        $costCodes = CostCode::active()->orderById()->get();

        return view('admin.contracts.edit', compact('contract', 'projects', 'suppliers', 'costCodes'));
    }

    /**
     * Update contract.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'contract_title' => 'required|string|max:255',
            'contract_project_id' => 'required|integer',
            'contract_supplier_id' => 'required|integer',
            'contract_cost_code_id' => 'nullable|integer',
            'contract_original_value' => 'required|numeric|min:0',
            'contract_retention_pct' => 'nullable|numeric|min:0|max:100',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'contract_scope' => 'nullable|string',
            'contract_terms' => 'nullable|string',
            'contract_description' => 'nullable|string',
        ]);

        $result = $this->contractService->updateContract($id, $request->only([
            'contract_title', 'contract_description', 'contract_project_id',
            'contract_supplier_id', 'contract_cost_code_id', 'contract_original_value',
            'contract_retention_pct', 'contract_start_date', 'contract_end_date',
            'contract_scope', 'contract_terms',
        ]));

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['error']);
        }

        return redirect()->route('admin.contracts.show', $id)
            ->with('success', 'Contract updated successfully.');
    }

    /**
     * Delete/cancel contract.
     */
    public function destroy($id)
    {
        $contract = Contract::findOrFail($id);

        if ($contract->contract_status !== Contract::STATUS_DRAFT) {
            return back()->with('error', 'Only draft contracts can be deleted.');
        }

        // Delete documents
        foreach ($contract->documents as $doc) {
            Storage::disk('public')->delete($doc->cdoc_path);
        }

        $contract->documents()->delete();
        $contract->changeOrders()->delete();
        $contract->delete();

        return redirect()->route('admin.contracts.index')
            ->with('success', 'Contract deleted successfully.');
    }

    /**
     * Update contract status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['action' => 'required|in:activate,complete,cancel']);

        $userId = auth()->id();

        $result = match ($request->action) {
            'activate' => $this->contractService->activateContract($id, $userId),
            'complete' => $this->contractService->completeContract($id, $userId),
            'cancel' => $this->contractService->cancelContract($id, $userId),
        };

        if (!$result['success']) {
            return back()->with('error', $result['error']);
        }

        return back()->with('success', 'Contract status updated successfully.');
    }

    /**
     * Upload documents to a contract.
     */
    public function uploadDocuments(Request $request, $id)
    {
        $request->validate([
            'documents' => 'required|array|max:10',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,csv,txt|max:10240',
            'document_types' => 'nullable|array',
        ]);

        $contract = Contract::findOrFail($id);

        $this->storeDocuments($contract, $request->file('documents'), $request->input('document_types', []));

        return back()->with('success', 'Documents uploaded successfully.');
    }

    /**
     * Download a contract document.
     */
    public function downloadDocument($id, $docId)
    {
        $document = ContractDocument::where('cdoc_contract_id', $id)
            ->where('cdoc_id', $docId)
            ->firstOrFail();

        if (!Storage::disk('public')->exists($document->cdoc_path)) {
            return back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download($document->cdoc_path, $document->cdoc_original_name);
    }

    /**
     * Delete a contract document.
     */
    public function deleteDocument($id, $docId)
    {
        $document = ContractDocument::where('cdoc_contract_id', $id)
            ->where('cdoc_id', $docId)
            ->firstOrFail();

        Storage::disk('public')->delete($document->cdoc_path);
        $document->delete();

        return back()->with('success', 'Document deleted.');
    }

    /**
     * Release retention on a contract.
     */
    public function releaseRetention(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $result = $this->contractService->releaseRetention($id, $request->amount, auth()->id());

        if (!$result['success']) {
            return back()->with('error', $result['error']);
        }

        return back()->with('success', "Retention of \${$request->amount} released successfully.");
    }

    /**
     * Store uploaded documents for a contract.
     */
    private function storeDocuments(Contract $contract, array $files, array $types = [])
    {
        foreach ($files as $index => $file) {
            $originalName = $file->getClientOriginalName();
            $storedName = Str::random(10) . '_' . $originalName;
            $path = $file->storeAs("contracts/{$contract->contract_id}", $storedName, 'public');

            ContractDocument::create([
                'cdoc_contract_id' => $contract->contract_id,
                'cdoc_original_name' => $originalName,
                'cdoc_path' => $path,
                'cdoc_mime' => $file->getClientMimeType(),
                'cdoc_size' => $file->getSize(),
                'cdoc_type' => $types[$index] ?? 'other',
                'cdoc_createby' => auth()->id(),
                'cdoc_createdate' => now(),
                'cdoc_status' => 1,
                'company_id' => session('company_id'),
            ]);
        }
    }
}
