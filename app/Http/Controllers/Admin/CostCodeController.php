<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CostCode;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CostCodeController extends Controller
{
    /**
     * Display a listing of the cost codes.
     */
    public function index()
    {
        $costCodes = CostCode::orderBy('cc_no', 'asc')->get();

        return view('admin.costcodes.index', compact('costCodes'));
    }

    /**
     * Store a newly created cost code.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cc_no' => 'required|string|max:191|unique:cost_code_master,cc_no',
            'cc_description' => 'required|string|max:500',
        ]);

        CostCode::create([
            'cc_no' => trim($validated['cc_no']),
            'cc_description' => trim($validated['cc_description']),
            'cc_status' => 1,
            'cc_created_at' => Carbon::now(),
            'cc_created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.costcodes.index')->with('success', 'Cost code created successfully.');
    }

    /**
     * Update the specified cost code.
     */
    public function update(Request $request, CostCode $costcode)
    {
        $validated = $request->validate([
            'cc_no' => 'required|string|max:191|unique:cost_code_master,cc_no,' . $costcode->cc_id . ',cc_id',
            'cc_description' => 'required|string|max:500',
            'cc_status' => 'required|boolean',
        ]);

        $costcode->update([
            'cc_no' => trim($validated['cc_no']),
            'cc_description' => trim($validated['cc_description']),
            'cc_status' => $validated['cc_status'],
            'cc_modifydate' => Carbon::now(),
            'cc_modifyby' => auth()->id(),
        ]);

        return redirect()->route('admin.costcodes.index')->with('success', 'Cost code updated successfully.');
    }

    /**
     * Remove the specified cost code.
     */
    public function destroy(CostCode $costcode)
    {
        $canDelete = DB::table('purchase_order_items')->where('cc_id', $costcode->cc_id)->doesntExist()
            && DB::table('budget_line_items')->where('cc_id', $costcode->cc_id)->doesntExist();

        if (! $canDelete) {
            return redirect()->route('admin.costcodes.index')->with('error', 'Cost code is in use and cannot be deleted.');
        }

        $costcode->delete();

        return redirect()->route('admin.costcodes.index')->with('success', 'Cost code deleted successfully.');
    }
}
