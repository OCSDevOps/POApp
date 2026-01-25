<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'eqm_location' => $request->get('project'),
            'eqm_category' => $request->get('category'),
            'eqm_asset_type' => $request->get('type'),
            'eqm_status' => $request->get('status'),
            'eqm_current_operator' => $request->get('user'),
        ];

        $query = Equipment::active()->orderBy('eq_id', 'desc');
        foreach ($filters as $column => $value) {
            if ($value !== null && $value !== '') {
                $query->where($column, $value);
            }
        }

        $equipments = $query->get();
        $availableEquipments = Equipment::active()->where('eqm_status', 'Available')->get();
        $inUseEquipments = Equipment::active()->where('eqm_status', 'In Use')->get();

        return view('admin.equipment.index', compact('equipments', 'availableEquipments', 'inUseEquipments', 'filters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'eqm_asset_name' => 'required|string|max:191',
            'eqm_asset_description' => 'required|string|max:1000',
            'eqm_asset_type' => 'required|string|max:100',
            'eqm_asset_tag' => 'required|string|max:191|unique:eq_master,eqm_asset_tag',
            'eqm_category' => 'nullable|string|max:191',
            'eqm_status' => 'required|string|max:50',
            'eqm_existing_reading' => 'nullable|numeric',
            'eqm_estimate_usage' => 'nullable|numeric',
            'eqm_location' => 'nullable|string|max:191',
            'eqm_supplier' => 'nullable|numeric',
            'eqm_current_operator' => 'nullable|numeric',
            'eqm_license_plate' => 'nullable|string|max:191',
            'eqm_year' => 'nullable|string|max:50',
            'eqm_brand' => 'nullable|string|max:191',
            'eqm_model' => 'nullable|string|max:191',
            'eqm_asset_picture' => 'nullable|image|max:5120',
        ]);

        $remainingLife = null;
        if ($request->filled('eqm_estimate_usage') && $request->filled('eqm_existing_reading')) {
            $remainingLife = $request->eqm_estimate_usage - $request->eqm_existing_reading;
        }

        $data = array_merge($validated, [
            'eqm_remaining_life' => $remainingLife,
            'eqm_created_date' => now(),
        ]);

        if ($request->hasFile('eqm_asset_picture')) {
            $path = $request->file('eqm_asset_picture')->storeAs(
                'equipment',
                Str::random(8) . '_' . $request->file('eqm_asset_picture')->getClientOriginalName(),
                'public'
            );
            $data['eqm_asset_picture'] = $path;
        }

        Equipment::create($data);

        return redirect()->route('admin.equipment.index')->with('success', 'Equipment created successfully.');
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'eqm_asset_name' => 'required|string|max:191',
            'eqm_asset_description' => 'required|string|max:1000',
            'eqm_asset_type' => 'required|string|max:100',
            'eqm_asset_tag' => 'required|string|max:191|unique:eq_master,eqm_asset_tag,' . $equipment->eq_id . ',eq_id',
            'eqm_category' => 'nullable|string|max:191',
            'eqm_status' => 'required|string|max:50',
            'eqm_existing_reading' => 'nullable|numeric',
            'eqm_estimate_usage' => 'nullable|numeric',
            'eqm_location' => 'nullable|string|max:191',
            'eqm_supplier' => 'nullable|numeric',
            'eqm_current_operator' => 'nullable|numeric',
            'eqm_license_plate' => 'nullable|string|max:191',
            'eqm_year' => 'nullable|string|max:50',
            'eqm_brand' => 'nullable|string|max:191',
            'eqm_model' => 'nullable|string|max:191',
            'eqm_asset_picture' => 'nullable|image|max:5120',
        ]);

        $remainingLife = null;
        if ($request->filled('eqm_estimate_usage') && $request->filled('eqm_existing_reading')) {
            $remainingLife = $request->eqm_estimate_usage - $request->eqm_existing_reading;
        }

        $data = array_merge($validated, [
            'eqm_remaining_life' => $remainingLife,
        ]);

        if ($request->hasFile('eqm_asset_picture')) {
            if ($equipment->eqm_asset_picture) {
                Storage::disk('public')->delete($equipment->eqm_asset_picture);
            }
            $path = $request->file('eqm_asset_picture')->storeAs(
                'equipment',
                Str::random(8) . '_' . $request->file('eqm_asset_picture')->getClientOriginalName(),
                'public'
            );
            $data['eqm_asset_picture'] = $path;
        }

        $equipment->update($data);

        return redirect()->route('admin.equipment.index')->with('success', 'Equipment updated successfully.');
    }

    public function destroy(Equipment $equipment)
    {
        // TODO: add usage checks when reservations/maintenance are ported
        if ($equipment->eqm_asset_picture) {
            Storage::disk('public')->delete($equipment->eqm_asset_picture);
        }
        $equipment->delete();

        return redirect()->route('admin.equipment.index')->with('success', 'Equipment deleted successfully.');
    }
}
