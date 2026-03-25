<?php

namespace App\Services;

use App\Models\PoChangeOrder;
use App\Models\Budget;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PO Change Order Service
 * 
 * Handles:
 * - PO change order creation
 * - Approval and application to PO
 * - Budget impact tracking
 */
class PoChangeOrderService
{
    /**
     * Create a PO change order.
     */
    public function createPoChangeOrder(array $data): array
    {
        try {
            DB::beginTransaction();
            
            $po = PurchaseOrder::findOrFail($data['purchase_order_id']);
            
            $poco = PoChangeOrder::create([
                'company_id' => $po->company_id ?? session('company_id'),
                'purchase_order_id' => $data['purchase_order_id'],
                'poco_type' => $data['poco_type'] ?? 'amount_change',
                'poco_amount' => $data['poco_amount'],
                'previous_total' => $po->porder_total_amount,
                'new_total' => $po->porder_total_amount + $data['poco_amount'],
                'poco_description' => $data['poco_description'],
                'poco_notes' => $data['poco_notes'] ?? null,
                'poco_reference' => $data['poco_reference'] ?? null,
                'poco_details' => $data['poco_details'] ?? null,
                'poco_status' => 'draft',
                'created_by' => $data['created_by'],
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'poco' => $poco,
                'message' => 'PO change order created successfully',
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('PO CO creation failed', [
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
     * Approve a PO change order (called after approval workflow completes).
     */
    public function approvePoChangeOrder($pocoId, $userId): array
    {
        try {
            DB::beginTransaction();
            
            $poco = PoChangeOrder::with('purchaseOrder')->findOrFail($pocoId);
            
            if (!in_array($poco->poco_status, ['draft', 'pending_approval'])) {
                throw new \Exception('PO change order cannot be approved in current status');
            }
            
            // Update PCO status
            $poco->poco_status = 'approved';
            $poco->approved_by = $userId;
            $poco->approved_at = now();
            $poco->save();
            
            // Update the PO
            $po = $poco->purchaseOrder;
            
            if ((float) ($po->porder_original_total ?? 0) === 0.0) {
                $po->porder_original_total = $po->porder_total_amount;
            }
            
            $po->porder_total_amount = (float) $poco->new_total;
            $po->porder_change_orders_total = (float) ($po->porder_change_orders_total ?? 0) + (float) $poco->poco_amount;
            $po->save();
            
            // Update budget commitment if needed
            if ($poco->poco_amount != 0) {
                $costCodeId = $this->resolvePoCostCode($po);

                $budgetService = app(BudgetService::class);
                if ($costCodeId) {
                    $budgetService->updateBudgetCommitment(
                        $po->porder_project_ms,
                        $costCodeId,
                        $poco->poco_amount
                    );
                }
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'poco' => $poco,
                'po' => $po,
                'message' => 'PO change order approved and PO updated',
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('PO CO approval failed', [
                'poco_id' => $pocoId,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get change order history for a PO.
     */
    public function getPoChangeOrderHistory($poId): array
    {
        $pocos = PoChangeOrder::with(['creator', 'approver'])
            ->where('purchase_order_id', $poId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return [
            'change_orders' => $pocos,
            'total_changes' => $pocos->where('poco_status', 'approved')->sum('poco_amount'),
            'pending_count' => $pocos->whereIn('poco_status', ['draft', 'pending_approval'])->count(),
        ];
    }

    /**
     * Validate if PO change order can be created (budget check).
     */
    public function validatePoChangeOrder($poId, $changeAmount): array
    {
        $po = PurchaseOrder::find($poId);
        if (!$po) {
            return [
                'valid' => false,
                'reason' => 'Purchase order not found',
            ];
        }

        $newTotal = $po->porder_total_amount + $changeAmount;
        
        if ($changeAmount > 0) {
            $costCodeId = $this->resolvePoCostCode($po);
            if (!$costCodeId) {
                return [
                    'valid' => false,
                    'reason' => 'Unable to determine cost code for purchase order',
                ];
            }

            // Increase - check budget
            $budgetService = app(BudgetService::class);
            $validation = $budgetService->validatePoBudget(
                $po->porder_project_ms,
                $costCodeId,
                $changeAmount // Only validate the increase amount
            );
            
            if (!$validation['valid']) {
                return [
                    'valid' => false,
                    'reason' => 'Insufficient budget for PO increase',
                    'details' => $validation,
                ];
            }
        }
        
        return [
            'valid' => true,
            'current_total' => $po->porder_total_amount,
            'change_amount' => $changeAmount,
            'new_total' => $newTotal,
        ];
    }

    private function resolvePoCostCode(PurchaseOrder $po): ?int
    {
        if (!empty($po->porder_cost_code)) {
            return (int) $po->porder_cost_code;
        }

        $itemCostCode = $po->items()
            ->whereNotNull('po_detail_ccode')
            ->value('po_detail_ccode');

        if ($itemCostCode) {
            return (int) $itemCostCode;
        }

        $budgetCostCodes = Budget::where('budget_project_id', $po->porder_project_ms)
            ->pluck('budget_cost_code_id')
            ->filter()
            ->unique()
            ->values();

        return $budgetCostCodes->count() === 1 ? (int) $budgetCostCodes->first() : null;
    }
}
