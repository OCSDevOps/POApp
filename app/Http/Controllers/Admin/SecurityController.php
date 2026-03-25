<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\Security\AuditLogService;
use App\Services\Security\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SecurityController extends Controller
{
    public function __construct(
        protected TotpService $totpService,
        protected AuditLogService $auditLogService
    ) {
    }

    /**
     * Show two-factor management settings.
     */
    public function twoFactorSettings(Request $request)
    {
        $user = $request->user();
        $pendingSecret = session('pending_two_factor_secret');
        $otpAuthUrl = null;

        if ($pendingSecret && $user) {
            $otpAuthUrl = $this->totpService->getOtpAuthUrl(
                config('app.name', 'POAPP'),
                $user->email ?? ('user-' . $user->id),
                $pendingSecret
            );
        }

        return view('admin.security.two_factor', compact('user', 'pendingSecret', 'otpAuthUrl'));
    }

    /**
     * Start two-factor setup by generating a pending secret.
     */
    public function generateTwoFactorSecret(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->hasTwoFactorEnabled()) {
            return back()->with('error', 'Two-factor authentication is already enabled.');
        }

        $secret = $this->totpService->generateSecret();
        session(['pending_two_factor_secret' => $secret]);

        $this->auditLogService->logEvent('auth.2fa_setup_initiated', $user);

        return back()->with('success', 'Authenticator secret generated. Scan or copy the secret, then confirm with a code.');
    }

    /**
     * Confirm and enable two-factor authentication.
     */
    public function confirmTwoFactor(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|min:6|max:6',
        ]);

        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $secret = session('pending_two_factor_secret');
        if (!$secret) {
            return back()->with('error', 'No pending two-factor setup found. Generate a secret first.');
        }

        if (!$this->totpService->verifyCode($secret, $request->otp_code)) {
            return back()->with('error', 'Invalid code. Please try again.');
        }

        DB::table('users')->where('id', $user->id)->update([
            'two_factor_enabled' => 1,
            'two_factor_secret' => Crypt::encryptString($secret),
            'two_factor_confirmed_at' => now(),
        ]);

        session()->forget('pending_two_factor_secret');
        $this->auditLogService->logEvent('auth.2fa_enabled', $user);

        return back()->with('success', 'Two-factor authentication enabled successfully.');
    }

    /**
     * Disable two-factor authentication.
     */
    public function disableTwoFactor(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Password verification failed.');
        }

        DB::table('users')->where('id', $user->id)->update([
            'two_factor_enabled' => 0,
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ]);

        session()->forget('pending_two_factor_secret');
        $this->auditLogService->logEvent('auth.2fa_disabled', $user);

        return back()->with('success', 'Two-factor authentication disabled.');
    }

    /**
     * Audit log viewer with lightweight filtering.
     */
    public function auditLogs(Request $request)
    {
        $query = AuditLog::query()->with('user');

        if (session('u_type') != 1 && session('company_id')) {
            $query->where('company_id', session('company_id'));
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', 'like', '%' . $request->event_type . '%');
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderByDesc('id')->paginate(50)->withQueryString();

        $users = User::query()
            ->orderBy('name')
            ->limit(300)
            ->get(['id', 'name', 'email']);

        return view('admin.security.audit_logs', [
            'logs' => $logs,
            'users' => $users,
            'filters' => $request->only(['event_type', 'user_id', 'date_from', 'date_to', 'company_id']),
        ]);
    }
}
