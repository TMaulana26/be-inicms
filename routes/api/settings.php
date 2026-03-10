<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController;

Route::middleware('auth:sanctum')->prefix('settings')->group(function () {
    // 1. Bulk & Custom Routes
    Route::get('/grouped', [SettingController::class, 'grouped']);
    Route::post('/bulk', [SettingController::class, 'updateBulk']);
    Route::post('/bulk-destroy', [SettingController::class, 'bulkDestroy']);
    Route::post('/bulk-force-delete', [SettingController::class, 'bulkForceDelete']);
    Route::patch('/bulk-restore', [SettingController::class, 'bulkRestore']);
    Route::patch('/bulk-toggle-status', [SettingController::class, 'bulkToggleStatus']);

    // 2. Special single-item routes
    Route::patch('/{id}/restore', [SettingController::class, 'restore']);
    Route::delete('/{id}/force-delete', [SettingController::class, 'forceDelete']);
    Route::patch('/{setting}/toggle-status', [SettingController::class, 'toggleStatus']);

    // 3. Standard CRUD
    Route::get('/', [SettingController::class, 'index']);
    Route::get('/{setting}', [SettingController::class, 'show']);
    Route::delete('/{setting}', [SettingController::class, 'destroy']);
});
