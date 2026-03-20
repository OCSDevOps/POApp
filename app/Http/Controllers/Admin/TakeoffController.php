<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessTakeoffDrawingJob;
use App\Models\AiSetting;
use App\Models\CostCode;
use App\Models\Item;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Takeoff;
use App\Models\TakeoffDrawing;
use App\Models\TakeoffItem;
use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TakeoffController extends Controller
{
    public function index(Request $request)
    {
        $query = Takeoff::with('project')
            ->orderByDesc('to_id');

        if ($request->filled('project_id')) {
            $query->byProject($request->project_id);
        }
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        $takeoffs = $query->paginate(15)->withQueryString();
        $projects = Project::active()->orderByName()->get();

        return view('admin.takeoffs.index', compact('takeoffs', 'projects'));
    }

    public function create()
    {
        $projects = Project::active()->orderByName()->get();
        $items = Item::active()->orderByName()->get();
        $uoms = UnitOfMeasure::active()->orderByName()->get();
        $costCodes = CostCode::active()->orderById()->get();
        $aiEnabled = AiSetting::where('company_id', session('company_id'))
            ->where('is_active', 1)->exists();

        return view('admin.takeoffs.create', compact('projects', 'items', 'uoms', 'costCodes', 'aiEnabled'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:project_master,proj_id',
            'title' => 'required|string|max:250',
            'description' => 'nullable|string',
            'drawings' => 'nullable|array|max:20',
            'drawings.*' => 'file|mimes:pdf,jpg,jpeg,png,tif,tiff,bmp|max:20480',
            'items' => 'nullable|array',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $companyId = session('company_id');

            $takeoff = Takeoff::create([
                'to_number' => Takeoff::generateNumber(),
                'to_project_id' => $request->project_id,
                'to_title' => $request->title,
                'to_description' => $request->description,
                'to_status' => Takeoff::STATUS_DRAFT,
                'to_createby' => Auth::id(),
                'to_createdate' => now(),
                'company_id' => $companyId,
            ]);

            // Save manual line items
            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    if (empty($itemData['description'])) continue;

                    $qty = (float) ($itemData['quantity'] ?? 0);
                    $price = (float) ($itemData['unit_price'] ?? 0);

                    TakeoffItem::create([
                        'tod_takeoff_id' => $takeoff->to_id,
                        'tod_item_code' => $itemData['item_code'] ?? null,
                        'tod_description' => $itemData['description'],
                        'tod_quantity' => $qty,
                        'tod_uom_id' => $itemData['uom_id'] ?: null,
                        'tod_unit_price' => $price,
                        'tod_subtotal' => round($qty * $price, 2),
                        'tod_cost_code_id' => $itemData['cost_code_id'] ?: null,
                        'tod_source' => 'manual',
                        'tod_status' => 1,
                        'tod_createdate' => now(),
                        'company_id' => $companyId,
                    ]);
                }
            }

            // Save drawings
            if ($request->hasFile('drawings')) {
                $this->storeDrawings($takeoff, $request->file('drawings'), $companyId);
            }

            $takeoff->recalculateTotals();

            DB::commit();

            return redirect()->route('admin.takeoffs.show', $takeoff->to_id)
                ->with('success', 'Takeoff created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating takeoff: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $takeoff = Takeoff::with(['project', 'activeItems.unitOfMeasure', 'activeItems.costCode', 'activeDrawings'])
            ->findOrFail($id);

        abort_unless($takeoff->company_id == session('company_id'), 403);

        $aiEnabled = AiSetting::where('company_id', session('company_id'))
            ->where('is_active', 1)->exists();

        return view('admin.takeoffs.show', compact('takeoff', 'aiEnabled'));
    }

    public function edit($id)
    {
        $takeoff = Takeoff::with(['activeItems', 'activeDrawings'])->findOrFail($id);
        abort_unless($takeoff->company_id == session('company_id'), 403);

        if (!in_array($takeoff->to_status, [Takeoff::STATUS_DRAFT, Takeoff::STATUS_REVIEW])) {
            return back()->with('error', 'Only draft or review takeoffs can be edited.');
        }

        $projects = Project::active()->orderByName()->get();
        $items = Item::active()->orderByName()->get();
        $uoms = UnitOfMeasure::active()->orderByName()->get();
        $costCodes = CostCode::active()->orderById()->get();

        return view('admin.takeoffs.edit', compact('takeoff', 'projects', 'items', 'uoms', 'costCodes'));
    }

    public function update(Request $request, $id)
    {
        $takeoff = Takeoff::findOrFail($id);
        abort_unless($takeoff->company_id == session('company_id'), 403);

        if (!in_array($takeoff->to_status, [Takeoff::STATUS_DRAFT, Takeoff::STATUS_REVIEW])) {
            return back()->with('error', 'Only draft or review takeoffs can be edited.');
        }

        $request->validate([
            'project_id' => 'required|exists:project_master,proj_id',
            'title' => 'required|string|max:250',
            'description' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $companyId = session('company_id');

            $takeoff->update([
                'to_project_id' => $request->project_id,
                'to_title' => $request->title,
                'to_description' => $request->description,
                'to_modifyby' => Auth::id(),
                'to_modifydate' => now(),
            ]);

            // Replace all line items
            TakeoffItem::where('tod_takeoff_id', $takeoff->to_id)->delete();

            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    if (empty($itemData['description'])) continue;

                    $qty = (float) ($itemData['quantity'] ?? 0);
                    $price = (float) ($itemData['unit_price'] ?? 0);

                    TakeoffItem::create([
                        'tod_takeoff_id' => $takeoff->to_id,
                        'tod_item_code' => $itemData['item_code'] ?? null,
                        'tod_description' => $itemData['description'],
                        'tod_quantity' => $qty,
                        'tod_uom_id' => $itemData['uom_id'] ?: null,
                        'tod_unit_price' => $price,
                        'tod_subtotal' => round($qty * $price, 2),
                        'tod_cost_code_id' => $itemData['cost_code_id'] ?: null,
                        'tod_source' => $itemData['source'] ?? 'manual',
                        'tod_ai_confidence' => $itemData['ai_confidence'] ?? null,
                        'tod_notes' => $itemData['notes'] ?? null,
                        'tod_status' => 1,
                        'tod_createdate' => now(),
                        'company_id' => $companyId,
                    ]);
                }
            }

            $takeoff->recalculateTotals();

            DB::commit();

            return redirect()->route('admin.takeoffs.show', $takeoff->to_id)
                ->with('success', 'Takeoff updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating takeoff: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $takeoff = Takeoff::findOrFail($id);
        abort_unless($takeoff->company_id == session('company_id'), 403);

        if ($takeoff->to_status === Takeoff::STATUS_FINALIZED) {
            return back()->with('error', 'Finalized takeoffs cannot be deleted.');
        }

        // Delete drawing files
        foreach ($takeoff->drawings as $drawing) {
            Storage::disk('public')->delete($drawing->tdr_path);
        }

        $takeoff->drawings()->delete();
        $takeoff->items()->delete();
        $takeoff->delete();

        return redirect()->route('admin.takeoffs.index')
            ->with('success', 'Takeoff deleted successfully.');
    }

    public function uploadDrawings(Request $request, $id)
    {
        $takeoff = Takeoff::findOrFail($id);
        abort_unless($takeoff->company_id == session('company_id'), 403);

        $request->validate([
            'drawings' => 'required|array|max:20',
            'drawings.*' => 'file|mimes:pdf,jpg,jpeg,png,tif,tiff,bmp|max:20480',
        ]);

        $this->storeDrawings($takeoff, $request->file('drawings'), session('company_id'));

        return back()->with('success', count($request->file('drawings')) . ' drawing(s) uploaded successfully.');
    }

    public function processDrawing($id, $drawingId)
    {
        $takeoff = Takeoff::findOrFail($id);
        abort_unless($takeoff->company_id == session('company_id'), 403);

        $drawing = TakeoffDrawing::where('tdr_takeoff_id', $id)
            ->where('tdr_id', $drawingId)
            ->where('tdr_status', 1)
            ->firstOrFail();

        $aiSettings = AiSetting::where('company_id', session('company_id'))
            ->where('is_active', 1)->first();

        if (!$aiSettings) {
            return back()->with('error', 'AI is not configured. Please set up AI settings first.');
        }

        // Update takeoff status to processing
        if ($takeoff->to_status === Takeoff::STATUS_DRAFT) {
            $takeoff->update(['to_status' => Takeoff::STATUS_PROCESSING]);
        }

        $drawing->update(['tdr_ai_status' => 'pending']);

        // Dispatch async job (or process synchronously if queue not running)
        try {
            ProcessTakeoffDrawingJob::dispatch($drawing->tdr_id, session('company_id'));
        } catch (\Exception $e) {
            // Fallback: process synchronously
            $job = new ProcessTakeoffDrawingJob($drawing->tdr_id, session('company_id'));
            $job->handle();
        }

        return back()->with('success', 'AI processing started for "' . $drawing->tdr_original_name . '". Refresh the page to see results.');
    }

    public function deleteDrawing($id, $drawingId)
    {
        $takeoff = Takeoff::findOrFail($id);
        abort_unless($takeoff->company_id == session('company_id'), 403);

        $drawing = TakeoffDrawing::where('tdr_takeoff_id', $id)
            ->where('tdr_id', $drawingId)
            ->firstOrFail();

        Storage::disk('public')->delete($drawing->tdr_path);
        $drawing->delete();

        return back()->with('success', 'Drawing removed.');
    }

    public function downloadDrawing($id, $drawingId)
    {
        $takeoff = Takeoff::findOrFail($id);
        abort_unless($takeoff->company_id == session('company_id'), 403);

        $drawing = TakeoffDrawing::where('tdr_takeoff_id', $id)
            ->where('tdr_id', $drawingId)
            ->firstOrFail();

        return Storage::disk('public')->download(
            $drawing->tdr_path,
            $drawing->tdr_original_name
        );
    }

    public function checkProcessingStatus($id)
    {
        $takeoff = Takeoff::findOrFail($id);
        abort_unless($takeoff->company_id == session('company_id'), 403);

        $drawings = $takeoff->activeDrawings()->get(['tdr_id', 'tdr_original_name', 'tdr_ai_status', 'tdr_ai_error']);
        $itemCount = $takeoff->activeItems()->count();

        return response()->json([
            'takeoff_status' => $takeoff->to_status,
            'drawings' => $drawings,
            'item_count' => $itemCount,
            'all_complete' => $drawings->every(fn($d) => in_array($d->tdr_ai_status, ['completed', 'failed', 'pending'])),
        ]);
    }

    public function finalize($id)
    {
        $takeoff = Takeoff::findOrFail($id);
        abort_unless($takeoff->company_id == session('company_id'), 403);

        if ($takeoff->activeItems()->count() === 0) {
            return back()->with('error', 'Cannot finalize a takeoff with no items.');
        }

        $takeoff->update([
            'to_status' => Takeoff::STATUS_FINALIZED,
            'to_finalized_by' => Auth::id(),
            'to_finalized_at' => now(),
            'to_modifyby' => Auth::id(),
            'to_modifydate' => now(),
        ]);

        return back()->with('success', 'Takeoff finalized successfully.');
    }

    public function convertToPo($id)
    {
        $takeoff = Takeoff::with('activeItems')->findOrFail($id);
        abort_unless($takeoff->company_id == session('company_id'), 403);

        if ($takeoff->to_status !== Takeoff::STATUS_FINALIZED) {
            return back()->with('error', 'Only finalized takeoffs can be converted to a Purchase Order.');
        }

        $itemsWithCode = $takeoff->activeItems->filter(fn($i) => !empty($i->tod_item_code));

        if ($itemsWithCode->isEmpty()) {
            return back()->with('error', 'No matched items to convert. Please match items to your item catalog before converting.');
        }

        DB::beginTransaction();

        try {
            $companyId = session('company_id');

            // Generate PO number
            $lastPo = PurchaseOrder::withoutGlobalScopes()->max('porder_id') ?? 0;
            $poNumber = 'PO-' . str_pad($lastPo + 1, 6, '0', STR_PAD_LEFT);

            $totalAmount = $itemsWithCode->sum('tod_subtotal');

            $po = PurchaseOrder::create([
                'porder_no' => $poNumber,
                'porder_project_ms' => $takeoff->to_project_id,
                'porder_description' => 'Generated from Takeoff ' . $takeoff->to_number . ': ' . $takeoff->to_title,
                'porder_total_item' => $itemsWithCode->count(),
                'porder_total_amount' => $totalAmount,
                'porder_total_tax' => 0,
                'porder_status' => 1,
                'porder_createby' => Auth::id(),
                'porder_createdate' => now(),
                'company_id' => $companyId,
            ]);

            foreach ($itemsWithCode as $toItem) {
                PurchaseOrderItem::create([
                    'po_detail_porder_ms' => $po->porder_id,
                    'po_detail_item' => $toItem->tod_item_code,
                    'po_detail_quantity' => $toItem->tod_quantity,
                    'po_detail_unitprice' => $toItem->tod_unit_price,
                    'po_detail_subtotal' => $toItem->tod_subtotal,
                    'po_detail_taxamount' => 0,
                    'po_detail_total' => $toItem->tod_subtotal,
                    'po_detail_createdate' => now(),
                    'po_detail_status' => 1,
                    'company_id' => $companyId,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.porder.edit', $po->porder_id)
                ->with('success', 'Purchase Order ' . $poNumber . ' created from takeoff. Please select a supplier and review.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating PO: ' . $e->getMessage());
        }
    }

    public function getItemSuggestions(Request $request)
    {
        $search = $request->input('q', '');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $items = Item::active()
            ->where(function ($q) use ($search) {
                $q->where('item_name', 'LIKE', "%{$search}%")
                  ->orWhere('item_code', 'LIKE', "%{$search}%");
            })
            ->with('unitOfMeasure')
            ->limit(10)
            ->get(['item_id', 'item_code', 'item_name', 'item_unit_ms']);

        return response()->json($items->map(function ($item) {
            $catalog = \App\Models\SupplierCatalog::getBestPrice($item->item_code);
            return [
                'item_code' => $item->item_code,
                'item_name' => $item->item_name,
                'uom_id' => $item->item_unit_ms,
                'uom_name' => $item->unitOfMeasure->uom_name ?? '',
                'unit_price' => $catalog ? (float) $catalog->supcat_price : 0,
            ];
        }));
    }

    /**
     * Store uploaded drawing files.
     */
    protected function storeDrawings(Takeoff $takeoff, array $files, int $companyId): void
    {
        foreach ($files as $file) {
            $storedPath = $file->storeAs(
                'takeoffs/' . $takeoff->to_id,
                Str::random(10) . '_' . $file->getClientOriginalName(),
                'public'
            );

            TakeoffDrawing::create([
                'tdr_takeoff_id' => $takeoff->to_id,
                'tdr_original_name' => $file->getClientOriginalName(),
                'tdr_path' => $storedPath,
                'tdr_mime' => $file->getClientMimeType(),
                'tdr_size' => $file->getSize(),
                'tdr_ai_status' => 'pending',
                'tdr_createby' => Auth::id(),
                'tdr_createdate' => now(),
                'tdr_status' => 1,
                'company_id' => $companyId,
            ]);
        }
    }
}
