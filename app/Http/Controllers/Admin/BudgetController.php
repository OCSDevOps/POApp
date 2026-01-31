<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\CostCode;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BudgetController extends Controller
{
    /**
     * Display a listing of budgets.
     */
    public function index(Request $request)
    {
        $query = Budget::with(['project', 'costCode']);

        // Filters
        if ($request->filled('project_id')) {
            $query->where('budget_project_id', $request->project_id);
        }

        if ($request->filled('cost_code_id')) {
            $query->where('budget_cost_code_id', $request->cost_code_id);
        }

        if ($request->filled('fiscal_year')) {
            $query->where('budget_fiscal_year', $request->fiscal_year);
        }

        $budgets = $query->orderBy('budget_created_at', 'DESC')->paginate(15);
        $projects = Project::active()->orderByName()->get();
        $costCodes = CostCode::active()->orderByCode()->get();

        // Get budget summary
        $summary = DB::table('vw_budget_summary')
            ->where('company_id', session('company_id'))
            ->when($request->filled('project_id'), function ($q) use ($request) {
                return $q->where('proj_id', $request->project_id);
            })
            ->get();

        return view('admin.budget.index', compact('budgets', 'projects', 'costCodes', 'summary'));
    }

    /**
     * Show the form for creating a new budget.
     */
    public function create()
    {
        $projects = Project::active()->orderByName()->get();
        $costCodes = CostCode::active()->orderByCode()->get();

        return view('admin.budget.create', compact('projects', 'costCodes'));
    }

    /**
     * Store a newly created budget.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:project_master,proj_id',
            'cost_code_id' => 'required|exists:costcode_master,ccode_id',
            'fiscal_year' => 'required|integer|min:2020|max:2100',
            'original_amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        // Check for duplicate budget
        $exists = Budget::where('budget_project_id', $request->project_id)
            ->where('budget_cost_code_id', $request->cost_code_id)
            ->where('budget_fiscal_year', $request->fiscal_year)
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->with('error', 'A budget already exists for this project, cost code, and fiscal year.');
        }

        try {
            Budget::create([
                'budget_project_id' => $request->project_id,
                'budget_cost_code_id' => $request->cost_code_id,
                'budget_fiscal_year' => $request->fiscal_year,
                'budget_original_amount' => $request->original_amount,
                'budget_revised_amount' => $request->original_amount,
                'budget_committed_amount' => 0,
                'budget_spent_amount' => 0,
                'budget_description' => $request->description,
                'budget_status' => 1,
                'budget_created_by' => auth()->id(),
                'budget_created_at' => now(),
                'company_id' => session('company_id'),
            ]);

            return redirect()->route('admin.budget.index')
                ->with('success', 'Budget created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating budget: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified budget.
     */
    public function show($id)
    {
        $budget = Budget::with(['project', 'costCode'])->findOrFail($id);
        abort_unless($budget->company_id === session('company_id'), 403);

        // Get related purchase orders
        $purchaseOrders = DB::table('porder_master')
            ->join('porder_detail', 'porder_master.porder_id', '=', 'porder_detail.po_detail_porder_ms')
            ->join('item_master', 'porder_detail.po_detail_item', '=', 'item_master.item_code')
            ->where('porder_master.company_id', session('company_id'))
            ->where('porder_master.porder_project_ms', $budget->budget_project_id)
            ->where('item_master.item_ccode_ms', $budget->budget_cost_code_id)
            ->select('porder_master.*', DB::raw('SUM(porder_detail.po_detail_total) as total_amount'))
            ->groupBy('porder_master.porder_id')
            ->get();

        return view('admin.budget.show', compact('budget', 'purchaseOrders'));
    }

    /**
     * Show the form for editing the specified budget.
     */
    public function edit($id)
    {
        $budget = Budget::findOrFail($id);
        abort_unless($budget->company_id === session('company_id'), 403);
        $projects = Project::active()->orderByName()->get();
        $costCodes = CostCode::active()->orderByCode()->get();

        return view('admin.budget.edit', compact('budget', 'projects', 'costCodes'));
    }

    /**
     * Update the specified budget.
     */
    public function update(Request $request, $id)
    {
        $budget = Budget::findOrFail($id);
        abort_unless($budget->company_id === session('company_id'), 403);

        $request->validate([
            'revised_amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:0,1',
        ]);

        // Ensure revised amount is not less than committed amount
        if ($request->revised_amount < $budget->budget_committed_amount) {
            return back()->withInput()
                ->with('error', 'Revised amount cannot be less than committed amount.');
        }

        try {
            $budget->update([
                'budget_revised_amount' => $request->revised_amount,
                'budget_description' => $request->description,
                'budget_status' => $request->status,
                'budget_modified_by' => auth()->id(),
                'budget_modified_at' => now(),
            ]);

            return redirect()->route('admin.budget.show', $budget->budget_id)
                ->with('success', 'Budget updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error updating budget: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified budget.
     */
    public function destroy($id)
    {
        $budget = Budget::findOrFail($id);
        abort_unless($budget->company_id === session('company_id'), 403);

        // Don't allow deletion if there are commitments
        if ($budget->budget_committed_amount > 0) {
            return back()->with('error', 'Cannot delete budget with existing commitments.');
        }

        try {
            $budget->delete();
            return redirect()->route('admin.budget.index')
                ->with('success', 'Budget deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting budget: ' . $e->getMessage());
        }
    }

    /**
     * Budget transfer between cost codes.
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'from_budget_id' => 'required|exists:budget_master,budget_id',
            'to_budget_id' => 'required|exists:budget_master,budget_id|different:from_budget_id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        $fromBudget = Budget::findOrFail($request->from_budget_id);
        $toBudget = Budget::findOrFail($request->to_budget_id);

        // Check if transfer is possible
        if ($fromBudget->remaining_amount < $request->amount) {
            return back()->withInput()
                ->with('error', 'Insufficient budget available for transfer.');
        }

        DB::beginTransaction();

        try {
            // Reduce from budget
            $fromBudget->update([
                'budget_revised_amount' => $fromBudget->budget_revised_amount - $request->amount,
                'budget_modified_by' => auth()->id(),
                'budget_modified_at' => now(),
            ]);

            // Increase to budget
            $toBudget->update([
                'budget_revised_amount' => $toBudget->budget_revised_amount + $request->amount,
                'budget_modified_by' => auth()->id(),
                'budget_modified_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Budget transfer completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error transferring budget: ' . $e->getMessage());
        }
    }

    /**
     * Budget summary report.
     */
    public function summary(Request $request)
    {
        $query = DB::table('vw_budget_summary')
            ->where('company_id', session('company_id'));

        if ($request->filled('project_id')) {
            $query->where('proj_id', $request->project_id);
        }

        $summary = $query->get();
        $projects = Project::active()->orderByName()->get();

        return view('admin.budget.summary', compact('summary', 'projects'));
    }

    /**
     * Import budgets from Procore.
     */
    public function importFromProcore(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:project_master,proj_id',
        ]);

        try {
            $procoreService = app(\App\Services\ProcoreService::class);
            $result = $procoreService->syncBudgets($request->project_id);

            return back()->with('success', "Imported {$result['created']} budgets, updated {$result['updated']} budgets from Procore.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error importing budgets from Procore: ' . $e->getMessage());
        }
    }
}
