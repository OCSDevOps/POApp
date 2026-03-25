<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CostCode;
use App\Models\CostCodeTemplate;
use App\Models\CostCodeTemplateItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CostCodeTemplateController extends Controller
{
    /**
     * List all cost code templates.
     */
    public function index()
    {
        $templates = CostCodeTemplate::withCount('items')
            ->orderBy('cct_name')
            ->paginate(25);

        return view('admin.costcode-templates.index', compact('templates'));
    }

    /**
     * Show form to create a new template.
     */
    public function create()
    {
        $costCodes = CostCode::orderBy('cc_full_code')->get();

        // Group cost codes by level for hierarchical display
        $parentCodes = $costCodes->where('cc_level', CostCode::LEVEL_PARENT);
        $categoryCodes = $costCodes->where('cc_level', CostCode::LEVEL_CATEGORY);
        $subcategoryCodes = $costCodes->where('cc_level', CostCode::LEVEL_SUBCATEGORY);

        return view('admin.costcode-templates.create', compact(
            'costCodes',
            'parentCodes',
            'categoryCodes',
            'subcategoryCodes'
        ));
    }

    /**
     * Store a new template.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cct_name' => 'required|string|max:150',
            'cct_description' => 'nullable|string|max:500',
            'cost_code_ids' => 'required|array|min:1',
            'cost_code_ids.*' => 'exists:cost_code_master,cc_id',
        ]);

        try {
            DB::beginTransaction();

            $template = CostCodeTemplate::create([
                'company_id' => session('company_id'),
                'cct_name' => $request->cct_name,
                'cct_description' => $request->cct_description,
                'cct_status' => 1,
                'cct_createby' => session('user_id'),
                'cct_createdate' => now(),
            ]);

            foreach ($request->cost_code_ids as $sort => $ccId) {
                CostCodeTemplateItem::create([
                    'ccti_template_id' => $template->cct_id,
                    'ccti_cost_code_id' => $ccId,
                    'ccti_sort_order' => $sort + 1,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.costcode-templates.show', $template->cct_id)
                ->with('success', 'Cost code template created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating template: ' . $e->getMessage());
        }
    }

    /**
     * Show template details.
     */
    public function show($id)
    {
        $template = CostCodeTemplate::findOrFail($id);
        $items = CostCodeTemplateItem::with('costCode')
            ->where('ccti_template_id', $id)
            ->orderBy('ccti_sort_order')
            ->get();

        return view('admin.costcode-templates.show', compact('template', 'items'));
    }

    /**
     * Show form to edit a template.
     */
    public function edit($id)
    {
        $template = CostCodeTemplate::findOrFail($id);
        $selectedIds = CostCodeTemplateItem::where('ccti_template_id', $id)
            ->pluck('ccti_cost_code_id')
            ->toArray();

        $costCodes = CostCode::orderBy('cc_full_code')->get();
        $parentCodes = $costCodes->where('cc_level', CostCode::LEVEL_PARENT);
        $categoryCodes = $costCodes->where('cc_level', CostCode::LEVEL_CATEGORY);
        $subcategoryCodes = $costCodes->where('cc_level', CostCode::LEVEL_SUBCATEGORY);

        return view('admin.costcode-templates.edit', compact(
            'template',
            'selectedIds',
            'costCodes',
            'parentCodes',
            'categoryCodes',
            'subcategoryCodes'
        ));
    }

    /**
     * Update a template.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'cct_name' => 'required|string|max:150',
            'cct_description' => 'nullable|string|max:500',
            'cost_code_ids' => 'required|array|min:1',
            'cost_code_ids.*' => 'exists:cost_code_master,cc_id',
        ]);

        try {
            DB::beginTransaction();

            $template = CostCodeTemplate::findOrFail($id);
            $template->update([
                'cct_name' => $request->cct_name,
                'cct_description' => $request->cct_description,
                'cct_modifyby' => session('user_id'),
                'cct_modifydate' => now(),
            ]);

            // Replace all items
            CostCodeTemplateItem::where('ccti_template_id', $id)->delete();

            foreach ($request->cost_code_ids as $sort => $ccId) {
                CostCodeTemplateItem::create([
                    'ccti_template_id' => $id,
                    'ccti_cost_code_id' => $ccId,
                    'ccti_sort_order' => $sort + 1,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.costcode-templates.show', $id)
                ->with('success', 'Cost code template updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating template: ' . $e->getMessage());
        }
    }

    /**
     * Delete a template.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            CostCodeTemplateItem::where('ccti_template_id', $id)->delete();
            CostCodeTemplate::where('cct_id', $id)->delete();

            DB::commit();

            return redirect()
                ->route('admin.costcode-templates.index')
                ->with('success', 'Template deleted.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting template: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Get cost codes for a template (used during project setup).
     */
    public function getCostCodes($id)
    {
        $items = CostCodeTemplateItem::with('costCode')
            ->where('ccti_template_id', $id)
            ->orderBy('ccti_sort_order')
            ->get()
            ->map(function ($item) {
                return [
                    'cc_id' => $item->costCode->cc_id,
                    'cc_full_code' => $item->costCode->cc_full_code,
                    'cc_description' => $item->costCode->cc_description,
                    'cc_level' => $item->costCode->cc_level,
                ];
            });

        return response()->json($items);
    }
}
