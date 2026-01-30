<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\CostCode;
use App\Models\Budget;
use App\Models\ProjectCostCode;
use App\Services\BudgetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectBudgetController extends Controller
{
    protected $budgetService;

    public function __construct(BudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    /**
     * Show project cost code assignment page.
     */
    public function assignCostCodes($projectId)
    {
        $project = Project::findOrFail($projectId);
        
        // Get all active cost codes grouped by hierarchy
        $parentCodes = CostCode::parents()->active()->orderBy('cc_parent_code')->get();
        
        // Get currently assigned cost codes
        $assignedCostCodeIds = ProjectCostCode::byProject($projectId)
            ->active()
            ->pluck('cost_code_id')
            ->toArray();
        
        return view('admin.budgets.assign-cost-codes', compact(
            'project',
            'parentCodes',
            'assignedCostCodeIds'
        ));
    }

    /**
     * Save cost code assignments.
     */
    public function saveCostCodeAssignments(Request $request, $projectId)
    {
        $request->validate([
            'cost_code_ids' => 'required|array',
            'cost_code_ids.*' => 'exists:cost_code_master,cc_id',
        ]);

        try {
            $result = $this->budgetService->assignCostCodesToProject(
                $projectId,
                $request->cost_code_ids
            );

            if ($result['success']) {
                return redirect()
                    ->route('admin.budgets.setup', $projectId)
                    ->with('success', 'Cost codes assigned successfully');
            }

            return back()->with('error', $result['message']);
        } catch (\Exception $e) {
            Log::error('Cost code assignment failed', [
                'project_id' => $projectId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to assign cost codes');
        }
    }

    /**
     * Show budget setup page for project.
     */
    public function setupBudgets($projectId)
    {
        $project = Project::with('budgets.costCode')->findOrFail($projectId);
        
        // Get assigned cost codes with hierarchy
        $assignedCostCodes = ProjectCostCode::with('costCode')
            ->byProject($projectId)
            ->active()
            ->get()
            ->pluck('costCode')
            ->sortBy(function($costCode) {
                return $costCode->cc_full_code ?? $costCode->cc_no;
            });
        
        // Get existing budgets for this project
        $existingBudgets = Budget::where('budget_project_ms', $projectId)
            ->with('costCode')
            ->get()
            ->keyBy('budget_cc_ms');
        
        return view('admin.budgets.setup', compact(
            'project',
            'assignedCostCodes',
            'existingBudgets'
        ));
    }

    /**
     * Save budget setup.
     */
    public function saveBudgets(Request $request, $projectId)
    {
        $request->validate([
            'budgets' => 'required|array',
            'budgets.*.cost_code_id' => 'required|exists:cost_code_master,cc_id',
            'budgets.*.amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            
            $successCount = 0;
            $bcoCount = 0;
            
            foreach ($request->budgets as $budgetData) {
                $result = $this->budgetService->setupBudget(
                    $projectId,
                    $budgetData['cost_code_id'],
                    $budgetData['amount'],
                    session('user_id')
                );
                
                if ($result['success']) {
                    $successCount++;
                    if ($result['change_order_created']) {
                        $bcoCount++;
                    }
                }
            }
            
            DB::commit();
            
            $message = "Successfully set up {$successCount} budget(s)";
            if ($bcoCount > 0) {
                $message .= " ({$bcoCount} budget change order(s) created for existing budgets)";
            }
            
            return redirect()
                ->route('admin.budgets.view', $projectId)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Budget setup failed', [
                'project_id' => $projectId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to save budgets');
        }
    }

    /**
     * View budget summary for project.
     */
    public function viewBudgetSummary($projectId)
    {
        $project = Project::findOrFail($projectId);
        
        // Get budget summary with rollup
        $summary = $this->budgetService->getProjectBudgetSummary($projectId);
        
        // Get parent codes for grouping
        $parentCodes = CostCode::parents()->active()->get();
        
        // Group summary by parent code
        $groupedSummary = collect($summary['budgets'])->groupBy(function($item) {
            $costCode = CostCode::find($item->cost_code_id);
            return $costCode ? $costCode->cc_parent_code : 'Other';
        });
        
        return view('admin.budgets.view', compact(
            'project',
            'summary',
            'groupedSummary',
            'parentCodes'
        ));
    }

    /**
     * Get budget details for a specific cost code.
     */
    public function getBudgetDetails($projectId, $costCodeId)
    {
        $budget = Budget::where('budget_project_ms', $projectId)
            ->where('budget_cc_ms', $costCodeId)
            ->with(['costCode', 'project'])
            ->first();
        
        if (!$budget) {
            return response()->json(['error' => 'Budget not found'], 404);
        }
        
        // Get change order history
        $changeOrders = $this->budgetService->getBudgetChangeOrderHistory(
            $projectId,
            $costCodeId
        );
        
        return response()->json([
            'budget' => $budget,
            'change_orders' => $changeOrders,
            'available' => $budget->budget_amount - $budget->budget_committed,
            'utilization' => $budget->budget_amount > 0 
                ? ($budget->budget_committed / $budget->budget_amount) * 100 
                : 0,
        ]);
    }

    /**
     * Check budget availability before PO creation.
     */
    public function checkBudgetAvailability(Request $request, $projectId)
    {
        $request->validate([
            'cost_code_id' => 'required|exists:cost_code_master,cc_id',
            'amount' => 'required|numeric|min:0',
        ]);

        $validation = $this->budgetService->validatePoBudget(
            $projectId,
            $request->cost_code_id,
            $request->amount
        );

        // Calculate warning thresholds
        $budget = Budget::where('budget_project_ms', $projectId)
            ->where('budget_cc_ms', $request->cost_code_id)
            ->first();

        $response = [
            'valid' => $validation['valid'],
            'message' => $validation['message'],
        ];

        if ($budget) {
            $available = $budget->budget_amount - $budget->budget_committed;
            $afterCommitment = $available - $request->amount;
            $utilizationAfter = ($budget->budget_committed + $request->amount) / $budget->budget_amount * 100;
            
            $response['budget_details'] = [
                'total_budget' => $budget->budget_amount,
                'committed' => $budget->budget_committed,
                'available' => $available,
                'after_commitment' => $afterCommitment,
                'utilization_after' => $utilizationAfter,
                'warning_threshold' => $budget->budget_warning_threshold ?? 75,
                'critical_threshold' => $budget->budget_critical_threshold ?? 90,
            ];

            // Add warning flags
            if ($utilizationAfter >= ($budget->budget_critical_threshold ?? 90)) {
                $response['severity'] = 'critical';
                $response['warning'] = 'This PO will exceed critical budget threshold (' . ($budget->budget_critical_threshold ?? 90) . '%)';
            } elseif ($utilizationAfter >= ($budget->budget_warning_threshold ?? 75)) {
                $response['severity'] = 'warning';
                $response['warning'] = 'This PO will exceed warning budget threshold (' . ($budget->budget_warning_threshold ?? 75) . '%)';
            }
        }

        return response()->json($response);
    }
}
