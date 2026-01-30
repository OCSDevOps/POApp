<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ReceiveOrderItem;
use App\Models\SupplierUser;
use App\Notifications\BackorderNotification;
use Illuminate\Support\Facades\DB;

class BackorderService
{
    /**
     * Recalculate backorder fields for all items on a PO.
     */
    public function recalcForPo(PurchaseOrder $po): void
    {
        $poItems = PurchaseOrderItem::where('po_detail_porder_ms', $po->porder_id)
            ->where('po_detail_status', 1)
            ->get();

        $hasBackorder = false;

        foreach ($poItems as $poItem) {
            $receivedQty = ReceiveOrderItem::whereHas('receiveOrder', function ($q) use ($po) {
                    $q->where('rorder_porder_ms', $po->porder_id);
                })
                ->where('ro_detail_item', $poItem->po_detail_item)
                ->where('ro_detail_status', 1)
                ->sum('ro_detail_quantity');

            $pending = max($poItem->po_detail_quantity - $receivedQty, 0);
            $status = $pending > 0 ? 1 : 0;

            $poItem->backordered_qty = $pending;
            $poItem->backorder_status = $status;
            // expected_backorder_date remains manual entry for now
            $poItem->save();

            if ($pending > 0) {
                $hasBackorder = true;
            }
        }

        if ($hasBackorder) {
            $this->notify($po, $poItems);
        }
    }

    /**
     * List backorders with optional filters.
     */
    public function listBackorders(?int $projectId = null, ?int $supplierId = null, ?int $companyId = null)
    {
        $query = DB::table('purchase_order_details as pod')
            ->join('purchase_order_master as pom', 'pom.porder_id', '=', 'pod.po_detail_porder_ms')
            ->leftJoin('project_master as proj', 'proj.proj_id', '=', 'pom.porder_project_ms')
            ->leftJoin('supplier_master as sup', 'sup.sup_id', '=', 'pom.porder_supplier_ms')
            ->selectRaw('pod.po_detail_id, pod.po_detail_item, pod.po_detail_quantity, pod.backordered_qty, pod.expected_backorder_date, pod.backorder_status, pom.porder_no, pom.porder_id, pom.porder_project_ms, pom.porder_supplier_ms, proj.proj_name, sup.sup_name, pom.company_id')
            ->where('pod.backordered_qty', '>', 0);

        if ($projectId) {
            $query->where('pom.porder_project_ms', $projectId);
        }
        if ($supplierId) {
            $query->where('pom.porder_supplier_ms', $supplierId);
        }
        if ($companyId) {
            $query->where('pom.company_id', $companyId);
        }

        return $query->orderBy('pom.porder_no', 'desc')->get();
    }

    /**
     * Notify admins and supplier users about backorders.
     */
    protected function notify(PurchaseOrder $po, $poItems): void
    {
        $remaining = [];
        foreach ($poItems as $poItem) {
            if ($poItem->backordered_qty > 0) {
                $remaining[] = [
                    'item_code' => $poItem->po_detail_item,
                    'pending_qty' => $poItem->backordered_qty,
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

        // Notify supplier portal users for this supplier
        $supplierUsers = SupplierUser::where('supplier_id', $po->porder_supplier_ms)->get();
        foreach ($supplierUsers as $user) {
            $user->notify(new BackorderNotification($context));
        }
    }
}
