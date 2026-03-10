<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TwoFactorController;

// Public Auth Routes
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/forgot-password', 'forgotPassword');
    Route::post('/reset-password', 'resetPassword');
    Route::post('/email/verify', 'verifyEmail')->name('verification.verify');
});

Route::middleware('auth:sanctum')->group(function () {
    // Authenticated Auth Routes
    Route::controller(AuthController::class)->group(function () {
        Route::get('/me', 'me');
        Route::post('/logout', 'logout');
        Route::post('/email/verification-notification', 'resendVerificationEmail')->name('verification.send');
    });

    // 2FA Routes
    Route::prefix('2fa')->group(function () {
        Route::controller(TwoFactorController::class)->group(function () {
            Route::post('enable', 'enable');
            Route::post('confirm', 'confirm');
            Route::delete('disable', 'disable');
        });

        // 2FA Challenge (Requires the temporary '2fa' token ability)
        Route::middleware('ability:2fa')->post('challenge', [TwoFactorController::class, 'challenge']);
    });
});
