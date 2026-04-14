<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\CategoryController;

Route::apiResource('categories', CategoryController::class);

Route::prefix('categories')->group(function () {
    // Bulk Operations
    Route::prefix('bulk')->group(function () {
        Route::post('delete', [CategoryController::class, 'bulkDestroy'])->name('categories.bulk-delete');
        Route::post('restore', [CategoryController::class, 'bulkRestore'])->name('categories.bulk-restore');
        Route::post('force-delete', [CategoryController::class, 'bulkForceDelete'])->name('categories.bulk-force-delete');
        Route::post('toggle-status', [CategoryController::class, 'bulkToggleStatus'])->name('categories.bulk-toggle-status');
    });

    Route::patch('{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::post('{category}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('{category}/force', [CategoryController::class, 'forceDelete'])->name('categories.force-delete');
});
