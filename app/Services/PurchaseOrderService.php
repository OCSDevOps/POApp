<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Item;
use App\Models\ItemPriceHistory;
use App\Models\PoTemplate;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ReceiveOrder;
use App\Models\ReceiveOrderItem;
use App\Models\Rfq;
use App\Models\RfqItem;
use App\Models\RfqQuote;
use App\Models\RfqSupplier;
use App\Models\Supplier;
use App\Models\SupplierCatalog;
use App\Models\User;
use App\Notifications\BackorderNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseOrderService
{
    // ==========================================
    // PURCHASE ORDER MANAGEMENT
    // ==========================================

    /**
     * Generate next PO number
     */
    public function generatePoNumber()
    {
        $lastPo = PurchaseOrder::orderBy('porder_id', 'DESC')->first();
        $nextNumber = $lastPo ? intval(substr($lastPo->porder_no, 2)) + 1 : 1;
        return 'PO' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new Purchase Order
     */
    public function createPurchaseOrder($data, $items = [])
    {
        DB::beginTransaction();
        
        try {
            // Check budget constraints if enabled
            if (config('app.budget_constraints_enabled', true)) {
                $this->validateBudgetConstraints($data['project_id'], $items);
            }

            // Create PO
            $po = PurchaseOrder::create([
                'porder_no' => $this->generatePoNumber(),
                'porder_project_ms' => $data['project_id'],
                'porder_supplier_ms' => $data['supplier_id'],
                'porder_address' => $data['address'] ?? '',
                'porder_delivery_note' => $data['delivery_note'] ?? null,
                'porder_description' => $data['description'] ?? null,
                'porder_total_item' => count($items),
                'porder_total_amount' => 0,
                'porder_total_tax' => 0,
                'porder_delivery_status' => 0,
                'porder_createdate' => now(),
                'porder_createby' => auth()->id() ?? 1,
                'porder_status' => 1,
                'integration_status' => 'pending',
            ]);

            // Add items
            $totalAmount = 0;
            $totalTax = 0;

            foreach ($items as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $taxAmount = $subtotal * (($item['tax_rate'] ?? 0) / 100);
                $total = $subtotal + $taxAmount;

                PurchaseOrderItem::create([
                    'po_detail_autogen' => date('dmyHis') . rand(100, 999),
                    'po_detail_porder_ms' => $po->porder_id,
                    'po_detail_item' => $item['item_code'],
                    'po_detail_sku' => $item['sku'] ?? '',
                    'po_detail_taxcode' => $item['tax_rate'] ?? '0',
                    'po_detail_quantity' => $item['quantity'],
                    'po_detail_unitprice' => $item['unit_price'],
                    'po_detail_subtotal' => $subtotal,
                    'po_detail_taxamount' => $taxAmount,
                    'po_detail_total' => $total,
                    'po_detail_createdate' => now(),
                    'po_detail_status' => 1,
                    'po_detail_tax_group' => $item['tax_group'] ?? null,
                ]);

                $totalAmount += $subtotal;
                $totalTax += $taxAmount;
            }

            // Update PO totals
            $po->update([
                'porder_total_amount' => $totalAmount + $totalTax,
                'porder_total_tax' => $totalTax,
            ]);

            // Commit budget if constraints enabled
            if (config('app.budget_constraints_enabled', true)) {
                $this->commitBudget($data['project_id'], $items, $totalAmount);
            }

            DB::commit();
            return $po;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validate budget constraints for PO items
     */
    protected function validateBudgetConstraints($projectId, $items)
    {
        $costCodeTotals = [];

        foreach ($items as $item) {
            $itemModel = Item::where('item_code', $item['item_code'])->first();
            if ($itemModel && $itemModel->item_ccode_ms) {
                $costCodeId = $itemModel->item_ccode_ms;
                $amount = $item['quantity'] * $item['unit_price'];
                
                if (!isset($costCodeTotals[$costCodeId])) {
                    $costCodeTotals[$costCodeId] = 0;
                }
                $costCodeTotals[$costCodeId] += $amount;
            }
        }

        foreach ($costCodeTotals as $costCodeId => $amount) {
            $budget = Budget::getBudgetFor($projectId, $costCodeId);
            
            if ($budget && !$budget->canCommit($amount)) {
                throw new \Exception(
                    "Insufficient budget for cost code {$costCodeId}. " .
                    "Available: {$budget->remaining_amount}, Required: {$amount}"
                );
            }
        }
    }

    /**
     * Commit budget for PO items
     */
    protected function commitBudget($projectId, $items, $totalAmount)
    {
        $costCodeTotals = [];

        foreach ($items as $item) {
            $itemModel = Item::where('item_code', $item['item_code'])->first();
            if ($itemModel && $itemModel->item_ccode_ms) {
                $costCodeId = $itemModel->item_ccode_ms;
                $amount = $item['quantity'] * $item['unit_price'];
                
                if (!isset($costCodeTotals[$costCodeId])) {
                    $costCodeTotals[$costCodeId] = 0;
                }
                $costCodeTotals[$costCodeId] += $amount;
            }
        }

        foreach ($costCodeTotals as $costCodeId => $amount) {
            $budget = Budget::getBudgetFor($projectId, $costCodeId);
            if ($budget) {
                $budget->commit($amount);
            }
        }
    }

    /**
     * Create PO from template
     */
    public function createFromTemplate($templateId, $projectId = null, $supplierId = null, $quantities = [])
    {
        $template = PoTemplate::findOrFail($templateId);
        return $template->createPurchaseOrder($projectId, $supplierId, $quantities);
    }

    // ==========================================
    // RFQ MANAGEMENT
    // ==========================================

    /**
     * Create a new RFQ
     */
    public function createRfq($data, $items = [], $supplierIds = [])
    {
        DB::beginTransaction();
        
        try {
            $rfq = Rfq::create([
                'rfq_no' => Rfq::generateRfqNumber(),
                'rfq_project_id' => $data['project_id'],
                'rfq_title' => $data['title'],
                'rfq_description' => $data['description'] ?? null,
                'rfq_due_date' => $data['due_date'],
                'rfq_status' => Rfq::STATUS_DRAFT,
                'rfq_created_by' => auth()->id() ?? 1,
                'rfq_created_at' => now(),
            ]);

            // Add items
            foreach ($items as $item) {
                RfqItem::create([
                    'rfqi_rfq_id' => $rfq->rfq_id,
                    'rfqi_item_id' => $item['item_id'],
                    'rfqi_quantity' => $item['quantity'],
                    'rfqi_uom_id' => $item['uom_id'],
                    'rfqi_target_price' => $item['target_price'] ?? null,
                    'rfqi_notes' => $item['notes'] ?? null,
                    'rfqi_created_at' => now(),
                ]);
            }

            // Add suppliers
            foreach ($supplierIds as $supplierId) {
                RfqSupplier::create([
                    'rfqs_rfq_id' => $rfq->rfq_id,
                    'rfqs_supplier_id' => $supplierId,
                    'rfqs_status' => RfqSupplier::STATUS_PENDING,
                    'rfqs_created_at' => now(),
                ]);
            }

            DB::commit();
            return $rfq;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Send RFQ to suppliers
     */
    public function sendRfq($rfqId)
    {
        $rfq = Rfq::findOrFail($rfqId);
        return $rfq->send();
    }

    /**
     * Record supplier quote for RFQ
     */
    public function recordQuote($rfqSupplierId, $quotes)
    {
        DB::beginTransaction();
        
        try {
            $rfqSupplier = RfqSupplier::findOrFail($rfqSupplierId);

            foreach ($quotes as $quote) {
                RfqQuote::create([
                    'rfqq_rfqs_id' => $rfqSupplierId,
                    'rfqq_rfqi_id' => $quote['rfq_item_id'],
                    'rfqq_quoted_price' => $quote['price'],
                    'rfqq_lead_time_days' => $quote['lead_time_days'] ?? null,
                    'rfqq_valid_until' => $quote['valid_until'] ?? null,
                    'rfqq_notes' => $quote['notes'] ?? null,
                    'rfqq_created_at' => now(),
                ]);
            }

            $rfqSupplier->markAsResponded();

            DB::commit();
            return $rfqSupplier;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Convert RFQ to Purchase Order
     */
    public function convertRfqToPo($rfqId, $supplierId)
    {
        DB::beginTransaction();
        
        try {
            $rfq = Rfq::with(['items', 'suppliers.quotes'])->findOrFail($rfqId);
            
            // Get selected supplier
            $rfqSupplier = $rfq->suppliers()
                ->where('rfqs_supplier_id', $supplierId)
                ->first();

            if (!$rfqSupplier) {
                throw new \Exception('Supplier not found in RFQ');
            }

            // Mark supplier as selected
            $rfqSupplier->select();

            // Get project
            $project = Project::find($rfq->rfq_project_id);

            // Build items array from quotes
            $items = [];
            foreach ($rfq->items as $rfqItem) {
                $quote = $rfqSupplier->quotes()
                    ->where('rfqq_rfqi_id', $rfqItem->rfqi_id)
                    ->first();

                if ($quote) {
                    $item = Item::find($rfqItem->rfqi_item_id);
                    
                    // Get supplier catalog for SKU
                    $catalog = SupplierCatalog::where('supcat_supplier', $supplierId)
                        ->where('supcat_item_code', $item->item_code)
                        ->first();

                    $items[] = [
                        'item_code' => $item->item_code,
                        'sku' => $catalog ? $catalog->supcat_sku_no : '',
                        'quantity' => $rfqItem->rfqi_quantity,
                        'unit_price' => $quote->rfqq_quoted_price,
                        'tax_rate' => 0,
                    ];

                    // Update supplier catalog with new price if different
                    if ($catalog && $catalog->supcat_price != $quote->rfqq_quoted_price) {
                        $this->updateItemPrice(
                            $item->item_id,
                            $supplierId,
                            $catalog->supcat_price,
                            $quote->rfqq_quoted_price
                        );
                    }
                }
            }

            // Create PO
            $po = $this->createPurchaseOrder([
                'project_id' => $rfq->rfq_project_id,
                'supplier_id' => $supplierId,
                'address' => $project->proj_address ?? '',
                'description' => "Created from RFQ: {$rfq->rfq_no}",
            ], $items);

            // Update RFQ status
            $rfq->rfq_status = Rfq::STATUS_CONVERTED;
            $rfq->rfq_modified_at = now();
            $rfq->save();

            DB::commit();
            return $po;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // ==========================================
    // RECEIVING ORDERS
    // ==========================================

    /**
     * Create a receive order
     */
    public function createReceiveOrder($poId, $slipNo, $items, $date = null)
    {
        DB::beginTransaction();
        
        try {
            $po = PurchaseOrder::findOrFail($poId);

            $receiveOrder = ReceiveOrder::create([
                'rorder_porder_ms' => $poId,
                'rorder_slip_no' => $slipNo,
                'rorder_date' => $date ?? now()->toDateString(),
                'rorder_totalitem' => count($items),
                'rorder_createdate' => now(),
                'rorder_createby' => auth()->id() ?? 1,
                'rorder_status' => 1,
            ]);

            $totalAmount = 0;

            foreach ($items as $item) {
                ReceiveOrderItem::create([
                    'ro_detail_rorder_ms' => $receiveOrder->rorder_id,
                    'ro_detail_item' => $item['item_code'],
                    'ro_detail_quantity' => $item['quantity'],
                    'ro_detail_createdate' => now(),
                    'ro_detail_status' => 1,
                ]);

                // Get unit price from PO
                $poItem = PurchaseOrderItem::where('po_detail_porder_ms', $poId)
                    ->where('po_detail_item', $item['item_code'])
                    ->first();

                if ($poItem) {
                    $totalAmount += $item['quantity'] * $poItem->po_detail_unitprice;
                }
            }

            $receiveOrder->update(['rorder_totalamount' => $totalAmount]);

            // Update PO delivery status
            $this->updatePoDeliveryStatus($po);

            DB::commit();
            return $receiveOrder;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update PO delivery status based on received quantities
     */
    protected function updatePoDeliveryStatus($po)
    {
        $poItems = PurchaseOrderItem::where('po_detail_porder_ms', $po->porder_id)
            ->where('po_detail_status', 1)
            ->get();

        $fullyReceived = true;
        $partiallyReceived = false;

        foreach ($poItems as $poItem) {
            $receivedQty = ReceiveOrderItem::whereHas('receiveOrder', function ($q) use ($po) {
                $q->where('rorder_porder_ms', $po->porder_id);
            })
            ->where('ro_detail_item', $poItem->po_detail_item)
            ->where('ro_detail_status', 1)
            ->sum('ro_detail_quantity');

            if ($receivedQty < $poItem->po_detail_quantity) {
                $fullyReceived = false;
            }
            if ($receivedQty > 0) {
                $partiallyReceived = true;
            }
        }

        if ($fullyReceived) {
            $po->porder_delivery_status = 1; // Fully received
        } elseif ($partiallyReceived) {
            $po->porder_delivery_status = 2; // Partially received
            $this->notifyBackorder($po, $poItems);
        } else {
            $po->porder_delivery_status = 0; // Not received
        }

        $po->save();
    }

    /**
     * Notify admins about backorder items for a PO.
     */
    protected function notifyBackorder($po, $poItems)
    {
        $remaining = [];

        foreach ($poItems as $poItem) {
            $receivedQty = ReceiveOrderItem::whereHas('receiveOrder', function ($q) use ($po) {
                $q->where('rorder_porder_ms', $po->porder_id);
            })
            ->where('ro_detail_item', $poItem->po_detail_item)
            ->where('ro_detail_status', 1)
            ->sum('ro_detail_quantity');

            $pending = max($poItem->po_detail_quantity - $receivedQty, 0);
            if ($pending > 0) {
                $remaining[] = [
                    'item_code' => $poItem->po_detail_item,
                    'pending_qty' => $pending,
                ];
            }
        }

        if (empty($remaining)) {
            return;
        }

        $context = [
            'po_no' => $po->porder_no,
            'project' => optional($po->project)->proj_name ?? '',
            'supplier' => optional($po->supplier)->sup_name ?? '',
            'remaining_items' => $remaining,
        ];

        $admins = User::where('u_type', 1)->get();
        foreach ($admins as $admin) {
            $admin->notify(new BackorderNotification($context));
        }
    }

    // ==========================================
    // BACK ORDER REPORTING
    // ==========================================

    /**
     * Get back order report
     */
    public function getBackOrderReport($projectId = null, $supplierId = null)
    {
        $query = DB::table('vw_back_order_report');

        if ($projectId) {
            $query->where('porder_project_ms', $projectId);
        }

        if ($supplierId) {
            $query->where('porder_supplier_ms', $supplierId);
        }

        return $query->get();
    }

    /**
     * Get back order summary by supplier
     */
    public function getBackOrderSummaryBySupplier()
    {
        return DB::table('vw_back_order_report')
            ->selectRaw('sup_name, COUNT(*) as item_count, SUM(back_order_qty) as total_back_order_qty')
            ->groupBy('sup_name')
            ->orderBy('total_back_order_qty', 'DESC')
            ->get();
    }

    // ==========================================
    // PRICE TRACKING
    // ==========================================

    /**
     * Update item price and record history
     */
    public function updateItemPrice($itemId, $supplierId, $oldPrice, $newPrice, $effectiveDate = null, $notes = null)
    {
        // Record price history
        ItemPriceHistory::recordPriceChange(
            $itemId,
            $supplierId,
            $oldPrice,
            $newPrice,
            $effectiveDate ?? now()->toDateString(),
            $notes
        );

        // Update supplier catalog
        $item = Item::find($itemId);
        if ($item) {
            SupplierCatalog::where('supcat_supplier', $supplierId)
                ->where('supcat_item_code', $item->item_code)
                ->update([
                    'supcat_price' => $newPrice,
                    'supcat_lastdate' => $effectiveDate ?? now()->toDateString(),
                    'supcat_modifydate' => now(),
                    'supcat_modifyby' => auth()->id(),
                ]);
        }
    }

    /**
     * Get price history for an item
     */
    public function getItemPriceHistory($itemId, $supplierId = null)
    {
        $query = ItemPriceHistory::where('iph_item_id', $itemId)
            ->with(['supplier', 'item'])
            ->orderBy('iph_effective_date', 'DESC');

        if ($supplierId) {
            $query->where('iph_supplier_id', $supplierId);
        }

        return $query->get();
    }

    /**
     * Get price comparison across suppliers for an item
     */
    public function getPriceComparison($itemCode)
    {
        return SupplierCatalog::where('supcat_item_code', $itemCode)
            ->where('supcat_status', 1)
            ->with('supplier')
            ->orderBy('supcat_price', 'ASC')
            ->get();
    }

    // ==========================================
    // SUPPLIER PORTAL
    // ==========================================

    /**
     * Get supplier dashboard data
     */
    public function getSupplierDashboard($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);

        return [
            'supplier' => $supplier,
            'total_orders' => PurchaseOrder::where('porder_supplier_ms', $supplierId)->count(),
            'pending_orders' => PurchaseOrder::where('porder_supplier_ms', $supplierId)
                ->where('porder_delivery_status', 0)
                ->count(),
            'total_order_value' => PurchaseOrder::where('porder_supplier_ms', $supplierId)
                ->where('porder_status', 1)
                ->sum('porder_total_amount'),
            'catalog_items' => SupplierCatalog::where('supcat_supplier', $supplierId)
                ->where('supcat_status', 1)
                ->count(),
            'pending_rfqs' => RfqSupplier::where('rfqs_supplier_id', $supplierId)
                ->whereIn('rfqs_status', [RfqSupplier::STATUS_PENDING, RfqSupplier::STATUS_SENT])
                ->count(),
            'recent_orders' => PurchaseOrder::where('porder_supplier_ms', $supplierId)
                ->with('project')
                ->orderBy('porder_createdate', 'DESC')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Get supplier catalog
     */
    public function getSupplierCatalog($supplierId)
    {
        return SupplierCatalog::where('supcat_supplier', $supplierId)
            ->where('supcat_status', 1)
            ->with(['item', 'unitOfMeasure'])
            ->orderBy('supcat_item_code')
            ->get();
    }

    /**
     * Add item to supplier catalog
     */
    public function addToCatalog($supplierId, $data)
    {
        return SupplierCatalog::create([
            'supcat_supplier' => $supplierId,
            'supcat_item_code' => $data['item_code'],
            'supcat_sku_no' => $data['sku_no'],
            'supcat_uom' => $data['uom_id'],
            'supcat_price' => $data['price'],
            'supcat_lastdate' => $data['effective_date'] ?? now()->toDateString(),
            'supcat_details' => $data['details'] ?? null,
            'supcat_createdate' => now(),
            'supcat_createby' => auth()->id() ?? 1,
            'supcat_status' => 1,
        ]);
    }
}
