<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\SupplierCatalog;
use App\Models\UnitOfMeasure;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierCatalogController extends Controller
{
    protected $poService;

    public function __construct(PurchaseOrderService $poService)
    {
        $this->poService = $poService;
    }

    /**
     * Display supplier portal dashboard.
     */
    public function dashboard($supplierId)
    {
        $dashboard = $this->poService->getSupplierDashboard($supplierId);
        return view('admin.supplier.portal.dashboard', $dashboard);
    }

    /**
     * Display supplier catalog.
     */
    public function index(Request $request, $supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        
        $query = SupplierCatalog::where('supcat_supplier', $supplierId)
            ->with(['item', 'unitOfMeasure']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('supcat_item_code', 'like', "%{$search}%")
                  ->orWhere('supcat_sku_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('supcat_status', $request->status);
        }

        $catalog = $query->orderBy('supcat_item_code')->paginate(15);

        return view('admin.supplier.portal.catalog', compact('supplier', 'catalog'));
    }

    /**
     * Show form to add item to catalog.
     */
    public function create($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        
        // Get items not already in catalog
        $existingItems = SupplierCatalog::where('supcat_supplier', $supplierId)
            ->pluck('supcat_item_code')
            ->toArray();

        $items = Item::active()
            ->whereNotIn('item_code', $existingItems)
            ->orderByName()
            ->get();

        $uoms = UnitOfMeasure::active()->orderByName()->get();

        return view('admin.supplier.portal.catalog_create', compact('supplier', 'items', 'uoms'));
    }

    /**
     * Store new catalog entry.
     */
    public function store(Request $request, $supplierId)
    {
        $request->validate([
            'item_code' => 'required|exists:item_master,item_code',
            'sku_no' => 'required|string|max:100',
            'uom_id' => 'required|exists:unit_of_measure_tab,uom_id',
            'price' => 'required|numeric|min:0',
            'effective_date' => 'nullable|date',
            'details' => 'nullable|string|max:500',
        ]);

        // Check for duplicate
        $exists = SupplierCatalog::where('supcat_supplier', $supplierId)
            ->where('supcat_item_code', $request->item_code)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'This item is already in the catalog.');
        }

        try {
            $this->poService->addToCatalog($supplierId, [
                'item_code' => $request->item_code,
                'sku_no' => $request->sku_no,
                'uom_id' => $request->uom_id,
                'price' => $request->price,
                'effective_date' => $request->effective_date,
                'details' => $request->details,
            ]);

            return redirect()->route('admin.supplier.catalog.index', $supplierId)
                ->with('success', 'Item added to catalog successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error adding item: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit catalog entry.
     */
    public function edit($supplierId, $catalogId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $catalogEntry = SupplierCatalog::where('supcat_supplier', $supplierId)
            ->where('supcat_id', $catalogId)
            ->with(['item', 'unitOfMeasure'])
            ->firstOrFail();

        $uoms = UnitOfMeasure::active()->orderByName()->get();

        return view('admin.supplier.portal.catalog_edit', compact('supplier', 'catalogEntry', 'uoms'));
    }

    /**
     * Update catalog entry.
     */
    public function update(Request $request, $supplierId, $catalogId)
    {
        $catalogEntry = SupplierCatalog::where('supcat_supplier', $supplierId)
            ->where('supcat_id', $catalogId)
            ->firstOrFail();

        $request->validate([
            'sku_no' => 'required|string|max:100',
            'uom_id' => 'required|exists:unit_of_measure_tab,uom_id',
            'price' => 'required|numeric|min:0',
            'effective_date' => 'nullable|date',
            'details' => 'nullable|string|max:500',
            'status' => 'required|in:0,1',
        ]);

        try {
            // Record price change if different
            if ($catalogEntry->supcat_price != $request->price) {
                $item = Item::where('item_code', $catalogEntry->supcat_item_code)->first();
                if ($item) {
                    $this->poService->updateItemPrice(
                        $item->item_id,
                        $supplierId,
                        $catalogEntry->supcat_price,
                        $request->price,
                        $request->effective_date
                    );
                }
            }

            $catalogEntry->update([
                'supcat_sku_no' => $request->sku_no,
                'supcat_uom' => $request->uom_id,
                'supcat_price' => $request->price,
                'supcat_lastdate' => $request->effective_date ?? now()->toDateString(),
                'supcat_details' => $request->details,
                'supcat_status' => $request->status,
                'supcat_modifydate' => now(),
                'supcat_modifyby' => auth()->id(),
            ]);

            return redirect()->route('admin.supplier.catalog.index', $supplierId)
                ->with('success', 'Catalog entry updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error updating entry: ' . $e->getMessage());
        }
    }

    /**
     * Remove catalog entry.
     */
    public function destroy($supplierId, $catalogId)
    {
        $catalogEntry = SupplierCatalog::where('supcat_supplier', $supplierId)
            ->where('supcat_id', $catalogId)
            ->firstOrFail();

        try {
            $catalogEntry->delete();

            return redirect()->route('admin.supplier.catalog.index', $supplierId)
                ->with('success', 'Catalog entry removed successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error removing entry: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update prices.
     */
    public function bulkUpdatePrices(Request $request, $supplierId)
    {
        $request->validate([
            'prices' => 'required|array',
            'prices.*.catalog_id' => 'required|exists:supplier_catalog,supcat_id',
            'prices.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->prices as $priceData) {
                $catalogEntry = SupplierCatalog::find($priceData['catalog_id']);
                
                if ($catalogEntry && $catalogEntry->supcat_supplier == $supplierId) {
                    if ($catalogEntry->supcat_price != $priceData['price']) {
                        $item = Item::where('item_code', $catalogEntry->supcat_item_code)->first();
                        if ($item) {
                            $this->poService->updateItemPrice(
                                $item->item_id,
                                $supplierId,
                                $catalogEntry->supcat_price,
                                $priceData['price']
                            );
                        }
                    }
                }
            }

            DB::commit();

            return back()->with('success', 'Prices updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating prices: ' . $e->getMessage());
        }
    }

    /**
     * Import catalog from CSV.
     */
    public function import(Request $request, $supplierId)
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
                    // Expected columns: item_code, sku_no, uom_id, price, details
                    if (count($row) >= 4) {
                        SupplierCatalog::updateOrCreate(
                            [
                                'supcat_supplier' => $supplierId,
                                'supcat_item_code' => $row[0],
                            ],
                            [
                                'supcat_sku_no' => $row[1],
                                'supcat_uom' => $row[2],
                                'supcat_price' => $row[3],
                                'supcat_details' => $row[4] ?? null,
                                'supcat_lastdate' => now()->toDateString(),
                                'supcat_createdate' => now(),
                                'supcat_createby' => auth()->id(),
                                'supcat_status' => 1,
                            ]
                        );
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row {$imported}: " . $e->getMessage();
                }
            }

            fclose($handle);

            $message = "Imported {$imported} catalog entries.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', array_slice($errors, 0, 5));
            }

            return back()->with('success', $message);
        }

        $supplier = Supplier::findOrFail($supplierId);
        return view('admin.supplier.portal.catalog_import', compact('supplier'));
    }

    /**
     * Export catalog to CSV.
     */
    public function export($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $catalog = SupplierCatalog::where('supcat_supplier', $supplierId)
            ->with(['item', 'unitOfMeasure'])
            ->orderBy('supcat_item_code')
            ->get();

        $filename = 'catalog_' . str_replace(' ', '_', $supplier->sup_name) . '_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($catalog) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Item Code', 'Item Name', 'SKU', 'UOM', 'Price', 'Last Updated', 'Status']);
            
            foreach ($catalog as $entry) {
                fputcsv($file, [
                    $entry->supcat_item_code,
                    $entry->item ? $entry->item->item_name : '',
                    $entry->supcat_sku_no,
                    $entry->unitOfMeasure ? $entry->unitOfMeasure->uom_name : '',
                    $entry->supcat_price,
                    $entry->supcat_lastdate,
                    $entry->supcat_status ? 'Active' : 'Inactive',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Supplier performance report.
     */
    public function performance($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        abort_unless($supplier->company_id === session('company_id'), 403);
        
        $companyId = session('company_id');
        $performance = DB::table('vw_supplier_performance')
            ->where('sup_id', $supplierId)
            ->where('company_id', $companyId)
            ->first();

        // Get order history (company-scoped)
        $orderHistory = DB::table('purchase_order_master')
            ->where('porder_supplier_ms', $supplierId)
            ->where('company_id', $companyId)
            ->where('porder_status', 1)
            ->selectRaw('YEAR(porder_createdate) as year, MONTH(porder_createdate) as month, COUNT(*) as order_count, SUM(porder_total_amount + porder_total_tax) as total_amount')
            ->groupBy(DB::raw('YEAR(porder_createdate)'), DB::raw('MONTH(porder_createdate)'))
            ->orderBy('year', 'DESC')
            ->orderBy('month', 'DESC')
            ->limit(12)
            ->get();

        return view('admin.supplier.portal.performance', compact('supplier', 'performance', 'orderHistory'));
    }
}
