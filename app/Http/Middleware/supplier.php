<?php

use App\Http\Controllers\Supplier\AuthController;
use App\Http\Controllers\Supplier\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Supplier Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('supplier')->name('supplier.')->group(function () {
    Route::middleware('guest:supplier')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login']);
        Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [AuthController::class, 'register']);
        Route::get('password/reset', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
        Route::post('password/reset', [AuthController::class, 'reset'])->name('password.update');
    });

    Route::middleware(['auth:supplier'])->group(function () {
        Route::get('email/verify', [AuthController::class, 'showVerificationNotice'])->name('verification.notice');
        Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verify'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
        Route::post('email/resend', [AuthController::class, 'resendVerificationEmail'])->middleware('throttle:6,1')->name('verification.resend');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::middleware('verified.supplier')->get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});