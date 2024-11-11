<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentProviderController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\AuthorizationMiddleware;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
});

// Payment Callback
Route::post('payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

Route::middleware(JwtMiddleware::class)->group(function () {
    // User
    Route::get('me', [UserController::class, 'me'])->name('users.me');
    Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');

    // Payment
    Route::post('payment', [PaymentController::class, 'store'])->name('payment.store');

    // Transaction
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Payment Provider
    Route::get('payment-providers', [PaymentProviderController::class, 'index'])->name('payment-providers.index');

    Route::middleware(AuthorizationMiddleware::class)->group(function () {
        // Payment Provider
        Route::get('payment-providers/{id}', [PaymentProviderController::class, 'show'])->name('payment-providers.show');
        Route::put('payment-providers/{id}/change-pos-status', [PaymentProviderController::class, 'changePosStatus'])->name('payment-providers.change-pos-status');
    });
});
