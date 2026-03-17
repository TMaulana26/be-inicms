<?php

use Illuminate\Support\Facades\Route;
use Modules\Menu\Http\Controllers\MenuController;
use Modules\Menu\Http\Controllers\MenuItemController;

Route::middleware('auth:sanctum')->group(function () {
    // Menu Routes
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

    // Menu Item Routes
    Route::prefix('menu-items')->group(function () {
        // 1. Bulk
        Route::post('/bulk-destroy', [MenuItemController::class, 'bulkDestroy']);
        Route::post('/bulk-force-delete', [MenuItemController::class, 'bulkForceDelete']);
        Route::patch('/bulk-restore', [MenuItemController::class, 'bulkRestore']);
        Route::patch('/bulk-toggle-status', [MenuItemController::class, 'bulkToggleStatus']);

        // 2. Special single-item routes
        Route::patch('/{id}/restore', [MenuItemController::class, 'restore']);
        Route::delete('/{id}/force-delete', [MenuItemController::class, 'forceDelete']);
        Route::patch('/{menu_item}/toggle-status', [MenuItemController::class, 'toggleStatus']);

        // 3. Standard CRUD
        Route::get('/', [MenuItemController::class, 'index']);
        Route::post('/', [MenuItemController::class, 'store']);
        Route::get('/{menu_item}', [MenuItemController::class, 'show']);
        Route::match(['put', 'patch'], '/{menu_item}', [MenuItemController::class, 'update']);
        Route::delete('/{menu_item}', [MenuItemController::class, 'destroy']);
    });
});
