<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\SupplierUser;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Supplier Auth Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the authentication of supplier users including
    | registration, login, password reset, and email verification.
    | It uses a custom guard 'supplier' to keep them separate from staff users.
    |
    */

    use AuthenticatesUsers, RegistersUsers, SendsPasswordResetEmails, ResetsPasswords, VerifiesEmails;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = route('supplier.dashboard');
        $this->middleware('guest:supplier')->except([
            'logout', 'showVerificationNotice', 'verifyEmail', 'sendVerificationEmail'
        ]);
    }

    // --- OVERRIDE TRAIT METHODS FOR 'supplier' GUARD ---

    protected function guard()
    {
        return Auth::guard('supplier');
    }

    public function showLoginForm()
    {
        return view('supplier.auth.login');
    }

    public function showRegistrationForm()
    {
        return view('supplier.auth.register');
    }

    protected function validator(array $data)
    {
        // Note: You must have a way to associate a supplier user with a company_id
        // and a supplier_id. This might come from an invitation token or a selection
        // on the registration form. This example assumes it's provided in the request.
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:supplier_users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'company_id' => ['required', 'exists:companies,id'],
            'supplier_id' => ['required', 'exists:supplier_master,sup_id'],
        ]);
    }

    protected function create(array $data)
    {
        return SupplierUser::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'company_id' => $data['company_id'],
            'supplier_id' => $data['supplier_id'],
            'status' => 1, // Active by default
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 201)
                    : redirect($this->redirectPath());
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('supplier.login');
    }

    protected function credentials(Request $request)
    {
        // Add status check to login credentials to prevent disabled users from logging in.
        return array_merge($request->only($this->username(), 'password'), ['status' => 1]);
    }

    // --- Password Reset ---

    public function showForgotForm()
    {
        return view('supplier.auth.passwords.email');
    }

    public function sendResetLink(Request $request)
    {
        return $this->sendResetLinkEmail($request);
    }

    public function broker()
    {
        return Password::broker('supplier_users');
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('supplier.auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    // --- Email Verification ---

    public function showVerificationNotice(Request $request)
    {
        return $request->user('supplier')->hasVerifiedEmail()
                        ? redirect($this->redirectPath())
                        : view('supplier.auth.verify-email');
    }

    public function verifyEmail(Request $request)
    {
        if ($request->route('id') != $request->user('supplier')->getKey()) {
            throw new \Illuminate\Auth\Access\AuthorizationException;
        }

        if ($request->user('supplier')->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        if ($request->user('supplier')->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($request->user('supplier')));
        }

        return redirect($this->redirectPath())->with('verified', true);
    }

    public function sendVerificationEmail(Request $request)
    {
        if ($request->user('supplier')->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        $request->user('supplier')->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }
}