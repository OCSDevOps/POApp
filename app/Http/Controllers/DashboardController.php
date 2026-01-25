<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    public function index(){
        if(Auth::check()){
            return view('profile_pages/dashboard');
        }

        return redirect('/')->with('error-login','Dashboard');
    }
    public function dashboard_analytics(){
        if(Auth::check()){
            return view('profile_pages/dashboard-analytics');
        }

        return redirect('/')->with('error-login','Dashboard Analytics');
    }
    public function dashboard_ecommerce(){
        if(Auth::check()){
            return view('profile_pages/dashboard-ecommerce');
        }

        return redirect('/')->with('error-login','Dashboard Ecommerce');
    }
    public function dashboard_projects(){
        if(Auth::check()){
            return view('profile_pages/dashboard-projects');
        }

        return redirect('/')->with('error-login','Dashboard Projects');
    }
}
