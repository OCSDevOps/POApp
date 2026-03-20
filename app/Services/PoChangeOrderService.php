<?php

namespace App\Services;

use App\Models\PoChangeOrder;
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
            
            if (!$po->porder_original_total) {
                $po->porder_original_total = $po->porder_total_amount;
            }
            
            $po->porder_total_amount = $poco->new_total;
            $po->porder_change_orders_total += $poco->poco_amount;
            $po->save();
            
            // Update budget commitment if needed
            if ($poco->poco_amount != 0) {
                $budgetService = app(BudgetService::class);
                $budgetService->updateBudgetCommitment(
                    $po->porder_project_ms,
                    $po->porder_cost_code,
                    $poco->poco_amount
                );
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
        $po = PurchaseOrder::findOrFail($poId);
        $newTotal = $po->porder_total_amount + $changeAmount;
        
        if ($changeAmount > 0) {
            // Increase - check budget
            $budgetService = app(BudgetService::class);
            $validation = $budgetService->validatePoBudget(
                $po->porder_project_ms,
                $po->porder_cost_code,
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
}
