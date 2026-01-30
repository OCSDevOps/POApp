<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Rfq;
use App\Models\RfqQuote;
use App\Models\RfqSupplier;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RfqController extends Controller
{
    private PurchaseOrderService $poService;

    public function __construct(PurchaseOrderService $poService)
    {
        $this->poService = $poService;
    }

    /**
     * List RFQs assigned to the supplier.
     */
    public function index()
    {
        $supplierId = Auth::guard('supplier')->user()->supplier_id;

        $rfqs = RfqSupplier::with(['rfq.project'])
            ->where('rfqs_supplier_id', $supplierId)
            ->orderBy('rfqs_created_at', 'desc')
            ->paginate(15);

        return view('supplier.rfq.index', compact('rfqs'));
    }

    /**
     * Show RFQ with items for quote submission.
     */
    public function show($rfqId)
    {
        $supplierId = Auth::guard('supplier')->user()->supplier_id;

        $rfq = Rfq::with(['items.item', 'items.unitOfMeasure', 'project'])
            ->whereHas('suppliers', function ($q) use ($supplierId) {
                $q->where('rfqs_supplier_id', $supplierId);
            })
            ->findOrFail($rfqId);

        $rfqSupplier = RfqSupplier::where('rfqs_rfq_id', $rfqId)
            ->where('rfqs_supplier_id', $supplierId)
            ->firstOrFail();

        return view('supplier.rfq.show', compact('rfq', 'rfqSupplier'));
    }

    /**
     * Submit quote for RFQ items.
     */
    public function submitQuote(Request $request, $rfqId)
    {
        $supplierId = Auth::guard('supplier')->user()->supplier_id;

        $rfqSupplier = RfqSupplier::where('rfqs_rfq_id', $rfqId)
            ->where('rfqs_supplier_id', $supplierId)
            ->firstOrFail();

        $request->validate([
            'quotes' => 'required|array|min:1',
            'quotes.*.rfq_item_id' => 'required|integer',
            'quotes.*.price' => 'required|numeric|min:0',
            'quotes.*.lead_time_days' => 'nullable|integer|min:0',
        ]);

        $this->poService->recordQuote($rfqSupplier->rfqs_id, $request->quotes);

        return redirect()->route('supplier.rfq.index')->with('status', 'Quote submitted successfully.');
    }
}
