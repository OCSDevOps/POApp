<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers.
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('sup_id', 'DESC')->get();
        
        return view('admin.supplier.supplier_list_view', compact('suppliers'));
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create()
    {
        return view('admin.supplier.add_supplier');
    }

    /**
     * Store a newly created supplier.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sup_name' => 'required|string|max:255',
            'sup_email' => 'required|email|unique:supplier_master,sup_email',
            'sup_mobile' => 'nullable|unique:supplier_master,sup_mobile',
        ]);

        Supplier::create([
            'sup_name' => $request->sup_name,
            'sup_code' => $request->sup_code,
            'sup_email' => $request->sup_email,
            'sup_phone' => $request->sup_phone,
            'sup_mobile' => $request->sup_mobile,
            'sup_address' => $request->sup_address,
            'sup_city' => $request->sup_city,
            'sup_state' => $request->sup_state,
            'sup_zip' => $request->sup_zip,
            'sup_country' => $request->sup_country,
            'sup_contact_person' => $request->sup_contact_person,
            'sup_status' => 1,
            'sup_created_by' => Auth::id(),
            'sup_created_at' => now(),
        ]);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Display the specified supplier.
     */
    public function show($id)
    {
        $supplier = Supplier::with('catalogItems')->findOrFail($id);
        
        return view('admin.supplier.view_supplier', compact('supplier'));
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        
        return view('admin.supplier.edit_supplier', compact('supplier'));
    }

    /**
     * Update the specified supplier.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'sup_name' => 'required|string|max:255',
            'sup_email' => 'required|email|unique:supplier_master,sup_email,' . $id . ',sup_id',
            'sup_mobile' => 'nullable|unique:supplier_master,sup_mobile,' . $id . ',sup_id',
        ]);

        $supplier = Supplier::findOrFail($id);

        $supplier->update([
            'sup_name' => $request->sup_name,
            'sup_code' => $request->sup_code,
            'sup_email' => $request->sup_email,
            'sup_phone' => $request->sup_phone,
            'sup_mobile' => $request->sup_mobile,
            'sup_address' => $request->sup_address,
            'sup_city' => $request->sup_city,
            'sup_state' => $request->sup_state,
            'sup_zip' => $request->sup_zip,
            'sup_country' => $request->sup_country,
            'sup_contact_person' => $request->sup_contact_person,
            'sup_modified_by' => Auth::id(),
            'sup_modified_at' => now(),
        ]);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Update supplier status.
     */
    public function updateStatus(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        
        $supplier->update([
            'sup_status' => $request->status,
            'sup_modified_by' => Auth::id(),
            'sup_modified_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy($id)
    {
        // Check if supplier has purchase orders
        $hasPO = DB::table('purchase_order_master')
            ->where('porder_supplier_ms', $id)
            ->exists();

        if ($hasPO) {
            return back()->with('error', 'Cannot delete supplier with existing purchase orders.');
        }

        Supplier::destroy($id);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
