<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UnitOfMeasureController extends Controller
{
    /**
     * Display a listing of the units of measure.
     */
    public function index()
    {
        $units = UnitOfMeasure::orderBy('uom_name', 'asc')->get();

        return view('admin.uom.index', compact('units'));
    }

    /**
     * Store a newly created unit of measure.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'uom_name' => 'required|string|max:191|unique:unit_of_measure_tab,uom_name',
            'uom_detail' => 'nullable|string|max:500',
        ]);

        UnitOfMeasure::create([
            'uom_name' => trim($validated['uom_name']),
            'uom_detail' => trim($validated['uom_detail'] ?? ''),
            'uom_status' => 1,
            'uom_createdate' => Carbon::now(),
            'uom_createby' => auth()->id(),
        ]);

        return redirect()->route('admin.uom.index')->with('success', 'Unit of measure created successfully.');
    }

    /**
     * Update the specified unit of measure.
     */
    public function update(Request $request, UnitOfMeasure $uom)
    {
        $validated = $request->validate([
            'uom_name' => 'required|string|max:191|unique:unit_of_measure_tab,uom_name,' . $uom->uom_id . ',uom_id',
            'uom_detail' => 'nullable|string|max:500',
            'uom_status' => 'required|boolean',
        ]);

        $uom->update([
            'uom_name' => trim($validated['uom_name']),
            'uom_detail' => trim($validated['uom_detail'] ?? ''),
            'uom_status' => $validated['uom_status'],
            'uom_modifydate' => Carbon::now(),
        ]);

        return redirect()->route('admin.uom.index')->with('success', 'Unit of measure updated successfully.');
    }

    /**
     * Remove the specified unit of measure.
     */
    public function destroy(UnitOfMeasure $uom)
    {
        $companyId = session('company_id');
        $inUse = DB::table('item_master')
                ->where('item_unit_ms', $uom->uom_id)
                ->where('company_id', $companyId)
                ->exists()
            || DB::table('purchase_order_items')
                ->where('porder_item_uom', $uom->uom_id)
                ->where('company_id', $companyId)
                ->exists();

        if ($inUse) {
            return redirect()->route('admin.uom.index')->with('error', 'Unit is linked to items or purchase orders and cannot be deleted.');
        }

        $uom->delete();

        return redirect()->route('admin.uom.index')->with('success', 'Unit of measure deleted successfully.');
    }
}
