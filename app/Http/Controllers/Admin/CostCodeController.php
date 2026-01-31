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

    /**
     * Display hierarchical cost code structure.
     */
    public function hierarchy()
    {
        // Get all cost codes organized by hierarchy
        $rootCodes = CostCode::whereNull('parent_code')
            ->orWhere('parent_code', '')
            ->orderBy('full_code')
            ->get();

        // Build hierarchy tree
        $hierarchy = $this->buildHierarchyTree($rootCodes);

        return view('admin.costcodes.hierarchy', compact('hierarchy', 'rootCodes'));
    }

    /**
     * Store a hierarchical cost code.
     */
    public function storeHierarchical(Request $request)
    {
        $validated = $request->validate([
            'category_code' => 'required|string|max:10',
            'subcategory_code' => 'nullable|string|max:10',
            'detail_code' => 'nullable|string|max:10',
            'description' => 'required|string|max:500',
            'parent_code' => 'nullable|string|max:50',
        ]);

        // Build full code (XX-XX-XX format)
        $fullCode = $validated['category_code'];
        $level = 1;

        if (!empty($validated['subcategory_code'])) {
            $fullCode .= '-' . $validated['subcategory_code'];
            $level = 2;
        }

        if (!empty($validated['detail_code'])) {
            $fullCode .= '-' . $validated['detail_code'];
            $level = 3;
        }

        // Check if code already exists
        if (CostCode::where('full_code', $fullCode)->exists()) {
            return back()->withInput()->with('error', 'Cost code already exists: ' . $fullCode);
        }

        CostCode::create([
            'cc_no' => $fullCode,
            'cc_description' => trim($validated['description']),
            'parent_code' => $validated['parent_code'] ?? null,
            'category_code' => $validated['category_code'],
            'subcategory_code' => $validated['subcategory_code'] ?? null,
            'full_code' => $fullCode,
            'level' => $level,
            'cc_status' => 1,
            'cc_created_at' => Carbon::now(),
            'cc_created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Hierarchical cost code created: ' . $fullCode);
    }

    /**
     * Get child codes for a parent (AJAX).
     */
    public function getChildCodes($parentCode)
    {
        $children = CostCode::where('parent_code', $parentCode)
            ->orderBy('full_code')
            ->get()
            ->map(function ($code) {
                return [
                    'id' => $code->cc_id,
                    'code' => $code->full_code,
                    'cc_no' => $code->cc_no,
                    'description' => $code->cc_description,
                    'level' => $code->level,
                    'has_children' => $code->children()->count() > 0,
                ];
            });

        return response()->json($children);
    }

    /**
     * Build hierarchy tree recursively.
     */
    protected function buildHierarchyTree($codes)
    {
        $tree = [];

        foreach ($codes as $code) {
            $node = [
                'code' => $code,
                'children' => $this->buildHierarchyTree($code->children),
            ];
            $tree[] = $node;
        }

        return $tree;
    }
}
