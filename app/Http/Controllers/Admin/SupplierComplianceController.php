<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierCompliance;
use App\Services\ComplianceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupplierComplianceController extends Controller
{
    protected $complianceService;

    public function __construct(ComplianceService $complianceService)
    {
        $this->complianceService = $complianceService;
    }

    /**
     * List compliance items for a supplier.
     */
    public function index($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $status = $this->complianceService->getSupplierComplianceStatus($supplierId);
        $typeOptions = SupplierCompliance::getTypeOptions();

        return view('admin.compliance.index', compact('supplier', 'status', 'typeOptions'));
    }

    /**
     * Store a new compliance item.
     */
    public function store(Request $request, $supplierId)
    {
        $request->validate([
            'compliance_type' => 'required|string|max:50',
            'compliance_name' => 'required|string|max:255',
            'compliance_number' => 'nullable|string|max:100',
            'compliance_issuer' => 'nullable|string|max:255',
            'compliance_amount' => 'nullable|numeric|min:0',
            'compliance_issue_date' => 'nullable|date',
            'compliance_expiry_date' => 'nullable|date',
            'compliance_warning_days' => 'nullable|integer|min:1|max:365',
            'compliance_required' => 'nullable|boolean',
            'compliance_contract_id' => 'nullable|integer',
            'compliance_notes' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $file = $request->hasFile('document') ? $request->file('document') : null;

        $result = $this->complianceService->addComplianceItem(
            $supplierId,
            $request->only([
                'compliance_type', 'compliance_name', 'compliance_number',
                'compliance_issuer', 'compliance_amount', 'compliance_issue_date',
                'compliance_expiry_date', 'compliance_warning_days', 'compliance_required',
                'compliance_contract_id', 'compliance_notes',
            ]),
            $file
        );

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['error']);
        }

        return back()->with('success', 'Compliance item added successfully.');
    }

    /**
     * Update a compliance item.
     */
    public function update(Request $request, $supplierId, $id)
    {
        $request->validate([
            'compliance_type' => 'required|string|max:50',
            'compliance_name' => 'required|string|max:255',
            'compliance_number' => 'nullable|string|max:100',
            'compliance_issuer' => 'nullable|string|max:255',
            'compliance_amount' => 'nullable|numeric|min:0',
            'compliance_issue_date' => 'nullable|date',
            'compliance_expiry_date' => 'nullable|date',
            'compliance_warning_days' => 'nullable|integer|min:1|max:365',
            'compliance_required' => 'nullable|boolean',
            'compliance_notes' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $file = $request->hasFile('document') ? $request->file('document') : null;

        $result = $this->complianceService->updateComplianceItem(
            $id,
            $request->only([
                'compliance_type', 'compliance_name', 'compliance_number',
                'compliance_issuer', 'compliance_amount', 'compliance_issue_date',
                'compliance_expiry_date', 'compliance_warning_days', 'compliance_required',
                'compliance_notes',
            ]),
            $file
        );

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['error']);
        }

        return back()->with('success', 'Compliance item updated.');
    }

    /**
     * Delete a compliance item.
     */
    public function destroy($supplierId, $id)
    {
        $item = SupplierCompliance::where('compliance_supplier_id', $supplierId)
            ->where('compliance_id', $id)
            ->firstOrFail();

        if ($item->compliance_document_path) {
            Storage::disk('public')->delete($item->compliance_document_path);
        }

        $item->delete();

        return back()->with('success', 'Compliance item removed.');
    }

    /**
     * Company-wide compliance dashboard.
     */
    public function dashboard()
    {
        $dashboardData = $this->complianceService->getDashboardData();

        return view('admin.compliance.dashboard', $dashboardData);
    }

    /**
     * Upload/replace document for a compliance item.
     */
    public function uploadDocument(Request $request, $id)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $item = SupplierCompliance::findOrFail($id);

        // Remove old document
        if ($item->compliance_document_path) {
            Storage::disk('public')->delete($item->compliance_document_path);
        }

        $file = $request->file('document');
        $originalName = $file->getClientOriginalName();
        $storedName = \Illuminate\Support\Str::random(10) . '_' . $originalName;
        $path = $file->storeAs("compliance/{$item->compliance_supplier_id}", $storedName, 'public');

        $item->compliance_document_path = $path;
        $item->compliance_document_name = $originalName;
        $item->compliance_modified_by = auth()->id();
        $item->compliance_modified_at = now();
        $item->save();

        return back()->with('success', 'Document uploaded.');
    }

    /**
     * Download a compliance document.
     */
    public function downloadDocument($id)
    {
        $item = SupplierCompliance::findOrFail($id);

        if (!$item->compliance_document_path || !Storage::disk('public')->exists($item->compliance_document_path)) {
            return back()->with('error', 'Document not found.');
        }

        return Storage::disk('public')->download(
            $item->compliance_document_path,
            $item->compliance_document_name ?? 'document'
        );
    }
}
