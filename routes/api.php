<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/test', function () {
    return ['status' => 'OK', 'message' => 'API is running successfully!'];
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Public Verification Route
Route::post('/email/verify', [AuthController::class, 'verifyEmail'])->name('verification.verify');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Auth Routes
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])->name('verification.send');

    // 2FA Setup & Disabling (Requires full authentication)
    Route::controller(\App\Http\Controllers\TwoFactorController::class)->prefix('2fa')->group(function () {
        Route::post('enable', 'enable');
        Route::post('confirm', 'confirm');
        Route::delete('disable', 'disable');
    });

    // 2FA Challenge (Requires the temporary '2fa' token ability)
    Route::middleware('ability:2fa')->post('/login/2fa-challenge', [\App\Http\Controllers\TwoFactorController::class, 'challenge']);

    // User Routes
    Route::controller(\App\Http\Controllers\UserController::class)->prefix('users')->group(function () {
        Route::patch('{user}/toggle-status', 'toggleStatus');
        Route::patch('{id}/restore', 'restore');
        Route::delete('{id}/force-delete', 'forceDelete');
        Route::patch('bulk-toggle-status', 'bulkToggleStatus');
        Route::post('bulk-destroy', 'bulkDestroy');
        Route::patch('bulk-restore', 'bulkRestore');
        Route::post('bulk-force-delete', 'bulkForceDelete');
        Route::post('{user}/sync-roles', 'syncRoles');
        Route::post('{user}/assign-roles', 'assignRoles');
        Route::post('{user}/remove-roles', 'removeRoles');
    });
    Route::apiResource('users', \App\Http\Controllers\UserController::class);

    // Role Routes
    Route::controller(\App\Http\Controllers\RoleController::class)->prefix('roles')->group(function () {
        Route::patch('{role}/toggle-status', 'toggleStatus');
        Route::patch('bulk-toggle-status', 'bulkToggleStatus');
    });
    Route::apiResource('roles', \App\Http\Controllers\RoleController::class);

    // Permission Routes
    Route::controller(\App\Http\Controllers\PermissionController::class)->prefix('permissions')->group(function () {
        Route::patch('{permission}/toggle-status', 'toggleStatus');
        Route::patch('bulk-toggle-status', 'bulkToggleStatus');
    });
    Route::apiResource('permissions', \App\Http\Controllers\PermissionController::class);

    // Media Routes
    Route::apiResource('media', \App\Http\Controllers\MediaController::class)->only(['index', 'store', 'destroy']);
});

