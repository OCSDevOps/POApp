<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ReceiveOrder;
use App\Models\ReceiveOrderItem;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiveOrderController extends Controller
{
    protected $poService;

    public function __construct(PurchaseOrderService $poService)
    {
        $this->poService = $poService;
    }

    /**
     * Display a listing of receive orders.
     */
    public function index(Request $request)
    {
        $query = ReceiveOrder::with(['purchaseOrder.project', 'purchaseOrder.supplier']);

        // Filters
        if ($request->filled('po_id')) {
            $query->where('rorder_porder_ms', $request->po_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('rorder_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('rorder_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('rorder_slip_no', 'like', "%{$search}%");
        }

        $receiveOrders = $query->orderBy('rorder_createdate', 'DESC')->paginate(15);

        // Get receiving summary
        $summary = DB::table('vw_receiving_summary')
            ->when($request->filled('po_id'), function ($q) use ($request) {
                return $q->where('porder_id', $request->po_id);
            })
            ->get();

        return view('admin.receive.index', compact('receiveOrders', 'summary'));
    }

    /**
     * Show the form for creating a new receive order.
     */
    public function create(Request $request)
    {
        $poId = $request->get('po_id');
        
        if ($poId) {
            $purchaseOrder = PurchaseOrder::with(['items', 'project', 'supplier'])->findOrFail($poId);
            
            // Get already received quantities
            $receivedQtys = [];
            foreach ($purchaseOrder->items as $item) {
                $receivedQtys[$item->po_detail_item] = ReceiveOrderItem::whereHas('receiveOrder', function ($q) use ($poId) {
                    $q->where('rorder_porder_ms', $poId);
                })
                ->where('ro_detail_item', $item->po_detail_item)
                ->where('ro_detail_status', 1)
                ->sum('ro_detail_quantity');
            }

            return view('admin.receive.create', compact('purchaseOrder', 'receivedQtys'));
        }

        // Show PO selection
        $purchaseOrders = PurchaseOrder::with(['project', 'supplier'])
            ->where('porder_delivery_status', '!=', 1) // Not fully received
            ->where('porder_status', 1)
            ->orderBy('porder_createdate', 'DESC')
            ->get();

        return view('admin.receive.select_po', compact('purchaseOrders'));
    }

    /**
     * Store a newly created receive order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'po_id' => 'required|exists:porder_master,porder_id',
            'slip_no' => 'required|string|max:100',
            'receive_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_code' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $receiveOrder = $this->poService->createReceiveOrder(
                $request->po_id,
                $request->slip_no,
                $request->items,
                $request->receive_date
            );

            return redirect()->route('admin.receive.show', $receiveOrder->rorder_id)
                ->with('success', 'Receive order created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating receive order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified receive order.
     */
    public function show($id)
    {
        $receiveOrder = ReceiveOrder::with([
            'purchaseOrder.project',
            'purchaseOrder.supplier',
            'items.item',
        ])->findOrFail($id);

        return view('admin.receive.show', compact('receiveOrder'));
    }

    /**
     * Show the form for editing the specified receive order.
     */
    public function edit($id)
    {
        $receiveOrder = ReceiveOrder::with([
            'purchaseOrder.items',
            'items',
        ])->findOrFail($id);

        return view('admin.receive.edit', compact('receiveOrder'));
    }

    /**
     * Update the specified receive order.
     */
    public function update(Request $request, $id)
    {
        $receiveOrder = ReceiveOrder::findOrFail($id);

        $request->validate([
            'slip_no' => 'required|string|max:100',
            'receive_date' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $receiveOrder->update([
                'rorder_slip_no' => $request->slip_no,
                'rorder_date' => $request->receive_date,
                'rorder_modifydate' => now(),
                'rorder_modifyby' => auth()->id(),
            ]);

            // Update items if provided
            if ($request->has('items')) {
                foreach ($request->items as $itemCode => $data) {
                    ReceiveOrderItem::where('ro_detail_rorder_ms', $receiveOrder->rorder_id)
                        ->where('ro_detail_item', $itemCode)
                        ->update([
                            'ro_detail_quantity' => $data['quantity'],
                            'ro_detail_modifydate' => now(),
                            'ro_detail_modifyby' => auth()->id(),
                        ]);
                }
            }

            // Recalculate total
            $totalAmount = 0;
            $poItems = PurchaseOrderItem::where('po_detail_porder_ms', $receiveOrder->rorder_porder_ms)->get();
            
            foreach ($receiveOrder->items as $item) {
                $poItem = $poItems->where('po_detail_item', $item->ro_detail_item)->first();
                if ($poItem) {
                    $totalAmount += $item->ro_detail_quantity * $poItem->po_detail_unitprice;
                }
            }

            $receiveOrder->update(['rorder_totalamount' => $totalAmount]);

            // Update PO delivery status
            $po = PurchaseOrder::find($receiveOrder->rorder_porder_ms);
            $this->updatePoDeliveryStatus($po);

            DB::commit();

            return redirect()->route('admin.receive.show', $receiveOrder->rorder_id)
                ->with('success', 'Receive order updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating receive order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified receive order.
     */
    public function destroy($id)
    {
        $receiveOrder = ReceiveOrder::findOrFail($id);
        $poId = $receiveOrder->rorder_porder_ms;

        DB::beginTransaction();

        try {
            // Delete items
            ReceiveOrderItem::where('ro_detail_rorder_ms', $id)->delete();
            
            // Delete receive order
            $receiveOrder->delete();

            // Update PO delivery status
            $po = PurchaseOrder::find($poId);
            if ($po) {
                $this->updatePoDeliveryStatus($po);
            }

            DB::commit();

            return redirect()->route('admin.receive.index')
                ->with('success', 'Receive order deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting receive order: ' . $e->getMessage());
        }
    }

    /**
     * Update PO delivery status based on received quantities.
     */
    protected function updatePoDeliveryStatus($po)
    {
        // Defer to the shared service so partial receipts also trigger backorder notifications.
        return $this->poService->updatePoDeliveryStatus($po);
    }

    /**
     * Back order report.
     */
    public function backOrderReport(Request $request)
    {
        $query = DB::table('vw_back_order_report');

        if ($request->filled('project_id')) {
            $query->where('porder_project_ms', $request->project_id);
        }

        if ($request->filled('supplier_id')) {
            $query->where('porder_supplier_ms', $request->supplier_id);
        }

        $backOrders = $query->get();

        // Summary by supplier
        $supplierSummary = $this->poService->getBackOrderSummaryBySupplier();

        return view('admin.receive.back_order_report', compact('backOrders', 'supplierSummary'));
    }

    /**
     * Receiving summary report.
     */
    public function receivingSummary(Request $request)
    {
        $query = DB::table('vw_receiving_summary');

        if ($request->filled('project_id')) {
            $query->where('proj_id', $request->project_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('rorder_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('rorder_date', '<=', $request->date_to);
        }

        $summary = $query->get();

        return view('admin.receive.summary', compact('summary'));
    }
}
