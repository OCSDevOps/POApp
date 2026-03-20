<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Project;
use App\Models\Rfq;
use App\Models\RfqItem;
use App\Models\RfqQuote;
use App\Models\RfqSupplier;
use App\Models\Supplier;
use App\Models\UnitOfMeasure;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RfqController extends Controller
{
    protected $poService;

    public function __construct(PurchaseOrderService $poService)
    {
        $this->poService = $poService;
    }

    /**
     * Display a listing of RFQs.
     */
    public function index(Request $request)
    {
        $query = Rfq::with(['project', 'suppliers', 'items']);

        // Filters
        if ($request->filled('project_id')) {
            $query->where('rfq_project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('rfq_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('rfq_no', 'like', "%{$search}%")
                  ->orWhere('rfq_title', 'like', "%{$search}%");
            });
        }

        $rfqs = $query->orderBy('rfq_created_at', 'DESC')->paginate(15);
        $projects = Project::active()->orderByName()->get();

        return view('admin.rfq.index', compact('rfqs', 'projects'));
    }

    /**
     * Show the form for creating a new RFQ.
     */
    public function create()
    {
        $projects = Project::active()->orderByName()->get();
        $suppliers = Supplier::active()->orderByName()->get();
        $items = Item::active()->orderByName()->get();
        $uoms = UnitOfMeasure::active()->orderByName()->get();

        return view('admin.rfq.create', compact('projects', 'suppliers', 'items', 'uoms'));
    }

    /**
     * Store a newly created RFQ.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:project_master,proj_id',
            'title' => 'required|string|max:250',
            'due_date' => 'required|date|after:today',
            'supplier_ids' => 'required|array|min:1',
            'supplier_ids.*' => 'exists:supplier_master,sup_id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:item_master,item_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.uom_id' => 'required|exists:unit_of_measure_tab,uom_id',
        ]);

        try {
            $rfq = $this->poService->createRfq(
                [
                    'project_id' => $request->project_id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'due_date' => $request->due_date,
                ],
                $request->items,
                $request->supplier_ids
            );

            return redirect()->route('admin.rfq.show', $rfq->rfq_id)
                ->with('success', 'RFQ created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating RFQ: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified RFQ.
     */
    public function show($id)
    {
        $rfq = Rfq::with([
            'project',
            'items.item',
            'items.unitOfMeasure',
            'items.quotes',
            'suppliers.supplier',
            'suppliers.quotes',
        ])->findOrFail($id);

        return view('admin.rfq.show', compact('rfq'));
    }

    /**
     * Show the form for editing the specified RFQ.
     */
    public function edit($id)
    {
        $rfq = Rfq::with(['items', 'suppliers'])->findOrFail($id);

        if ($rfq->rfq_status != Rfq::STATUS_DRAFT) {
            return back()->with('error', 'Only draft RFQs can be edited.');
        }

        $projects = Project::active()->orderByName()->get();
        $suppliers = Supplier::active()->orderByName()->get();
        $items = Item::active()->orderByName()->get();
        $uoms = UnitOfMeasure::active()->orderByName()->get();

        return view('admin.rfq.edit', compact('rfq', 'projects', 'suppliers', 'items', 'uoms'));
    }

    /**
     * Update the specified RFQ.
     */
    public function update(Request $request, $id)
    {
        $rfq = Rfq::findOrFail($id);

        if ($rfq->rfq_status != Rfq::STATUS_DRAFT) {
            return back()->with('error', 'Only draft RFQs can be edited.');
        }

        $request->validate([
            'project_id' => 'required|exists:project_master,proj_id',
            'title' => 'required|string|max:250',
            'due_date' => 'required|date|after:today',
        ]);

        DB::beginTransaction();

        try {
            $rfq->update([
                'rfq_project_id' => $request->project_id,
                'rfq_title' => $request->title,
                'rfq_description' => $request->description,
                'rfq_due_date' => $request->due_date,
                'rfq_modified_by' => auth()->id(),
                'rfq_modified_at' => now(),
            ]);

            // Update items if provided
            if ($request->has('items')) {
                // Remove existing items
                RfqItem::where('rfqi_rfq_id', $rfq->rfq_id)->delete();

                // Add new items
                foreach ($request->items as $item) {
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
            }

            // Update suppliers if provided
            if ($request->has('supplier_ids')) {
                // Remove existing suppliers
                RfqSupplier::where('rfqs_rfq_id', $rfq->rfq_id)->delete();

                // Add new suppliers
                foreach ($request->supplier_ids as $supplierId) {
                    RfqSupplier::create([
                        'rfqs_rfq_id' => $rfq->rfq_id,
                        'rfqs_supplier_id' => $supplierId,
                        'rfqs_status' => RfqSupplier::STATUS_PENDING,
                        'rfqs_created_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.rfq.show', $rfq->rfq_id)
                ->with('success', 'RFQ updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating RFQ: ' . $e->getMessage());
        }
    }

    /**
     * Send RFQ to suppliers.
     */
    public function send($id)
    {
        $rfq = Rfq::findOrFail($id);

        if ($rfq->rfq_status != Rfq::STATUS_DRAFT) {
            return back()->with('error', 'Only draft RFQs can be sent.');
        }

        try {
            $this->poService->sendRfq($id);
            return back()->with('success', 'RFQ sent to suppliers successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error sending RFQ: ' . $e->getMessage());
        }
    }

    /**
     * Record supplier quote.
     */
    public function recordQuote(Request $request, $id, $supplierId)
    {
        $rfq = Rfq::findOrFail($id);
        $rfqSupplier = RfqSupplier::where('rfqs_rfq_id', $id)
            ->where('rfqs_supplier_id', $supplierId)
            ->firstOrFail();

        $request->validate([
            'quotes' => 'required|array|min:1',
            'quotes.*.rfq_item_id' => 'required|exists:rfq_items,rfqi_id',
            'quotes.*.price' => 'required|numeric|min:0',
        ]);

        try {
            $this->poService->recordQuote($rfqSupplier->rfqs_id, $request->quotes);
            return back()->with('success', 'Quote recorded successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error recording quote: ' . $e->getMessage());
        }
    }

    /**
     * Select supplier and convert to PO.
     */
    public function convertToPo(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier_master,sup_id',
        ]);

        try {
            $po = $this->poService->convertRfqToPo($id, $request->supplier_id);
            return redirect()->route('admin.porder.show', $po->porder_id)
                ->with('success', 'RFQ converted to Purchase Order successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error converting RFQ to PO: ' . $e->getMessage());
        }
    }

    /**
     * Cancel RFQ.
     */
    public function cancel($id)
    {
        $rfq = Rfq::findOrFail($id);

        if (in_array($rfq->rfq_status, [Rfq::STATUS_CONVERTED, Rfq::STATUS_CANCELLED])) {
            return back()->with('error', 'This RFQ cannot be cancelled.');
        }

        $rfq->update([
            'rfq_status' => Rfq::STATUS_CANCELLED,
            'rfq_modified_by' => auth()->id(),
            'rfq_modified_at' => now(),
        ]);

        return back()->with('success', 'RFQ cancelled successfully.');
    }

    /**
     * Compare quotes from different suppliers.
     */
    public function compareQuotes($id)
    {
        $rfq = Rfq::with([
            'items.item',
            'items.quotes.rfqSupplier.supplier',
            'suppliers.supplier',
            'suppliers.quotes',
        ])->findOrFail($id);

        return view('admin.rfq.compare', compact('rfq'));
    }
}
