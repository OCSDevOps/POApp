<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierUser;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show the supplier login form.
     */
    public function showLoginForm()
    {
        return view('supplier.auth.login');
    }

    /**
     * Handle a supplier login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'status' => 1,
        ];

        if (Auth::guard('supplier')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $supplierUser = Auth::guard('supplier')->user();

            // Set tenant context for supplier
            if ($supplierUser->company_id) {
                session(['company_id' => $supplierUser->company_id]);
            } elseif ($supplierUser->supplier && $supplierUser->supplier->company_id) {
                session(['company_id' => $supplierUser->supplier->company_id]);
            }

            return redirect()->intended(route('supplier.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->withInput($request->only('email', 'remember'));
    }

    /**
     * Show the supplier registration form.
     */
    public function showRegisterForm()
    {
        return view('supplier.auth.register');
    }

    /**
     * Handle supplier registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:supplier_users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:50',
            'supplier_id' => 'nullable|integer',
        ]);

        $companyId = null;

        // If supplier exists, inherit its company_id when possible
        if ($request->filled('supplier_id')) {
            $supplier = Supplier::withoutGlobalScopes()->find($request->supplier_id);
            $companyId = $supplier->company_id ?? null;
        } elseif (session()->has('company_id')) {
            $companyId = session('company_id');
        }

        $supplierUser = SupplierUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'supplier_id' => $request->supplier_id,
            'company_id' => $companyId,
            'status' => 1,
        ]);

        event(new Registered($supplierUser));

        Auth::guard('supplier')->login($supplierUser);

        return redirect()->route('supplier.verification.notice');
    }

    /**
     * Log the supplier out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('supplier')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('supplier.login')->with('status', 'You have been logged out.');
    }

    /**
     * Show the password reset request form.
     */
    public function showForgotForm()
    {
        return view('supplier.auth.forgot');
    }

    /**
     * Send a password reset link to the supplier.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('supplier_users')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm(Request $request, string $token = null)
    {
        return view('supplier.auth.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Handle resetting the supplier user's password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::broker('supplier_users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                Auth::guard('supplier')->login($user);
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('supplier.dashboard')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Send a fresh email verification link.
     */
    public function sendVerificationEmail(Request $request)
    {
        $user = $request->user('supplier');

        if ($user && $user->hasVerifiedEmail()) {
            return redirect()->route('supplier.dashboard');
        }

        if ($user) {
            $user->sendEmailVerificationNotification();
        }

        return back()->with('status', 'Verification link sent!');
    }

    /**
     * Verify the supplier's email.
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = Auth::guard('supplier')->user();

        if (! $user || $user->getKey() != $id) {
            return redirect()->route('supplier.login');
        }

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('supplier.verification.notice')->withErrors([
                'email' => 'Invalid verification link.',
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('supplier.dashboard');
        }

        $user->markEmailAsVerified();

        return redirect()->route('supplier.dashboard')->with('status', 'Email verified!');
    }
}
