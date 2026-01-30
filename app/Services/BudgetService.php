<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\BudgetChangeOrder;
use App\Models\PurchaseOrder;
use App\Models\Project;
use App\Models\CostCode;
use App\Models\ProjectCostCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Budget Management Service
 * 
 * Handles:
 * - Budget setup per project per cost code
 * - Budget change order processing
 * - Budget validation for PO creation
 * - Job cost tracking (committed vs actual)
 * - Budget vs actual reporting
 */
class BudgetService
{
    /**
     * Assign cost codes to a project.
     */
    public function assignCostCodesToProject($projectId, array $costCodeIds): array
    {
        try {
            DB::beginTransaction();
            
            $project = Project::findOrFail($projectId);
            
            // Remove existing assignments not in new list
            ProjectCostCode::where('project_id', $projectId)
                ->whereNotIn('cost_code_id', $costCodeIds)
                ->delete();
            
            $assigned = [];
            foreach ($costCodeIds as $costCodeId) {
                $assignment = ProjectCostCode::firstOrCreate([
                    'project_id' => $projectId,
                    'cost_code_id' => $costCodeId,
                ], [
                    'company_id' => $project->company_id ?? session('company_id'),
                    'is_active' => true,
                ]);
                
                $assigned[] = $assignment;
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'assigned' => $assigned,
                'message' => count($assigned) . ' cost codes assigned to project',
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Cost code assignment failed', [
                'project_id' => $projectId,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create or update budget for project cost code.
     */
    public function setupBudget($projectId, $costCodeId, $amount, $userId): array
    {
        try {
            DB::beginTransaction();
            
            // Check if project-cost code assignment exists
            $assignment = ProjectCostCode::where('project_id', $projectId)
                ->where('cost_code_id', $costCodeId)
                ->where('is_active', true)
                ->first();
            
            if (!$assignment) {
                throw new \Exception('Cost code not assigned to this project');
            }
            
            // Find or create budget
            $budget = Budget::firstOrNew([
                'budget_project_ms' => $projectId,
                'budget_cc_ms' => $costCodeId,
            ]);
            
            if (!$budget->exists) {
                // New budget
                $budget->budget_amount = $amount;
                $budget->budget_original_amount = $amount;
                $budget->budget_change_orders_total = 0;
                $budget->budget_committed = 0;
                $budget->budget_actual = 0;
                $budget->_created_by = $userId;
            } else {
                // Existing budget - update via change order
                return $this->createBudgetChangeOrder([
                    'budget_id' => $budget->budget_id,
                    'project_id' => $projectId,
                    'cost_code_id' => $costCodeId,
                    'bco_type' => $amount > $budget->budget_amount ? 'increase' : 'decrease',
                    'bco_amount' => $amount - $budget->budget_amount,
                    'bco_reason' => 'Budget adjustment',
                    'created_by' => $userId,
                ]);
            }
            
            $budget->save();
            
            DB::commit();
            
            return [
                'success' => true,
                'budget' => $budget,
                'message' => 'Budget created successfully',
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Budget setup failed', [
                'project_id' => $projectId,
                'cost_code_id' => $costCodeId,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a budget change order.
     */
    public function createBudgetChangeOrder(array $data): array
    {
        try {
            DB::beginTransaction();
            
            $budget = Budget::findOrFail($data['budget_id']);
            
            $bco = BudgetChangeOrder::create([
                'company_id' => $budget->company_id ?? session('company_id'),
                'budget_id' => $data['budget_id'],
                'project_id' => $data['project_id'],
                'cost_code_id' => $data['cost_code_id'],
                'bco_type' => $data['bco_type'],
                'bco_amount' => $data['bco_amount'],
                'previous_budget' => $budget->budget_amount,
                'new_budget' => $budget->budget_amount + $data['bco_amount'],
                'bco_reason' => $data['bco_reason'] ?? null,
                'bco_notes' => $data['bco_notes'] ?? null,
                'bco_reference' => $data['bco_reference'] ?? null,
                'bco_status' => 'draft',
                'created_by' => $data['created_by'],
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'bco' => $bco,
                'message' => 'Budget change order created successfully',
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Budget CO creation failed', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Approve a budget change order (called after approval workflow completes).
     */
    public function approveBudgetChangeOrder($bcoId, $userId): array
    {
        try {
            DB::beginTransaction();
            
            $bco = BudgetChangeOrder::findOrFail($bcoId);
            
            if (!in_array($bco->bco_status, ['draft', 'pending_approval'])) {
                throw new \Exception('Budget change order cannot be approved in current status');
            }
            
            // Update BCO status
            $bco->bco_status = 'approved';
            $bco->approved_by = $userId;
            $bco->approved_at = now();
            $bco->save();
            
            // Update the budget
            $budget = $bco->budget;
            $budget->budget_amount = $bco->new_budget;
            $budget->budget_change_orders_total += $bco->bco_amount;
            $budget->save();
            
            DB::commit();
            
            return [
                'success' => true,
                'bco' => $bco,
                'budget' => $budget,
                'message' => 'Budget change order approved and budget updated',
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Budget CO approval failed', [
                'bco_id' => $bcoId,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate if PO can be created against budget.
     */
    public function validatePoBudget($projectId, $costCodeId, $poAmount): array
    {
        $budget = Budget::where('budget_project_ms', $projectId)
            ->where('budget_cc_ms', $costCodeId)
            ->first();
        
        if (!$budget) {
            return [
                'valid' => false,
                'reason' => 'No budget found for this project and cost code',
            ];
        }
        
        $availableBudget = $budget->budget_amount - $budget->budget_committed;
        
        if ($poAmount > $availableBudget) {
            return [
                'valid' => false,
                'reason' => 'PO amount exceeds available budget',
                'budget_amount' => $budget->budget_amount,
                'committed' => $budget->budget_committed,
                'available' => $availableBudget,
                'shortfall' => $poAmount - $availableBudget,
            ];
        }
        
        return [
            'valid' => true,
            'budget_amount' => $budget->budget_amount,
            'committed' => $budget->budget_committed,
            'available' => $availableBudget,
            'remaining_after_po' => $availableBudget - $poAmount,
        ];
    }

    /**
     * Update budget commitment when PO is created/updated.
     */
    public function updateBudgetCommitment($projectId, $costCodeId, $poAmount): void
    {
        $budget = Budget::where('budget_project_ms', $projectId)
            ->where('budget_cc_ms', $costCodeId)
            ->first();
        
        if ($budget) {
            $budget->budget_committed += $poAmount;
            $budget->save();
        }
    }

    /**
     * Update job cost actual when goods are received.
     */
    public function updateJobCostActual($projectId, $costCodeId, $actualAmount): void
    {
        $budget = Budget::where('budget_project_ms', $projectId)
            ->where('budget_cc_ms', $costCodeId)
            ->first();
        
        if ($budget) {
            $budget->budget_actual += $actualAmount;
            $budget->save();
        }
    }

    /**
     * Get budget summary for a project.
     */
    public function getProjectBudgetSummary($projectId): array
    {
        $budgets = Budget::with(['project', 'costCode'])
            ->where('budget_project_ms', $projectId)
            ->get();
        
        $summary = [
            'total_budget' => $budgets->sum('budget_amount'),
            'total_original' => $budgets->sum('budget_original_amount'),
            'total_change_orders' => $budgets->sum('budget_change_orders_total'),
            'total_committed' => $budgets->sum('budget_committed'),
            'total_actual' => $budgets->sum('budget_actual'),
            'budgets_by_cost_code' => [],
        ];
        
        foreach ($budgets as $budget) {
            $summary['budgets_by_cost_code'][] = [
                'cost_code' => $budget->costCode->cc_code ?? 'N/A',
                'cost_code_name' => $budget->costCode->cc_description ?? 'N/A',
                'original_budget' => $budget->budget_original_amount,
                'change_orders' => $budget->budget_change_orders_total,
                'current_budget' => $budget->budget_amount,
                'committed' => $budget->budget_committed,
                'actual' => $budget->budget_actual,
                'available' => $budget->budget_amount - $budget->budget_committed,
                'variance' => $budget->budget_amount - $budget->budget_actual,
            ];
        }
        
        $summary['total_available'] = $summary['total_budget'] - $summary['total_committed'];
        $summary['total_variance'] = $summary['total_budget'] - $summary['total_actual'];
        
        return $summary;
    }

    /**
     * Get budget change order history for a project.
     */
    public function getBudgetChangeOrderHistory($projectId): array
    {
        $bcos = BudgetChangeOrder::with(['costCode', 'creator', 'approver'])
            ->where('project_id', $projectId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return [
            'change_orders' => $bcos,
            'total_increase' => $bcos->where('bco_type', 'increase')->where('bco_status', 'approved')->sum('bco_amount'),
            'total_decrease' => $bcos->where('bco_type', 'decrease')->where('bco_status', 'approved')->sum('bco_amount'),
            'pending_count' => $bcos->whereIn('bco_status', ['draft', 'pending_approval'])->count(),
        ];
    }
}
