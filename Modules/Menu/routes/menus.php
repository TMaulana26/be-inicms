<?php

use Illuminate\Support\Facades\Route;
use Modules\Menu\Http\Controllers\MenuController;

Route::middleware('auth:sanctum')->group(function () {
    // Menu Routes (Handles everything now)
    Route::prefix('menus')->group(function () {
        // 1. Bulk
        Route::post('/bulk-destroy', [MenuController::class, 'bulkDestroy']);
        Route::post('/bulk-force-delete', [MenuController::class, 'bulkForceDelete']);
        Route::patch('/bulk-restore', [MenuController::class, 'bulkRestore']);
        Route::patch('/bulk-toggle-status', [MenuController::class, 'bulkToggleStatus']);

        // 2. Special single-item routes
        Route::patch('/{id}/restore', [MenuController::class, 'restore']);
        Route::delete('/{id}/force-delete', [MenuController::class, 'forceDelete']);
        Route::patch('/{menu}/toggle-status', [MenuController::class, 'toggleStatus']);

        // 3. Standard CRUD
        Route::get('/', [MenuController::class, 'index']);
        Route::post('/', [MenuController::class, 'store']);
        Route::get('/{menu}', [MenuController::class, 'show']);
        Route::match(['put', 'patch'], '/{menu}', [MenuController::class, 'update']);
        Route::delete('/{menu}', [MenuController::class, 'destroy']);
    });
});
