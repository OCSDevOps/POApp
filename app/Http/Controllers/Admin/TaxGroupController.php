<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaxGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TaxGroupController extends Controller
{
    public function index()
    {
        $taxGroups = TaxGroup::orderBy('id', 'asc')->get();

        return view('admin.taxgroups.index', compact('taxGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191|unique:taxgroup_master,name',
            'percentage' => 'required|numeric',
            'description' => 'nullable|string|max:500',
        ]);

        TaxGroup::create([
            'name' => trim($validated['name']),
            'percentage' => $validated['percentage'],
            'description' => trim($validated['description'] ?? ''),
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.taxgroups.index')->with('success', 'Tax group created successfully.');
    }

    public function update(Request $request, TaxGroup $taxgroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191|unique:taxgroup_master,name,' . $taxgroup->id,
            'percentage' => 'required|numeric',
            'description' => 'nullable|string|max:500',
        ]);

        $taxgroup->update([
            'name' => trim($validated['name']),
            'percentage' => $validated['percentage'],
            'description' => trim($validated['description'] ?? ''),
        ]);

        return redirect()->route('admin.taxgroups.index')->with('success', 'Tax group updated successfully.');
    }

    public function destroy(TaxGroup $taxgroup)
    {
        $companyId = session('company_id');
        $inUse = DB::table('purchase_order_details')
                ->where('po_detail_tax_group', $taxgroup->id)
                ->where('company_id', $companyId)
                ->exists()
            || DB::table('request_purchase_order_details')
                ->where('rfq_detail_tax_group', $taxgroup->id)
                ->where('company_id', $companyId)
                ->exists();

        if ($inUse) {
            return redirect()->route('admin.taxgroups.index')->with('error', 'Tax group is in use and cannot be deleted.');
        }

        $taxgroup->delete();

        return redirect()->route('admin.taxgroups.index')->with('success', 'Tax group deleted successfully.');
    }
}
