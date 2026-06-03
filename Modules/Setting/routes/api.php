<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\SettingController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('settings', SettingController::class);
    Route::controller(SettingController::class)->prefix('settings')->group(function () {
        Route::get('grouped', 'grouped')->name('settings.grouped');
        Route::post('bulk-update', 'updateBulk')->name('settings.bulk-update');
        Route::patch('{setting}/toggle-status', 'toggleStatus')->name('settings.toggle-status');
        Route::patch('{id}/restore', 'restore')->name('settings.restore');
        Route::delete('{id}/force-delete', 'forceDelete')->name('settings.force-delete');

        Route::prefix('bulk')->group(function () {
            Route::patch('toggle-status', 'bulkToggleStatus')->name('settings.bulk-toggle-status');
            Route::post('delete', 'bulkDestroy')->name('settings.bulk-delete');
            Route::patch('restore', 'bulkRestore')->name('settings.bulk-restore');
            Route::post('force-delete', 'bulkForceDelete')->name('settings.bulk-force-delete');
        });
    });
});
