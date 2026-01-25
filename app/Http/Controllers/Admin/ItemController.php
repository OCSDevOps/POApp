<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CostCode;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemPriceHistory;
use App\Models\SupplierCatalog;
use App\Models\UnitOfMeasure;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    protected $poService;

    public function __construct(PurchaseOrderService $poService)
    {
        $this->poService = $poService;
    }

    /**
     * Display a listing of items.
     */
    public function index(Request $request)
    {
        $query = Item::with(['category', 'costCode', 'unitOfMeasure']);

        // Filters
        if ($request->filled('category_id')) {
            $query->where('item_cat_ms', $request->category_id);
        }

        if ($request->filled('cost_code_id')) {
            $query->where('item_ccode_ms', $request->cost_code_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%")
                  ->orWhere('item_description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('item_status', $request->status);
        }

        $items = $query->orderBy('item_name')->paginate(15);
        $categories = ItemCategory::active()->orderByName()->get();
        $costCodes = CostCode::active()->orderByCode()->get();

        return view('admin.item.index', compact('items', 'categories', 'costCodes'));
    }

    /**
     * Show the form for creating a new item.
     */
    public function create()
    {
        $categories = ItemCategory::active()->orderByName()->get();
        $costCodes = CostCode::active()->orderByCode()->get();
        $uoms = UnitOfMeasure::active()->orderByName()->get();

        return view('admin.item.create', compact('categories', 'costCodes', 'uoms'));
    }

    /**
     * Store a newly created item.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_code' => 'required|string|max:50|unique:item_master,item_code',
            'item_name' => 'required|string|max:200',
            'category_id' => 'required|exists:itemcategory_master,itemcat_id',
            'cost_code_id' => 'nullable|exists:costcode_master,ccode_id',
            'uom_id' => 'nullable|exists:unit_of_measure_tab,uom_id',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            Item::create([
                'item_code' => $request->item_code,
                'item_name' => $request->item_name,
                'item_cat_ms' => $request->category_id,
                'item_ccode_ms' => $request->cost_code_id,
                'item_uom_id' => $request->uom_id,
                'item_description' => $request->description,
                'item_createdate' => now(),
                'item_createby' => auth()->id(),
                'item_status' => 1,
            ]);

            return redirect()->route('admin.item.index')
                ->with('success', 'Item created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating item: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified item.
     */
    public function show($id)
    {
        $item = Item::with(['category', 'costCode', 'unitOfMeasure'])->findOrFail($id);

        // Get supplier catalog entries
        $supplierCatalog = SupplierCatalog::where('supcat_item_code', $item->item_code)
            ->where('supcat_status', 1)
            ->with('supplier')
            ->orderBy('supcat_price')
            ->get();

        // Get price history
        $priceHistory = ItemPriceHistory::where('iph_item_id', $id)
            ->with('supplier')
            ->orderBy('iph_effective_date', 'DESC')
            ->limit(20)
            ->get();

        // Get pricing summary from view
        $pricingSummary = DB::table('vw_item_pricing_summary')
            ->where('item_id', $id)
            ->first();

        return view('admin.item.show', compact('item', 'supplierCatalog', 'priceHistory', 'pricingSummary'));
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit($id)
    {
        $item = Item::findOrFail($id);
        $categories = ItemCategory::active()->orderByName()->get();
        $costCodes = CostCode::active()->orderByCode()->get();
        $uoms = UnitOfMeasure::active()->orderByName()->get();

        return view('admin.item.edit', compact('item', 'categories', 'costCodes', 'uoms'));
    }

    /**
     * Update the specified item.
     */
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $request->validate([
            'item_code' => 'required|string|max:50|unique:item_master,item_code,' . $id . ',item_id',
            'item_name' => 'required|string|max:200',
            'category_id' => 'required|exists:itemcategory_master,itemcat_id',
            'cost_code_id' => 'nullable|exists:costcode_master,ccode_id',
            'uom_id' => 'nullable|exists:unit_of_measure_tab,uom_id',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:0,1',
        ]);

        try {
            $item->update([
                'item_code' => $request->item_code,
                'item_name' => $request->item_name,
                'item_cat_ms' => $request->category_id,
                'item_ccode_ms' => $request->cost_code_id,
                'item_uom_id' => $request->uom_id,
                'item_description' => $request->description,
                'item_modifydate' => now(),
                'item_modifyby' => auth()->id(),
                'item_status' => $request->status,
            ]);

            return redirect()->route('admin.item.show', $item->item_id)
                ->with('success', 'Item updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error updating item: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified item.
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        // Check if item is used in any PO
        $usedInPo = DB::table('porder_detail')
            ->where('po_detail_item', $item->item_code)
            ->exists();

        if ($usedInPo) {
            return back()->with('error', 'Cannot delete item that is used in purchase orders. Deactivate instead.');
        }

        try {
            // Delete related records
            SupplierCatalog::where('supcat_item_code', $item->item_code)->delete();
            ItemPriceHistory::where('iph_item_id', $id)->delete();
            
            $item->delete();

            return redirect()->route('admin.item.index')
                ->with('success', 'Item deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting item: ' . $e->getMessage());
        }
    }

    /**
     * Price comparison across suppliers.
     */
    public function priceComparison($id)
    {
        $item = Item::findOrFail($id);
        $comparison = $this->poService->getPriceComparison($item->item_code);

        return view('admin.item.price_comparison', compact('item', 'comparison'));
    }

    /**
     * Price history for an item.
     */
    public function priceHistory($id, Request $request)
    {
        $item = Item::findOrFail($id);
        $history = $this->poService->getItemPriceHistory($id, $request->supplier_id);

        return view('admin.item.price_history', compact('item', 'history'));
    }

    /**
     * Update item price.
     */
    public function updatePrice(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier_master,sup_id',
            'new_price' => 'required|numeric|min:0',
            'effective_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $item = Item::findOrFail($id);

        // Get current price
        $catalog = SupplierCatalog::where('supcat_item_code', $item->item_code)
            ->where('supcat_supplier', $request->supplier_id)
            ->first();

        $oldPrice = $catalog ? $catalog->supcat_price : 0;

        try {
            $this->poService->updateItemPrice(
                $id,
                $request->supplier_id,
                $oldPrice,
                $request->new_price,
                $request->effective_date,
                $request->notes
            );

            return back()->with('success', 'Item price updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating price: ' . $e->getMessage());
        }
    }

    /**
     * Pricing summary report.
     */
    public function pricingSummary(Request $request)
    {
        $query = DB::table('vw_item_pricing_summary');

        if ($request->filled('category_id')) {
            $query->where('item_cat_ms', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%");
            });
        }

        $summary = $query->orderBy('item_name')->paginate(20);
        $categories = ItemCategory::active()->orderByName()->get();

        return view('admin.item.pricing_summary', compact('summary', 'categories'));
    }

    /**
     * Import items from CSV.
     */
    public function import(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:2048',
            ]);

            $file = $request->file('file');
            $handle = fopen($file->getPathname(), 'r');
            
            // Skip header row
            fgetcsv($handle);
            
            $imported = 0;
            $errors = [];

            while (($row = fgetcsv($handle)) !== false) {
                try {
                    // Expected columns: item_code, item_name, category_id, cost_code_id, description
                    if (count($row) >= 3) {
                        Item::updateOrCreate(
                            ['item_code' => $row[0]],
                            [
                                'item_name' => $row[1],
                                'item_cat_ms' => $row[2] ?? null,
                                'item_ccode_ms' => $row[3] ?? null,
                                'item_description' => $row[4] ?? null,
                                'item_createdate' => now(),
                                'item_createby' => auth()->id(),
                                'item_status' => 1,
                            ]
                        );
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row {$imported}: " . $e->getMessage();
                }
            }

            fclose($handle);

            $message = "Imported {$imported} items.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', array_slice($errors, 0, 5));
            }

            return back()->with('success', $message);
        }

        return view('admin.item.import');
    }

    /**
     * Export items to CSV.
     */
    public function export(Request $request)
    {
        $items = Item::with(['category', 'costCode'])
            ->when($request->filled('category_id'), function ($q) use ($request) {
                return $q->where('item_cat_ms', $request->category_id);
            })
            ->orderBy('item_name')
            ->get();

        $filename = 'items_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Item Code', 'Item Name', 'Category', 'Cost Code', 'Description', 'Status']);
            
            foreach ($items as $item) {
                fputcsv($file, [
                    $item->item_code,
                    $item->item_name,
                    $item->category ? $item->category->itemcat_name : '',
                    $item->costCode ? $item->costCode->ccode_code : '',
                    $item->item_description,
                    $item->item_status ? 'Active' : 'Inactive',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
