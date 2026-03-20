<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CompaniesController extends Controller
{
    /**
     * Display a listing of companies.
     * Only accessible by super admins (u_type = 1)
     */
    public function index()
    {
        // Only super admins can manage companies
        abort_unless(auth()->user()->u_type == 1, 403, 'Unauthorized - Super admin access required');
        
        $companies = Company::withCount(['users', 'projects', 'purchaseOrders'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new company.
     */
    public function create()
    {
        abort_unless(auth()->user()->u_type == 1, 403);
        
        return view('admin.companies.create');
    }

    /**
     * Store a newly created company.
     */
    public function store(Request $request)
    {
        abort_unless(auth()->user()->u_type == 1, 403);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:companies,name',
            'subdomain' => 'nullable|string|max:255|unique:companies,subdomain|alpha_dash',
        ]);

        // Auto-generate subdomain if not provided
        if (empty($validated['subdomain'])) {
            $validated['subdomain'] = Str::slug($validated['name']);
        }

        $validated['status'] = 1; // Active
        
        $company = Company::create($validated);

        return redirect()->route('admin.companies.show', $company)
            ->with('success', 'Company created successfully.');
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company)
    {
        abort_unless(auth()->user()->u_type == 1, 403);
        
        $company->loadCount(['users', 'projects', 'purchaseOrders']);

        // Get statistics
        $stats = [
            'total_users' => $company->users()->count(),
            'total_projects' => $company->projects()->count(),
            'active_projects' => $company->projects()->where('proj_status', 1)->count(),
            'total_pos' => $company->purchaseOrders()->count(),
            'pending_pos' => $company->purchaseOrders()->where('porder_status', 1)->count(),
            'total_suppliers' => $company->suppliers()->count(),
            'total_items' => $company->items()->count(),
        ];

        return view('admin.companies.show', compact('company', 'stats'));
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company)
    {
        abort_unless(auth()->user()->u_type == 1, 403);
        
        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified company.
     */
    public function update(Request $request, Company $company)
    {
        abort_unless(auth()->user()->u_type == 1, 403);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:companies,name,' . $company->id,
            'subdomain' => 'nullable|string|max:255|unique:companies,subdomain,' . $company->id . '|alpha_dash',
            'status' => 'required|integer|in:0,1',
        ]);
        
        $company->update($validated);

        return redirect()->route('admin.companies.show', $company)
            ->with('success', 'Company updated successfully.');
    }

    /**
     * Remove the specified company.
     */
    public function destroy(Company $company)
    {
        abort_unless(auth()->user()->u_type == 1, 403);
        
        // Prevent deletion if company has data
        if ($company->users()->count() > 0 || 
            $company->projects()->count() > 0 || 
            $company->purchaseOrders()->count() > 0) {
            return back()->with('error', 'Cannot delete company with existing users, projects, or purchase orders.');
        }

        $company->delete();

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company deleted successfully.');
    }

    /**
     * Switch current company context (super admin only).
     */
    public function switch(Company $company)
    {
        abort_unless(auth()->user()->u_type == 1, 403, 'Only super admins can switch companies');
        
        Session::put('company_id', $company->id);
        Session::put('company_name', $company->name);

        return redirect()->route('admin.dashboard')->with('success', "Switched to {$company->name}");
    }
}
