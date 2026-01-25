<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PoTemplate;
use App\Models\PoTemplateItem;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\UnitOfMeasure;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PoTemplateController extends Controller
{
    protected $poService;

    public function __construct(PurchaseOrderService $poService)
    {
        $this->poService = $poService;
    }

    /**
     * Display a listing of PO templates.
     */
    public function index(Request $request)
    {
        $query = PoTemplate::with(['defaultProject', 'defaultSupplier', 'items']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pot_name', 'like', "%{$search}%")
                  ->orWhere('pot_description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('pot_status', $request->status);
        }

        $templates = $query->orderBy('pot_created_at', 'DESC')->paginate(15);

        return view('admin.template.index', compact('templates'));
    }

    /**
     * Show the form for creating a new PO template.
     */
    public function create()
    {
        $projects = Project::active()->orderByName()->get();
        $suppliers = Supplier::active()->orderByName()->get();
        $items = Item::active()->orderByName()->get();
        $uoms = UnitOfMeasure::active()->orderByName()->get();

        return view('admin.template.create', compact('projects', 'suppliers', 'items', 'uoms'));
    }

    /**
     * Store a newly created PO template.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'nullable|string|max:500',
            'default_project_id' => 'nullable|exists:project_master,proj_id',
            'default_supplier_id' => 'nullable|exists:supplier_master,sup_id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:item_master,item_id',
            'items.*.default_quantity' => 'required|integer|min:1',
            'items.*.uom_id' => 'required|exists:unit_of_measure_tab,uom_id',
        ]);

        DB::beginTransaction();

        try {
            $template = PoTemplate::create([
                'pot_name' => $request->name,
                'pot_description' => $request->description,
                'pot_default_project_id' => $request->default_project_id,
                'pot_default_supplier_id' => $request->default_supplier_id,
                'pot_status' => 1,
                'pot_created_by' => auth()->id(),
                'pot_created_at' => now(),
            ]);

            // Add items
            $sortOrder = 1;
            foreach ($request->items as $item) {
                PoTemplateItem::create([
                    'poti_template_id' => $template->pot_id,
                    'poti_item_id' => $item['item_id'],
                    'poti_default_quantity' => $item['default_quantity'],
                    'poti_uom_id' => $item['uom_id'],
                    'poti_notes' => $item['notes'] ?? null,
                    'poti_sort_order' => $sortOrder++,
                    'poti_created_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.template.show', $template->pot_id)
                ->with('success', 'PO template created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating template: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified PO template.
     */
    public function show($id)
    {
        $template = PoTemplate::with([
            'defaultProject',
            'defaultSupplier',
            'items.item',
            'items.unitOfMeasure',
        ])->findOrFail($id);

        return view('admin.template.show', compact('template'));
    }

    /**
     * Show the form for editing the specified PO template.
     */
    public function edit($id)
    {
        $template = PoTemplate::with('items')->findOrFail($id);
        $projects = Project::active()->orderByName()->get();
        $suppliers = Supplier::active()->orderByName()->get();
        $items = Item::active()->orderByName()->get();
        $uoms = UnitOfMeasure::active()->orderByName()->get();

        return view('admin.template.edit', compact('template', 'projects', 'suppliers', 'items', 'uoms'));
    }

    /**
     * Update the specified PO template.
     */
    public function update(Request $request, $id)
    {
        $template = PoTemplate::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'nullable|string|max:500',
            'default_project_id' => 'nullable|exists:project_master,proj_id',
            'default_supplier_id' => 'nullable|exists:supplier_master,sup_id',
            'status' => 'required|in:0,1',
        ]);

        DB::beginTransaction();

        try {
            $template->update([
                'pot_name' => $request->name,
                'pot_description' => $request->description,
                'pot_default_project_id' => $request->default_project_id,
                'pot_default_supplier_id' => $request->default_supplier_id,
                'pot_status' => $request->status,
                'pot_modified_by' => auth()->id(),
                'pot_modified_at' => now(),
            ]);

            // Update items if provided
            if ($request->has('items')) {
                // Remove existing items
                PoTemplateItem::where('poti_template_id', $template->pot_id)->delete();

                // Add new items
                $sortOrder = 1;
                foreach ($request->items as $item) {
                    PoTemplateItem::create([
                        'poti_template_id' => $template->pot_id,
                        'poti_item_id' => $item['item_id'],
                        'poti_default_quantity' => $item['default_quantity'],
                        'poti_uom_id' => $item['uom_id'],
                        'poti_notes' => $item['notes'] ?? null,
                        'poti_sort_order' => $sortOrder++,
                        'poti_created_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.template.show', $template->pot_id)
                ->with('success', 'PO template updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating template: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified PO template.
     */
    public function destroy($id)
    {
        $template = PoTemplate::findOrFail($id);

        DB::beginTransaction();

        try {
            // Delete items
            PoTemplateItem::where('poti_template_id', $id)->delete();
            
            // Delete template
            $template->delete();

            DB::commit();

            return redirect()->route('admin.template.index')
                ->with('success', 'PO template deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting template: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate a PO template.
     */
    public function duplicate($id)
    {
        $template = PoTemplate::with('items')->findOrFail($id);

        DB::beginTransaction();

        try {
            $newTemplate = PoTemplate::create([
                'pot_name' => $template->pot_name . ' (Copy)',
                'pot_description' => $template->pot_description,
                'pot_default_project_id' => $template->pot_default_project_id,
                'pot_default_supplier_id' => $template->pot_default_supplier_id,
                'pot_status' => 1,
                'pot_created_by' => auth()->id(),
                'pot_created_at' => now(),
            ]);

            // Copy items
            foreach ($template->items as $item) {
                PoTemplateItem::create([
                    'poti_template_id' => $newTemplate->pot_id,
                    'poti_item_id' => $item->poti_item_id,
                    'poti_default_quantity' => $item->poti_default_quantity,
                    'poti_uom_id' => $item->poti_uom_id,
                    'poti_notes' => $item->poti_notes,
                    'poti_sort_order' => $item->poti_sort_order,
                    'poti_created_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.template.edit', $newTemplate->pot_id)
                ->with('success', 'PO template duplicated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error duplicating template: ' . $e->getMessage());
        }
    }

    /**
     * Show form to create PO from template.
     */
    public function createPo($id)
    {
        $template = PoTemplate::with([
            'defaultProject',
            'defaultSupplier',
            'items.item',
            'items.unitOfMeasure',
        ])->findOrFail($id);

        $projects = Project::active()->orderByName()->get();
        $suppliers = Supplier::active()->orderByName()->get();

        return view('admin.template.create_po', compact('template', 'projects', 'suppliers'));
    }

    /**
     * Create PO from template.
     */
    public function storePo(Request $request, $id)
    {
        $request->validate([
            'project_id' => 'required|exists:project_master,proj_id',
            'supplier_id' => 'required|exists:supplier_master,sup_id',
            'quantities' => 'nullable|array',
            'quantities.*' => 'nullable|integer|min:0',
        ]);

        try {
            $po = $this->poService->createFromTemplate(
                $id,
                $request->project_id,
                $request->supplier_id,
                $request->quantities ?? []
            );

            return redirect()->route('admin.porder.show', $po->porder_id)
                ->with('success', 'Purchase Order created from template successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating PO from template: ' . $e->getMessage());
        }
    }
}
