<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderAttachment;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\CostCode;
use App\Models\UnitOfMeasure;
use App\Models\TaxGroup;
use App\Services\BudgetService;
use App\Services\Cache\ReferenceDataCacheService;
use App\Services\ApprovalService;
use App\Services\PoChangeOrderService;

class PurchaseOrderController extends Controller
{
    protected $budgetService;
    protected $approvalService;
    protected $poChangeOrderService;
    protected $referenceDataCacheService;

    public function __construct(
        BudgetService $budgetService,
        ApprovalService $approvalService,
        PoChangeOrderService $poChangeOrderService,
        ReferenceDataCacheService $referenceDataCacheService
    ) {
        $this->budgetService = $budgetService;
        $this->approvalService = $approvalService;
        $this->poChangeOrderService = $poChangeOrderService;
        $this->referenceDataCacheService = $referenceDataCacheService;
    }

    /**
     * Display a listing of purchase orders.
     */
    public function index(Request $request)
    {
        $project = $request->get('project');
        $supplier = $request->get('supplier');
        $status = $request->get('status');

        $purchaseOrders = PurchaseOrder::with(['project', 'supplier'])
            ->byProject($project)
            ->bySupplier($supplier)
            ->byStatus($status)
            ->orderBy('porder_id', 'DESC')
            ->get();

        $companyId = (int) session('company_id');
        $projects = $this->referenceDataCacheService->getActiveProjects($companyId);
        $suppliers = $this->referenceDataCacheService->getActiveSuppliers($companyId);

        $filters = [
            'project' => $project,
            'supplier' => $supplier,
            'status' => $status,
        ];

        return view('admin.porder.porder_list_view', compact('purchaseOrders', 'projects', 'suppliers', 'filters'));
    }

    /**
     * Show the form for creating a new purchase order.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Check permissions
        $templateDetails = DB::table('permission_master')
            ->where('pt_id', session('pt_id'))
            ->where('status', 1)
            ->first();

        if ($user->u_type != 1 && (!$templateDetails || $templateDetails->pt_t_porder >= 3)) {
            return redirect()->route('error.404');
        }

        $companyId = (int) session('company_id');
        $items = Item::active()->orderByName()->get();
        $projects = $this->referenceDataCacheService->getActiveProjects($companyId);
        $suppliers = $this->referenceDataCacheService->getActiveSuppliers($companyId);
        $packages = \App\Models\ItemPackage::orderBy('ipack_name', 'ASC')->get();
        $taxGroups = \App\Models\TaxGroup::orderBy('id', 'ASC')->get();
        $costCodes = $this->referenceDataCacheService->getActiveCostCodes($companyId);
        $categories = ItemCategory::orderBy('icat_id', 'ASC')->get();
        $uoms = UnitOfMeasure::orderBy('uom_id', 'ASC')->get();

        // Get budget info for display
        $budgetInfo = [];
        if ($request->has('project_id')) {
            $budgetInfo = $this->budgetService->getProjectBudgetSummary($request->project_id);
        }

        return view('admin.porder.add_pur_order', compact(
            'items', 'projects', 'suppliers', 'packages', 
            'taxGroups', 'costCodes', 'categories', 'uoms', 'budgetInfo'
        ));
    }

    /**
     * Store a newly created purchase order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'po_project' => 'required',
            'po_supplier' => 'required',
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,csv,txt|max:10240',
        ]);

        DB::beginTransaction();
        try {
            // Generate PO number
            $lastPO = PurchaseOrder::orderBy('porder_id', 'DESC')->first();
            $poNumber = 'PO-' . str_pad(($lastPO ? $lastPO->porder_id + 1 : 1), 6, '0', STR_PAD_LEFT);

            $purchaseOrder = PurchaseOrder::create([
                'porder_no' => $poNumber,
                'porder_project_ms' => $request->po_project,
                'porder_supplier_ms' => $request->po_supplier,
                'porder_address' => $request->po_address ?? '',
                'porder_delivery_note' => $request->po_delivery_note,
                'porder_description' => $request->po_description,
                'porder_total_item' => 0,
                'porder_total_amount' => 0,
                'porder_total_tax' => 0,
                'porder_status' => 1,
                'porder_delivery_status' => 0,
                'porder_original_total' => 0,
                'porder_change_orders_total' => 0,
                'integration_status' => 'pending',
                'porder_createby' => Auth::id(),
                'porder_createdate' => now(),
            ]);

            // Process items
            if ($request->has('items')) {
                $total = 0;
                $tax = 0;

                foreach ($request->items as $item) {
                    $itemTotal = $item['quantity'] * $item['price'];
                    $itemTax = $itemTotal * ($item['tax_rate'] ?? 0) / 100;
                    
                    PurchaseOrderItem::create([
                        'po_detail_autogen' => uniqid('pod_'),
                        'po_detail_porder_ms' => $purchaseOrder->porder_id,
                        'po_detail_item' => $item['code'],
                        'po_detail_sku' => $item['name'],
                        'po_detail_taxcode' => $item['tax_code'] ?? '',
                        'po_detail_quantity' => $item['quantity'],
                        'po_detail_unitprice' => $item['price'],
                        'po_detail_subtotal' => $itemTotal,
                        'po_detail_taxamount' => $itemTax,
                        'po_detail_total' => $itemTotal + $itemTax,
                        'po_detail_createdate' => now(),
                        'po_detail_status' => 1,
                    ]);

                    $total += $itemTotal;
                    $tax += $itemTax;
                }

                $grandTotal = $total + $tax;

                $purchaseOrder->update([
                    'porder_total_amount' => $total,
                    'porder_total_tax' => $tax,
                    'porder_total_item' => count($request->items),
                    'porder_original_total' => $grandTotal,
                ]);

                // Budget validation - check availability
                $costCodeIds = collect($request->items)->pluck('cost_code')->filter()->unique()->toArray();
                
                if (!empty($costCodeIds)) {
                    $budgetValidation = $this->budgetService->validatePoBudget(
                        $request->po_project,
                        $costCodeIds,
                        $grandTotal
                    );

                    // Check if any cost code exceeds critical threshold (90%)
                    $hasExceeded = collect($budgetValidation)->contains(function ($validation) {
                        return $validation['utilization_after'] >= 90;
                    });

                    if ($hasExceeded && !$request->has('budget_override')) {
                        // Budget override required
                        DB::rollBack();
                        $exceedingCodes = collect($budgetValidation)
                            ->filter(fn($v) => $v['utilization_after'] >= 90)
                            ->map(fn($v) => $v['cost_code'])
                            ->implode(', ');
                        
                        return back()->withInput()
                            ->with('budget_warning', "Budget critical threshold (90%) exceeded for cost codes: {$exceedingCodes}. Override required.")
                            ->with('budget_validation', $budgetValidation)
                            ->with('requires_override', true);
                    }

                    // Check warning threshold (75%)
                    $hasWarning = collect($budgetValidation)->contains(function ($validation) {
                        return $validation['utilization_after'] >= 75 && $validation['utilization_after'] < 90;
                    });

                    if ($hasWarning) {
                        $warningCodes = collect($budgetValidation)
                            ->filter(fn($v) => $v['utilization_after'] >= 75 && $v['utilization_after'] < 90)
                            ->map(fn($v) => $v['cost_code'])
                            ->implode(', ');
                        
                        session()->flash('budget_warning', "Warning: Budget threshold (75%) reached for cost codes: {$warningCodes}");
                    }

                    // Update budget commitments
                    foreach ($request->items as $item) {
                        if (!empty($item['cost_code'])) {
                            $itemTotal = $item['quantity'] * $item['price'];
                            $this->budgetService->updateBudgetCommitment(
                                $request->po_project,
                                $item['cost_code'],
                                $itemTotal
                            );
                        }
                    }
                }
            }

            if ($request->hasFile('attachments')) {
                $this->storeAttachments($purchaseOrder, $request->file('attachments'));
            }

            DB::commit();
            return redirect()->route('admin.porder.index')
                ->with('success', 'Purchase Order created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified purchase order.
     */
    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with(['project', 'supplier', 'items', 'attachments'])->findOrFail($id);
        
        return view('admin.porder.view_pur_order', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified purchase order.
     */
    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::with(['items', 'attachments'])->findOrFail($id);
        
        // Authorization: Ensure user can only edit their company's POs
        if (!$purchaseOrder->isOwnedByCurrentCompany()) {
            abort(403, 'Unauthorized access to another company\'s purchase order');
        }
        
        $companyId = (int) session('company_id');
        $items = Item::active()->orderByName()->get();
        $projects = $this->referenceDataCacheService->getActiveProjects($companyId);
        $suppliers = $this->referenceDataCacheService->getActiveSuppliers($companyId);
        $packages = \App\Models\ItemPackage::orderBy('ipack_name', 'ASC')->get();
        $taxGroups = \App\Models\TaxGroup::orderBy('id', 'ASC')->get();
        $costCodes = $this->referenceDataCacheService->getActiveCostCodes($companyId);
        $categories = ItemCategory::orderBy('icat_id', 'ASC')->get();
        $uoms = UnitOfMeasure::orderBy('uom_id', 'ASC')->get();

        return view('admin.porder.edit_pur_order', compact(
            'purchaseOrder', 'items', 'projects', 'suppliers', 
            'packages', 'taxGroups', 'costCodes', 'categories', 'uoms'
        ));
    }

    /**
     * Update the specified purchase order.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'po_project' => 'required',
            'po_supplier' => 'required',
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,csv,txt|max:10240',
            'remove_attachment_ids' => 'nullable|array',
            'remove_attachment_ids.*' => 'integer',
        ]);

        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::findOrFail($id);

            // Authorization: Ensure user can only update their company's POs
            if (!$purchaseOrder->isOwnedByCurrentCompany()) {
                abort(403, 'Unauthorized access to another company\'s purchase order');
            }

            $purchaseOrder->update([
                'porder_project_ms' => $request->po_project,
                'porder_supplier_ms' => $request->po_supplier,
                'porder_address' => $request->po_address ?? $purchaseOrder->porder_address,
                'porder_delivery_note' => $request->po_delivery_note,
                'porder_description' => $request->po_description,
                'porder_modifyby' => Auth::id(),
                'porder_modifydate' => now(),
            ]);

            // Delete existing items and re-add (Eloquent automatically filters by company_id)
            PurchaseOrderItem::where('po_detail_porder_ms', $id)->delete();

            // Process items
            if ($request->has('items')) {
                $total = 0;
                $tax = 0;

                foreach ($request->items as $item) {
                    $itemTotal = $item['quantity'] * $item['price'];
                    $itemTax = $itemTotal * ($item['tax_rate'] ?? 0) / 100;

                    PurchaseOrderItem::create([
                        'po_detail_autogen' => uniqid('pod_'),
                        'po_detail_porder_ms' => $purchaseOrder->porder_id,
                        'po_detail_item' => $item['code'],
                        'po_detail_sku' => $item['name'],
                        'po_detail_taxcode' => $item['tax_code'] ?? '',
                        'po_detail_quantity' => $item['quantity'],
                        'po_detail_unitprice' => $item['price'],
                        'po_detail_subtotal' => $itemTotal,
                        'po_detail_taxamount' => $itemTax,
                        'po_detail_total' => $itemTotal + $itemTax,
                        'po_detail_createdate' => now(),
                        'po_detail_status' => 1,
                    ]);

                    $total += $itemTotal;
                    $tax += $itemTax;
                }

                $newGrandTotal = $total + $tax;
                $oldGrandTotal = $purchaseOrder->grand_total;
                $originalTotal = $purchaseOrder->porder_original_total ?? $oldGrandTotal;

                // Check if amount changed - create PCO if significant change
                if (abs($newGrandTotal - $oldGrandTotal) > 0.01) {
                    // Create PO Change Order
                    $pco = $this->poChangeOrderService->createPoChangeOrder(
                        $purchaseOrder->porder_id,
                        Auth::id(),
                        'amount_change',
                        $oldGrandTotal,
                        $newGrandTotal,
                        $request->change_reason ?? 'PO amount updated during edit'
                    );

                    // Submit for approval if required
                    try {
                        $this->approvalService->submitForApproval(
                            'PoChangeOrder',
                            $pco->poco_id,
                            $request->po_project,
                            abs($newGrandTotal - $oldGrandTotal)
                        );

                        session()->flash('info', 'PO Change Order ' . $pco->poco_number . ' created and submitted for approval.');
                    } catch (\Exception $e) {
                        // No workflow, auto-approve
                        $this->poChangeOrderService->approvePoChangeOrder($pco->poco_id, Auth::id());
                        session()->flash('info', 'PO Change Order ' . $pco->poco_number . ' created and auto-approved.');
                    }
                }

                $purchaseOrder->update([
                    'porder_total_amount' => $total,
                    'porder_total_tax' => $tax,
                    'porder_total_item' => count($request->items),
                    'porder_change_orders_total' => $newGrandTotal - $originalTotal,
                ]);
            }

            if ($request->filled('remove_attachment_ids')) {
                $this->removeAttachments($purchaseOrder, $request->remove_attachment_ids);
            }

            if ($request->hasFile('attachments')) {
                $this->storeAttachments($purchaseOrder, $request->file('attachments'));
            }

            DB::commit();
            return redirect()->route('admin.porder.index')
                ->with('success', 'Purchase Order updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified purchase order.
     */
    public function destroy($id)
    {
        // Authorization check
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        abort_unless($purchaseOrder->company_id === session('company_id'), 403, 'Unauthorized access');

        DB::beginTransaction();
        try {
            $attachments = PurchaseOrderAttachment::where('po_attachment_porder_ms', $id)->get();
            foreach ($attachments as $attachment) {
                if (!empty($attachment->po_attachment_path)) {
                    Storage::disk('public')->delete($attachment->po_attachment_path);
                }
            }
            PurchaseOrderAttachment::where('po_attachment_porder_ms', $id)->delete();

            // Delete items first (Eloquent automatically applies company scope)
            PurchaseOrderItem::where('po_detail_porder_ms', $id)->delete();

            $purchaseOrder->delete();

            DB::commit();
            return redirect()->route('admin.porder.index')
                ->with('success', 'Purchase Order deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Get item master list for AJAX request.
     */
    public function getItemMasterList(Request $request)
    {
        $query = Item::active()
            ->with(['category', 'costCode']);

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('cc')) {
            $query->byCostCode($request->cc);
        }

        $items = $query->orderByName()->get();

        $data = [];
        $count = 1;
        foreach ($items as $item) {
            $data[] = [
                $count,
                $item->item_code,
                $item->item_name,
                $item->category->icat_name ?? '',
                $item->costCode->cc_no ?? '',
                '<input type="checkbox" class="form-control search-item" id="search-item1' . $count . '" value="' . $item->item_code . '">'
            ];
            $count++;
        }

        return response()->json(['data' => $data]);
    }

    /**
     * Get supplier catalog list for AJAX request.
     */
    public function getSupplierCatalogList(Request $request)
    {
        $companyId = session('company_id');
        
        $query = DB::table('supplier_catalog_tab as sc')
            ->join('item_master as im', 'im.item_code', '=', 'sc.supcat_item_code')
            ->join('item_category_tab as ic', 'ic.icat_id', '=', 'im.item_cat_ms')
            ->join('cost_code_master as cc', 'cc.cc_id', '=', 'im.item_ccode_ms')
            ->where('im.item_status', 1)
            ->where('im.company_id', $companyId)
            ->where('sc.company_id', $companyId);

        if ($request->filled('category')) {
            $query->where('im.item_cat_ms', $request->category);
        }

        if ($request->filled('cc')) {
            $query->where('im.item_ccode_ms', $request->cc);
        }

        if ($request->filled('supplier')) {
            $query->where('sc.supcat_supplier', $request->supplier);
        }

        $items = $query->orderBy('im.item_name', 'ASC')->get();

        $data = [];
        $count = 1;
        foreach ($items as $item) {
            $data[] = [
                $count,
                $item->item_code,
                $item->item_name,
                $item->icat_name,
                $item->cc_no,
                $item->supcat_sku_no,
                $item->supcat_price,
                '<input type="checkbox" class="form-control search-item" id="search-item2' . $count . '" value="' . $item->item_code . '">'
            ];
            $count++;
        }

        return response()->json(['data' => $data]);
    }

    /**
     * Get project address for AJAX request.
     */
    public function getProjectAddress(Request $request)
    {
        $project = Project::find($request->po_project);
        
        if ($project) {
            return response()->json([
                'success' => true,
                'address' => $project->proj_address,
            ]);
        }

        return response()->json(['success' => false]);
    }

    /**
     * Update purchase order status.
     */
    public function updateStatus(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        
        $purchaseOrder->update([
            'porder_status' => $request->status,
            'porder_modifyby' => Auth::id(),
            'porder_modifydate' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    /**
     * Download an attachment for a purchase order.
     */
    public function downloadAttachment($id, $attachmentId)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        abort_unless($purchaseOrder->isOwnedByCurrentCompany(), 403, 'Unauthorized access');

        $attachment = PurchaseOrderAttachment::where('po_attachment_porder_ms', $id)
            ->where('po_attachment_id', $attachmentId)
            ->firstOrFail();

        if (!Storage::disk('public')->exists($attachment->po_attachment_path)) {
            return back()->with('error', 'Attachment file not found on disk.');
        }

        return Storage::disk('public')->download(
            $attachment->po_attachment_path,
            $attachment->po_attachment_original_name
        );
    }

    /**
     * Generate PDF for purchase order.
     */
    public function generatePdf($id)
    {
        $purchaseOrder = PurchaseOrder::with(['project', 'supplier', 'items'])->findOrFail($id);
        
        // You can use a PDF library like DomPDF or TCPDF here
        // For now, return a view that can be printed
        return view('admin.pdf_view.purchase_order', compact('purchaseOrder'));
    }

    /**
     * Check budget availability for AJAX request.
     */
    public function checkBudgetAvailability(Request $request)
    {
        try {
            $projectId = $request->input('project_id');
            $costCodeId = $request->input('cost_code_id');
            $amount = $request->input('amount', 0);

            if (!$projectId || !$costCodeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID and Cost Code ID are required'
                ], 400);
            }

            $validation = $this->budgetService->validatePoBudget(
                $projectId,
                [$costCodeId],
                $amount
            );

            $result = $validation[0] ?? null;

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'No budget found for this cost code'
                ]);
            }

            return response()->json([
                'success' => true,
                'budget' => $result['budget'],
                'committed' => $result['committed'],
                'available' => $result['available'],
                'utilization_before' => $result['utilization_before'],
                'utilization_after' => $result['utilization_after'],
                'status' => $result['utilization_after'] >= 90 ? 'critical' : 
                           ($result['utilization_after'] >= 75 ? 'warning' : 'ok'),
                'message' => $result['utilization_after'] >= 90 ? 
                    'Budget critical threshold (90%) exceeded. Override required.' :
                    ($result['utilization_after'] >= 75 ? 
                        'Warning: Budget threshold (75%) reached.' : 
                        'Budget available.')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking budget: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Persist uploaded files as purchase order attachments.
     */
    private function storeAttachments(PurchaseOrder $purchaseOrder, array $files): void
    {
        foreach ($files as $file) {
            $storedPath = $file->storeAs(
                'purchase-orders/' . $purchaseOrder->porder_id,
                Str::random(10) . '_' . $file->getClientOriginalName(),
                'public'
            );

            PurchaseOrderAttachment::create([
                'po_attachment_porder_ms' => $purchaseOrder->porder_id,
                'po_attachment_original_name' => $file->getClientOriginalName(),
                'po_attachment_path' => $storedPath,
                'po_attachment_mime' => $file->getClientMimeType(),
                'po_attachment_size' => $file->getSize(),
                'po_attachment_createby' => Auth::id(),
                'po_attachment_createdate' => now(),
                'po_attachment_status' => 1,
            ]);
        }
    }

    /**
     * Remove selected attachments from storage and database.
     */
    private function removeAttachments(PurchaseOrder $purchaseOrder, array $attachmentIds): void
    {
        $attachments = PurchaseOrderAttachment::where('po_attachment_porder_ms', $purchaseOrder->porder_id)
            ->whereIn('po_attachment_id', $attachmentIds)
            ->get();

        foreach ($attachments as $attachment) {
            if (!empty($attachment->po_attachment_path)) {
                Storage::disk('public')->delete($attachment->po_attachment_path);
            }

            $attachment->delete();
        }
    }
}
