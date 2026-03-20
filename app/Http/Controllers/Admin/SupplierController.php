<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;
use App\Services\Cache\ReferenceDataCacheService;

class SupplierController extends Controller
{
    protected $referenceDataCacheService;

    public function __construct(ReferenceDataCacheService $referenceDataCacheService)
    {
        $this->referenceDataCacheService = $referenceDataCacheService;
    }

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
            'sup_name' => 'required|string|max:250',
            'sup_email' => 'required|email|unique:supplier_master,sup_email',
            'sup_type' => 'nullable|integer|in:1,2,3',
        ]);

        Supplier::create([
            'sup_name' => $request->sup_name,
            'sup_email' => $request->sup_email,
            'sup_phone' => $request->sup_phone,
            'sup_address' => $request->sup_address,
            'sup_contact_person' => $request->sup_contact_person,
            'sup_details' => $request->sup_details,
            'sup_type' => $request->sup_type ?? 1,
            'sup_status' => 1,
            'sup_createby' => Auth::id(),
            'sup_createdate' => now(),
            'company_id' => session('company_id'),
        ]);
        $this->referenceDataCacheService->clearCompanyReferenceCaches((int) session('company_id'));

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Display the specified supplier.
     */
    public function show($id)
    {
        $supplier = Supplier::with('catalogItems')->findOrFail($id);
        abort_unless($supplier->company_id === session('company_id'), 403, 'Unauthorized access');
        
        return view('admin.supplier.view_supplier', compact('supplier'));
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        abort_unless($supplier->company_id === session('company_id'), 403, 'Unauthorized access');
        
        return view('admin.supplier.edit_supplier', compact('supplier'));
    }

    /**
     * Update the specified supplier.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'sup_name' => 'required|string|max:250',
            'sup_email' => 'required|email|unique:supplier_master,sup_email,' . $id . ',sup_id',
            'sup_type' => 'nullable|integer|in:1,2,3',
        ]);

        $supplier = Supplier::findOrFail($id);
        abort_unless($supplier->company_id === session('company_id'), 403, 'Unauthorized access');

        $supplier->update([
            'sup_name' => $request->sup_name,
            'sup_email' => $request->sup_email,
            'sup_phone' => $request->sup_phone,
            'sup_address' => $request->sup_address,
            'sup_contact_person' => $request->sup_contact_person,
            'sup_details' => $request->sup_details,
            'sup_type' => $request->sup_type ?? $supplier->sup_type,
            'sup_modifyby' => Auth::id(),
            'sup_modifydate' => now(),
        ]);
        $this->referenceDataCacheService->clearCompanyReferenceCaches((int) session('company_id'));

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
            'sup_modifyby' => Auth::id(),
            'sup_modifydate' => now(),
        ]);
        $this->referenceDataCacheService->clearCompanyReferenceCaches((int) session('company_id'));

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        abort_unless($supplier->company_id === session('company_id'), 403, 'Unauthorized access');

        // Check if supplier has purchase orders
        $hasPO = DB::table('purchase_order_master')
            ->where('porder_supplier_ms', $id)
            ->where('company_id', session('company_id'))
            ->exists();

        if ($hasPO) {
            return back()->with('error', 'Cannot delete supplier with existing purchase orders.');
        }

        $supplier->delete();
        $this->referenceDataCacheService->clearCompanyReferenceCaches((int) session('company_id'));

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
