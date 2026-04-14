<?php

use Illuminate\Support\Facades\Route;
use Modules\Page\Http\Controllers\PageController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('pages', PageController::class);

    Route::prefix('pages')->group(function () {
        // Bulk Operations
        Route::prefix('bulk')->group(function () {
            Route::post('delete', [PageController::class, 'bulkDestroy'])->name('pages.bulk-delete');
            Route::post('restore', [PageController::class, 'bulkRestore'])->name('pages.bulk-restore');
            Route::post('force-delete', [PageController::class, 'bulkForceDelete'])->name('pages.bulk-force-delete');
        });

        Route::post('{page}/restore', [PageController::class, 'restore'])->name('pages.restore');
        Route::delete('{page}/force', [PageController::class, 'forceDelete'])->name('pages.force-delete');
    });
});
