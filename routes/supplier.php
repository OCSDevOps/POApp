<?php

use App\Http\Controllers\Supplier\AuthController;
use App\Http\Controllers\Supplier\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('supplier')->name('supplier.')->group(function () {

    // Guest routes (no supplier authentication)
    Route::middleware('guest:supplier')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.submit');

        Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('register', [AuthController::class, 'register'])->name('register.submit');

        Route::get('password/forgot', [AuthController::class, 'showForgotForm'])->name('password.request');
        Route::post('password/email', [AuthController::class, 'sendResetLink'])->name('password.email');
        Route::get('password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
        Route::post('password/reset', [AuthController::class, 'reset'])->name('password.update');
    });

    // Authenticated supplier routes
    Route::middleware(['auth.supplier'])->group(function () {
        // Email verification
        Route::get('email/verify', function () {
            return view('supplier.auth.verify-email');
        })->name('verification.notice');

        Route::post('email/verification-notification', [AuthController::class, 'sendVerificationEmail'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        // Protected content (requires verified email)
        Route::middleware('verified.supplier')->group(function () {
            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('profile', [DashboardController::class, 'profile'])->name('profile');
            Route::post('profile', [DashboardController::class, 'updateProfile'])->name('profile.update');

            // Item Pricing
            Route::prefix('pricing')->name('pricing.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Supplier\ItemPricingController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Supplier\ItemPricingController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Supplier\ItemPricingController::class, 'store'])->name('store');
                Route::get('/import', [\App\Http\Controllers\Supplier\ItemPricingController::class, 'importForm'])->name('import');
                Route::post('/import', [\App\Http\Controllers\Supplier\ItemPricingController::class, 'import'])->name('import.store');
            });

            // RFQs
            Route::prefix('rfq')->name('rfq.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Supplier\RfqController::class, 'index'])->name('index');
                Route::get('/{rfq}', [\App\Http\Controllers\Supplier\RfqController::class, 'show'])->name('show');
                Route::post('/{rfq}/quote', [\App\Http\Controllers\Supplier\RfqController::class, 'submitQuote'])->name('quote');
            });
        });

        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });
});
