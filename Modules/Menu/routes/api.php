<?php

use Illuminate\Support\Facades\Route;
use Modules\Menu\Http\Controllers\MenuController;

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(MenuController::class)->prefix('menus')->group(function () {
        Route::prefix('bulk')->group(function () {
            Route::patch('toggle-status', 'bulkToggleStatus')->name('menus.bulk-toggle-status');
            Route::post('delete', 'bulkDestroy')->name('menus.bulk-delete');
            Route::patch('restore', 'bulkRestore')->name('menus.bulk-restore');
            Route::post('force-delete', 'bulkForceDelete')->name('menus.bulk-force-delete');
        });

        Route::patch('{menu}/toggle-status', 'toggleStatus')->name('menus.toggle-status');
        Route::patch('{id}/restore', 'restore')->name('menus.restore');
        Route::delete('{id}/force-delete', 'forceDelete')->name('menus.force-delete');
    });

    Route::apiResource('menus', MenuController::class);
});
