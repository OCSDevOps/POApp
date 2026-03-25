<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemPricing;
use App\Models\Project;
use App\Services\ItemPricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemPricingController extends Controller
{
    private ItemPricingService $pricingService;

    public function __construct(ItemPricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * List pricing for the authenticated supplier.
     */
    public function index()
    {
        $supplierId = Auth::guard('supplier')->id() ? Auth::guard('supplier')->user()->supplier_id : null;

        $pricing = ItemPricing::with(['item', 'project'])
            ->where('supplier_id', $supplierId)
            ->orderBy('effective_from', 'desc')
            ->paginate(20);

        return view('supplier.pricing.index', compact('pricing'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $items = Item::active()->orderByName()->get();
        $projects = Project::active()->orderByName()->get();

        return view('supplier.pricing.create', compact('items', 'projects'));
    }

    /**
     * Store pricing.
     */
    public function store(Request $request)
    {
        $supplierUser = Auth::guard('supplier')->user();

        $request->validate([
            'item_id' => 'required|integer|exists:item_master,item_id',
            'unit_price' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'project_id' => 'nullable|integer|exists:project_master,proj_id',
        ]);

        $this->pricingService->upsert([
            'item_id' => $request->item_id,
            'supplier_id' => $supplierUser->supplier_id,
            'project_id' => $request->project_id,
            'unit_price' => $request->unit_price,
            'effective_from' => $request->effective_from,
            'effective_to' => $request->effective_to,
            'company_id' => $supplierUser->company_id,
        ]);

        return redirect()->route('supplier.pricing.index')->with('status', 'Pricing saved.');
    }

    /**
     * Show import form.
     */
    public function importForm()
    {
        return view('supplier.pricing.import');
    }

    /**
     * Handle CSV import (simple fgetcsv parser).
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt',
        ]);

        $supplierUser = Auth::guard('supplier')->user();
        $rows = [];

        if (($handle = fopen($request->file('csv')->getRealPath(), 'r')) !== false) {
            // Header: item_id,supplier_id(optional ignored),project_id,unit_price,effective_from,effective_to
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($data) < 4) {
                    continue;
                }

                if (! is_numeric($data[0])) {
                    continue;
                }

                $itemId = (int) $data[0];
                $projectId = $data[2] !== '' ? (int) $data[2] : null;

                if (! Item::where('item_id', $itemId)->exists()) {
                    continue;
                }

                if ($projectId && ! Project::where('proj_id', $projectId)->exists()) {
                    continue;
                }

                $rows[] = [
                    'item_id' => $itemId,
                    'supplier_id' => $supplierUser->supplier_id,
                    'project_id' => $projectId,
                    'unit_price' => (float) $data[3],
                    'effective_from' => ! empty($data[4]) ? $data[4] : now()->toDateString(),
                    'effective_to' => ! empty($data[5]) ? $data[5] : null,
                ];
            }
            fclose($handle);
        }

        $imported = $this->pricingService->import($rows, $supplierUser->company_id);

        return redirect()->route('supplier.pricing.index')->with('status', "{$imported} price rows imported.");
    }
}
