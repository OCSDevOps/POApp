<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // All methods require an authenticated supplier who has verified their email.
        $this->middleware(['auth:supplier', 'verified.supplier']);
    }

    /**
     * Show the supplier dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $supplierUser = Auth::guard('supplier')->user();
        // You can pass stats or other data to the dashboard here
        return view('supplier.dashboard', compact('supplierUser'));
    }

    /**
     * Show the supplier profile page.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        $supplierUser = Auth::guard('supplier')->user();
        return view('supplier.profile', compact('supplierUser'));
    }

    /**
     * Update the supplier's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::guard('supplier')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('supplier_users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update(array_filter($validated, fn($value) => !is_null($value) && $value !== ''));

        return redirect()->route('supplier.profile')->with('success', 'Profile updated successfully.');
    }
}