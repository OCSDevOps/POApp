<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checklist;
use App\Models\ChecklistItem;
use App\Models\ChecklistPerformance;
use App\Models\ChecklistPerformanceDetail;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PerformChecklistController extends Controller
{
    public function index()
    {
        $performances = ChecklistPerformance::with(['checklist', 'equipment'])
            ->orderByDesc('cl_p_id')
            ->paginate(15);

        return view('admin.checklists.perform.index', compact('performances'));
    }

    public function create()
    {
        $checklists = Checklist::with('items')->where('status', 1)->orderBy('cl_name')->get();
        $equipments = Equipment::active()->orderBy('eq_id', 'desc')->get();
        return view('admin.checklists.perform.create', compact('checklists', 'equipments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cl_id' => 'required|exists:checklist_master,cl_id',
            'cl_eq_id' => 'nullable|exists:eq_master,eq_id',
            'cl_p_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.cli_id' => 'required|exists:checklist_details,cli_id',
            'items.*.value' => 'nullable|string|max:255',
            'items.*.notes' => 'nullable|string',
            'items.*.attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,csv|max:5120',
        ]);

        $performance = ChecklistPerformance::create([
            'cl_id' => $request->cl_id,
            'cl_eq_id' => $request->cl_eq_id,
            'cl_p_date' => $request->cl_p_date,
            'status' => 1,
            'created_date' => now(),
        ]);

        foreach ($request->items as $item) {
            $path = null;
            if (isset($item['attachment'])) {
                $path = $item['attachment']->store('checklists', 'public');
            }

            ChecklistPerformanceDetail::create([
                'cl_p_id' => $performance->cl_p_id,
                'cl_pd_cli_id' => $item['cli_id'],
                'cl_pd_cli_value' => $item['value'] ?? null,
                'cl_pd_cli_notes' => $item['notes'] ?? null,
                'cl_pd_cli_attachment' => $path,
                'status' => 1,
                'created_date' => now(),
            ]);
        }

        return redirect()->route('admin.performchecklists.index')->with('success', 'Checklist recorded.');
    }

    public function show(ChecklistPerformance $performchecklist)
    {
        $performchecklist->load(['checklist.items', 'details.item', 'equipment']);
        return view('admin.checklists.perform.show', compact('performchecklist'));
    }
}
