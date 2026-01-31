<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    //
    function index(){
        return view('auth/login');
    }

    function validate_login(Request $req){
        $req->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $credentials = $req->only('email','password');

        if(Auth::attempt($credentials)){
            $user = Auth::user();
            session(['name' => $user->name]);
            session(['company_id' => $user->company_id]);
            session(['company_name' => $user->company->name ?? 'Default Company']);
            return redirect('dashboard');
        }

        return redirect('/')->with('invalid-user','true');
    }

    function generate_hash(){
        $hashedPassword = Hash::make('admin@123');
        echo $hashedPassword;
    }

    function logout(){
        Session::flush();
        Auth::logout();

        return redirect('/')->with('logout','yes');
    }
}
