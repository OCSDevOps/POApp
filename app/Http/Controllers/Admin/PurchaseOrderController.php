<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\CostCode;
use App\Models\UnitOfMeasure;
use App\Models\TaxGroup;
use App\Services\BudgetService;
use App\Services\ApprovalService;
use App\Services\PoChangeOrderService;

class PurchaseOrderController extends Controller
{
    protected $budgetService;
    protected $approvalService;
    protected $poChangeOrderService;

    public function __construct(
        BudgetService $budgetService,
        ApprovalService $approvalService,
        PoChangeOrderService $poChangeOrderService
    ) {
        $this->budgetService = $budgetService;
        $this->approvalService = $approvalService;
        $this->poChangeOrderService = $poChangeOrderService;
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
            ->whereNotNull('porder_type')
            ->orderBy('porder_id', 'DESC')
            ->get();

        $projects = Project::active()->orderByName()->get();
        $suppliers = Supplier::active()->orderByName()->get();

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
    public function create()
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

        $items = Item::active()->nonRentable()->orderByName()->get();
        $projects = Project::active()->orderByName()->get();
        $suppliers = Supplier::active()->orderByName()->get();
        $packages = \App\Models\ItemPackage::orderBy('ipack_name', 'ASC')->get();
        $taxGroups = \App\Models\TaxGroup::orderBy('id', 'ASC')->get();
        $costCodes = CostCode::orderById()->get();
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
            'po_type' => 'required',
            'po_date' => 'required|date',
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
                'porder_type' => $request->po_type,
                'porder_date' => $request->po_date,
                'porder_delivery_date' => $request->po_delivery_date,
                'porder_notes' => $request->po_notes,
                'porder_terms' => $request->po_terms,
                'porder_general_status' => 'pending',
                'porder_delivery_status' => '0',
                'integration_status' => 'pending',
                'porder_created_by' => Auth::id(),
                'porder_created_at' => now(),
            ]);

            // Process items
            if ($request->has('items')) {
                $total = 0;
                $tax = 0;

                foreach ($request->items as $item) {
                    $itemTotal = $item['quantity'] * $item['price'];
                    $itemTax = $itemTotal * ($item['tax_rate'] ?? 0) / 100;
                    
                    PurchaseOrderItem::create([
                        'porder_item_porder_ms' => $purchaseOrder->porder_id,
                        'porder_item_code' => $item['code'],
                        'porder_item_name' => $item['name'],
                        'porder_item_qty' => $item['quantity'],
                        'porder_item_price' => $item['price'],
                        'porder_item_tax' => $itemTax,
                        'porder_item_total' => $itemTotal + $itemTax,
                        'porder_item_ccode' => $item['cost_code'] ?? null,
                        // company_id auto-added by CompanyScope trait
                    ]);

                    $total += $itemTotal;
                    $tax += $itemTax;
                }

                $grandTotal = $total + $tax;

                $purchaseOrder->update([
                    'porder_total' => $total,
                    'porder_tax' => $tax,
                    'porder_grand_total' => $grandTotal,
                    'original_total' => $grandTotal,
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
        $purchaseOrder = PurchaseOrder::with(['project', 'supplier', 'items'])->findOrFail($id);
        
        return view('admin.porder.view_pur_order', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified purchase order.
     */
    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::with(['items'])->findOrFail($id);
        
        // Authorization: Ensure user can only edit their company's POs
        if (!$purchaseOrder->isOwnedByCurrentCompany()) {
            abort(403, 'Unauthorized access to another company\'s purchase order');
        }
        
        $items = Item::active()->nonRentable()->orderByName()->get();
        $projects = Project::active()->orderByName()->get();
        $suppliers = Supplier::active()->orderByName()->get();
        $packages = \App\Models\ItemPackage::orderBy('ipack_name', 'ASC')->get();
        $taxGroups = \App\Models\TaxGroup::orderBy('id', 'ASC')->get();
        $costCodes = CostCode::orderById()->get();
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
            'po_type' => 'required',
            'po_date' => 'required|date',
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
                'porder_type' => $request->po_type,
                'porder_date' => $request->po_date,
                'porder_delivery_date' => $request->po_delivery_date,
                'porder_notes' => $request->po_notes,
                'porder_terms' => $request->po_terms,
                'porder_modified_by' => Auth::id(),
                'porder_modified_at' => now(),
            ]);

            // Delete existing items and re-add (Eloquent automatically filters by company_id)
            PurchaseOrderItem::where('porder_item_porder_ms', $id)->delete();

            // Process items
            if ($request->has('items')) {
                $total = 0;
                $tax = 0;

                foreach ($request->items as $item) {
                    $itemTotal = $item['quantity'] * $item['price'];
                    $itemTax = $itemTotal * ($item['tax_rate'] ?? 0) / 100;
                    
                    PurchaseOrderItem::create([
                        'porder_item_porder_ms' => $purchaseOrder->porder_id,
                        'porder_item_code' => $item['code'],
                        'porder_item_name' => $item['name'],
                        'porder_item_qty' => $item['quantity'],
                        'porder_item_price' => $item['price'],
                        'porder_item_tax' => $itemTax,
                        'porder_item_total' => $itemTotal + $itemTax,
                        'porder_item_ccode' => $item['cost_code'] ?? null,
                        // company_id auto-added by CompanyScope trait
                    ]);

                    $total += $itemTotal;
                    $tax += $itemTax;
                }

                $newGrandTotal = $total + $tax;
                $originalTotal = $purchaseOrder->original_total ?? $purchaseOrder->porder_grand_total;

                // Check if amount changed - create PCO if significant change
                if (abs($newGrandTotal - $purchaseOrder->porder_grand_total) > 0.01) {
                    // Create PO Change Order
                    $pco = $this->poChangeOrderService->createPoChangeOrder(
                        $purchaseOrder->porder_id,
                        Auth::id(),
                        'amount_change',
                        $purchaseOrder->porder_grand_total,
                        $newGrandTotal,
                        $request->change_reason ?? 'PO amount updated during edit'
                    );

                    // Submit for approval if required
                    try {
                        $this->approvalService->submitForApproval(
                            'PoChangeOrder',
                            $pco->poco_id,
                            $request->po_project,
                            abs($newGrandTotal - $purchaseOrder->porder_grand_total)
                        );

                        session()->flash('info', 'PO Change Order ' . $pco->poco_number . ' created and submitted for approval.');
                    } catch (\Exception $e) {
                        // No workflow, auto-approve
                        $this->poChangeOrderService->approvePoChangeOrder($pco->poco_id, Auth::id());
                        session()->flash('info', 'PO Change Order ' . $pco->poco_number . ' created and auto-approved.');
                    }
                }

                $purchaseOrder->update([
                    'porder_total' => $total,
                    'porder_tax' => $tax,
                    'porder_grand_total' => $newGrandTotal,
                ]);
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
            // Delete items first (Eloquent automatically applies company scope)
            PurchaseOrderItem::where('porder_item_porder_ms', $id)->delete();

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
            ->nonRentable()
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
            ->where('im.item_is_rentable', 0)
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
                'city' => $project->proj_city,
                'state' => $project->proj_state,
                'zip' => $project->proj_zip,
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
            'porder_general_status' => $request->status,
            'porder_modified_by' => Auth::id(),
            'porder_modified_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
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
}
