<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\StatsController;

Route::get('/test', function () {
    return ['status' => 'OK', 'message' => 'API is running successfully!'];
});

// Public Auth Routes
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/forgot-password', 'forgotPassword');
    Route::post('/reset-password', 'resetPassword');
    Route::post('/email/verify', 'verifyEmail')->name('verification.verify');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

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

    // User Routes
    Route::controller(UserController::class)->prefix('users')->group(function () {
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
    Route::apiResource('users', UserController::class);

    // Role Routes
    Route::controller(RoleController::class)->prefix('roles')->group(function () {
        Route::patch('{role}/toggle-status', 'toggleStatus');
        Route::patch('{id}/restore', 'restore');
        Route::delete('{id}/force-delete', 'forceDelete');
        Route::patch('bulk-toggle-status', 'bulkToggleStatus');
        Route::post('bulk-destroy', 'bulkDestroy');
        Route::patch('bulk-restore', 'bulkRestore');
        Route::post('bulk-force-delete', 'bulkForceDelete');
        Route::post('{role}/sync-permissions', 'syncPermissions');
        Route::post('{role}/give-permissions', 'givePermissions');
        Route::post('{role}/revoke-permissions', 'revokePermissions');
    });
    Route::apiResource('roles', RoleController::class);

    // Permission Routes
    Route::controller(PermissionController::class)->prefix('permissions')->group(function () {
        Route::patch('{permission}/toggle-status', 'toggleStatus');
        Route::patch('{id}/restore', 'restore');
        Route::delete('{id}/force-delete', 'forceDelete');
        Route::patch('bulk-toggle-status', 'bulkToggleStatus');
        Route::post('bulk-destroy', 'bulkDestroy');
        Route::patch('bulk-restore', 'bulkRestore');
        Route::post('bulk-force-delete', 'bulkForceDelete');
        Route::post('{permission}/sync-roles', 'syncRoles');
        Route::post('{permission}/assign-roles', 'assignRoles');
        Route::post('{permission}/remove-roles', 'removeRoles');
    });
    Route::apiResource('permissions', PermissionController::class);

    // Media Routes
    Route::controller(MediaController::class)->prefix('media')->group(function () {
        Route::patch('{media}/toggle-status', 'toggleStatus');
        Route::patch('{id}/restore', 'restore');
        Route::delete('{id}/force', 'forceDelete');
        Route::patch('bulk-toggle-status', 'bulkToggleStatus');
        Route::post('bulk-destroy', 'bulkDestroy');
        Route::patch('bulk-restore', 'bulkRestore');
        Route::post('bulk-force-delete', 'bulkForceDelete');
    });
    Route::apiResource('media', MediaController::class)->parameters(['media' => 'media']);

    // Stats Routes
    Route::get('stats', [StatsController::class, 'index']);
});

