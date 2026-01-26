<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checklist;
use App\Models\ChecklistItem;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    public function index()
    {
        $checklists = Checklist::withCount('items')->orderByDesc('cl_id')->paginate(15);
        return view('admin.checklists.index', compact('checklists'));
    }

    public function create()
    {
        $equipments = Equipment::active()->orderBy('eq_id', 'desc')->get();
        $users = User::orderBy('name')->get();
        return view('admin.checklists.create', compact('equipments', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cl_name' => 'required|string|max:255',
            'cl_frequency' => 'nullable|string|max:100',
            'cl_start_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*' => 'required|string|max:255',
        ]);

        $checklist = Checklist::create([
            'cl_name' => $request->cl_name,
            'cl_frequency' => $request->cl_frequency,
            'cl_eq_ids' => $request->cl_eq_ids ? array_values($request->cl_eq_ids) : [],
            'cl_user_ids' => $request->cl_user_ids ? array_values($request->cl_user_ids) : [],
            'cl_start_date' => $request->cl_start_date,
            'status' => 1,
            'created_date' => now(),
        ]);

        foreach ($request->items as $itemText) {
            ChecklistItem::create([
                'cl_id' => $checklist->cl_id,
                'cli_item' => $itemText,
                'status' => 1,
                'created_date' => now(),
            ]);
        }

        return redirect()->route('admin.checklists.index')->with('success', 'Checklist created.');
    }

    public function edit(Checklist $checklist)
    {
        $checklist->load('items');
        $equipments = Equipment::active()->orderBy('eq_id', 'desc')->get();
        $users = User::orderBy('name')->get();
        return view('admin.checklists.edit', compact('checklist', 'equipments', 'users'));
    }

    public function update(Request $request, Checklist $checklist)
    {
        $request->validate([
            'cl_name' => 'required|string|max:255',
            'cl_frequency' => 'nullable|string|max:100',
            'cl_start_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*' => 'required|string|max:255',
        ]);

        $checklist->update([
            'cl_name' => $request->cl_name,
            'cl_frequency' => $request->cl_frequency,
            'cl_eq_ids' => $request->cl_eq_ids ? array_values($request->cl_eq_ids) : [],
            'cl_user_ids' => $request->cl_user_ids ? array_values($request->cl_user_ids) : [],
            'cl_start_date' => $request->cl_start_date,
            'modified_date' => now(),
        ]);

        // Sync items: simple replace
        ChecklistItem::where('cl_id', $checklist->cl_id)->delete();
        foreach ($request->items as $itemText) {
            ChecklistItem::create([
                'cl_id' => $checklist->cl_id,
                'cli_item' => $itemText,
                'status' => 1,
                'created_date' => now(),
            ]);
        }

        return redirect()->route('admin.checklists.index')->with('success', 'Checklist updated.');
    }

    public function destroy(Checklist $checklist)
    {
        $checklist->delete();
        return back()->with('success', 'Checklist deleted.');
    }
}
