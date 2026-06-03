<?php

use Illuminate\Support\Facades\Route;
use Modules\Page\Http\Controllers\PageController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(PageController::class)->prefix('pages')->group(function () {
        Route::prefix('bulk')->group(function () {
            Route::post('delete', 'bulkDestroy')->name('pages.bulk-delete');
            Route::post('restore', 'bulkRestore')->name('pages.bulk-restore');
            Route::post('force-delete', 'bulkForceDelete')->name('pages.bulk-force-delete');
        });

        Route::post('{page}/restore', 'restore')->name('pages.restore');
        Route::delete('{page}/force', 'forceDelete')->name('pages.force-delete');
    });

    Route::apiResource('pages', PageController::class);
});
