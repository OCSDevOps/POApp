<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantManagementController extends Controller
{
    /**
     * Display list of all companies (super admin only)
     */
    public function index()
    {
        $this->authorizeSuperAdmin();
        
        $companies = Company::withCount([
            'users',
            'projects',
            'purchaseOrders',
        ])
        ->orderBy('name')
        ->paginate(20);
        
        return view('admin.tenants.index', compact('companies'));
    }
    
    /**
     * Show form to create new company
     */
    public function create()
    {
        $this->authorizeSuperAdmin();
        
        return view('admin.tenants.form');
    }
    
    /**
     * Store new company
     */
    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:companies',
            'subdomain' => 'nullable|string|max:50|unique:companies|regex:/^[a-z0-9-]+$/',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:50',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:8',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create company
            $company = Company::create([
                'name' => $validated['name'],
                'subdomain' => $validated['subdomain'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'zip' => $validated['zip'],
                'country' => $validated['country'] ?? 'USA',
                'status' => 1,
                'settings' => [
                    'currency' => 'USD',
                    'timezone' => 'America/New_York',
                    'date_format' => 'm/d/Y',
                ],
            ]);
            
            // Create admin user for the company
            $adminUser = User::create([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'username' => Str::before($validated['admin_email'], '@'),
                'password' => Hash::make($validated['admin_password']),
                'company_id' => $company->id,
                'u_type' => 1, // Admin type
                'u_status' => 1,
            ]);
            
            DB::commit();
            
            return redirect()
                ->route('admin.tenants.index')
                ->with('success', "Company '{$company->name}' created successfully with admin user.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create company: ' . $e->getMessage());
        }
    }
    
    /**
     * Show company details
     */
    public function show($id)
    {
        $this->authorizeSuperAdmin();
        
        $company = Company::with([
            'users',
            'projects' => function($q) { $q->limit(10); },
        ])
        ->withCount([
            'users',
            'projects',
            'purchaseOrders',
            'suppliers',
            'items',
        ])
        ->findOrFail($id);
        
        return view('admin.tenants.show', compact('company'));
    }
    
    /**
     * Show edit form
     */
    public function edit($id)
    {
        $this->authorizeSuperAdmin();
        
        $company = Company::findOrFail($id);
        
        return view('admin.tenants.form', compact('company'));
    }
    
    /**
     * Update company
     */
    public function update(Request $request, $id)
    {
        $this->authorizeSuperAdmin();
        
        $company = Company::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:companies,name,' . $id,
            'subdomain' => 'nullable|string|max:50|regex:/^[a-z0-9-]+$/|unique:companies,subdomain,' . $id,
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:50',
            'status' => 'required|in:0,1',
        ]);
        
        $company->update($validated);
        
        return redirect()
            ->route('admin.tenants.index')
            ->with('success', "Company '{$company->name}' updated successfully.");
    }
    
    /**
     * Soft delete company (disable)
     */
    public function destroy($id)
    {
        $this->authorizeSuperAdmin();
        
        $company = Company::findOrFail($id);
        
        // Prevent deleting the current company
        if ($company->id == session('company_id')) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete the company you are currently logged into.');
        }
        
        $company->update(['status' => 0]);
        
        return redirect()
            ->route('admin.tenants.index')
            ->with('success', "Company '{$company->name}' has been disabled.");
    }
    
    /**
     * Switch to a different company (super admin only)
     */
    public function switch($id)
    {
        $this->authorizeSuperAdmin();
        
        $company = Company::findOrFail($id);
        
        if ($company->status !== 1) {
            return redirect()
                ->back()
                ->with('error', 'Cannot switch to an inactive company.');
        }
        
        session(['company_id' => $company->id]);
        session(['company_name' => $company->name]);
        
        return redirect()
            ->route('admin.dashboard')
            ->with('success', "Switched to company: {$company->name}");
    }
    
    /**
     * Show company settings form
     */
    public function settings($id)
    {
        $this->authorizeSuperAdmin();
        
        $company = Company::findOrFail($id);
        
        return view('admin.tenants.settings', compact('company'));
    }
    
    /**
     * Update company settings
     */
    public function updateSettings(Request $request, $id)
    {
        $this->authorizeSuperAdmin();
        
        $company = Company::findOrFail($id);
        
        $validated = $request->validate([
            'currency' => 'required|string|size:3',
            'timezone' => 'required|string|max:50',
            'date_format' => 'required|string|max:20',
            'fiscal_year_start' => 'nullable|date',
        ]);
        
        $company->settings = array_merge($company->settings ?? [], $validated);
        $company->save();
        
        return redirect()
            ->back()
            ->with('success', 'Company settings updated successfully.');
    }
    
    /**
     * Authorize super admin access
     */
    protected function authorizeSuperAdmin()
    {
        // For now, check if user type is 1 (super admin)
        // You can replace this with proper authorization logic
        $user = auth()->user();
        
        if (!$user || $user->u_type !== 1) {
            abort(403, 'Access denied. Super admin only.');
        }
    }
}
