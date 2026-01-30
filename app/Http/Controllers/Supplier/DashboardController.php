<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    /**
     * Supplier dashboard landing page.
     */
    public function index()
    {
        return view('supplier.dashboard');
    }

    /**
     * Show supplier profile form.
     */
    public function profile()
    {
        return view('supplier.profile', [
            'supplierUser' => Auth::guard('supplier')->user(),
        ]);
    }

    /**
     * Update supplier profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::guard('supplier')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('status', 'Profile updated successfully.');
    }
}
