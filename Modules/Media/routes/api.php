<?php

use Illuminate\Support\Facades\Route;
use Modules\Media\Http\Controllers\MediaController;

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(MediaController::class)->prefix('media')->group(function () {
        Route::prefix('bulk')->group(function () {
            Route::patch('toggle-status', 'bulkToggleStatus')->name('media.bulk-toggle-status');
            Route::post('delete', 'bulkDestroy')->name('media.bulk-delete');
            Route::patch('restore', 'bulkRestore')->name('media.bulk-restore');
            Route::post('force-delete', 'bulkForceDelete')->name('media.bulk-force-delete');
        });

        Route::patch('{media}/toggle-status', 'toggleStatus')->name('media.toggle-status');
        Route::patch('{id}/restore', 'restore')->name('media.restore');
        Route::delete('{id}/force', 'forceDelete')->name('media.force-delete');
    });

    Route::apiResource('media', MediaController::class)->parameters(['media' => 'media']);
});
