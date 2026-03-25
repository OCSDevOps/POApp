<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Services\Security\AuditLogService;
use App\Services\Security\TotpService;

class AuthController extends Controller
{
    public function __construct(
        protected TotpService $totpService,
        protected AuditLogService $auditLogService
    ) {
    }

    public function index()
    {
        session()->forget(['two_factor_pending_user_id', 'two_factor_remember']);
        return view('auth/login');
    }

    public function validate_login(Request $req)
    {
        $req->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $req->only('email', 'password');
        $remember = $req->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            /** @var User $user */
            $user = Auth::user();

            if ($user && $user->hasTwoFactorEnabled()) {
                Auth::logout();

                session([
                    'two_factor_pending_user_id' => $user->id,
                    'two_factor_remember' => $remember,
                ]);

                $this->auditLogService->logEvent('auth.login_password_verified', $user, [], [], [
                    'requires_2fa' => true,
                ]);

                return redirect()->route('auth.2fa.challenge');
            }

            if ($user) {
                $this->finalizeAuthenticatedSession($user);
                $this->auditLogService->logEvent('auth.login_success', $user, [], [], [
                    'via_2fa' => false,
                ]);
            }

            return redirect()->route('admin.dashboard');
        }

        $this->auditLogService->logEvent('auth.login_failed', null, [], [], [
            'email' => $req->input('email'),
        ]);

        return redirect('/')->with('invalid-user','true');
    }

    public function showTwoFactorChallenge(Request $request)
    {
        if (!$request->session()->has('two_factor_pending_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two_factor_challenge');
    }

    public function verifyTwoFactorChallenge(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|min:6|max:6',
        ]);

        $pendingUserId = session('two_factor_pending_user_id');
        if (!$pendingUserId) {
            return redirect()->route('login');
        }

        /** @var User|null $user */
        $user = User::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->find($pendingUserId);
        if (!$user || empty($user->two_factor_secret)) {
            session()->forget(['two_factor_pending_user_id', 'two_factor_remember']);
            return redirect('/')->with('invalid-user', 'true');
        }

        $secret = $user->two_factor_secret;
        try {
            $secret = Crypt::decryptString($secret);
        } catch (\Throwable $e) {
            // Backward compatibility in case old plaintext secrets exist.
        }

        if (!$this->totpService->verifyCode($secret, $request->otp_code)) {
            $this->auditLogService->logEvent('auth.2fa_failed', $user);
            return back()->withErrors([
                'otp_code' => 'Invalid authentication code.',
            ]);
        }

        $remember = (bool) session('two_factor_remember', false);
        Auth::login($user, $remember);
        session()->forget(['two_factor_pending_user_id', 'two_factor_remember']);

        $this->finalizeAuthenticatedSession($user);
        $this->auditLogService->logEvent('auth.login_success', $user, [], [], [
            'via_2fa' => true,
        ]);

        return redirect()->route('admin.dashboard');
    }

    public function generate_hash()
    {
        $hashedPassword = Hash::make('admin@123');
        echo $hashedPassword;
    }

    public function logout()
    {
        if (Auth::check()) {
            $this->auditLogService->logEvent('auth.logout', Auth::user());
        }

        session()->forget(['two_factor_pending_user_id', 'two_factor_remember']);
        Session::flush();
        Auth::logout();

        return redirect('/')->with('logout','yes');
    }

    /**
     * Store login context in session and persist last login details.
     */
    private function finalizeAuthenticatedSession(User $user): void
    {
        session([
            'name' => $user->name,
            'company_id' => $user->company_id,
            'company_name' => $user->company->name ?? 'Default Company',
            'u_type' => $user->u_type ?? null,
            'pt_id' => $user->pt_id ?? null,
        ]);

        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);
    }
}
