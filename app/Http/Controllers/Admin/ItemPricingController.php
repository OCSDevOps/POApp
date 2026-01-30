<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemPricing;
use Illuminate\Http\Request;

class ItemPricingController extends Controller
{
    /**
     * Read-only view of supplier pricing.
     */
    public function index(Request $request)
    {
        $query = ItemPricing::with(['item', 'supplier', 'project']);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        $pricing = $query->orderBy('effective_from', 'desc')->paginate(25);

        return view('admin.pricing.index', compact('pricing'));
    }
}
