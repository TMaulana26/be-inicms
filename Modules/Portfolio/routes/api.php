<?php

use Illuminate\Support\Facades\Route;
use Modules\Portfolio\Http\Controllers\ProjectController;

// Public Routes
Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(ProjectController::class)->prefix('projects')->group(function () {
        // Bulk Actions
        Route::prefix('bulk')->group(function () {
            Route::post('delete', 'bulkDestroy')->name('projects.bulk-delete');
            Route::patch('restore', 'bulkRestore')->name('projects.bulk-restore');
            Route::patch('toggle-status', 'bulkToggleStatus')->name('projects.bulk-toggle-status');
            Route::post('force-delete', 'bulkForceDelete')->name('projects.bulk-force-delete');
        });

        // Single Actions
        Route::patch('{id}/restore', 'restore')->name('projects.restore');
        Route::delete('{id}/force-delete', 'forceDelete')->name('projects.force-delete');
        Route::patch('{id}/toggle-status', 'toggleStatus')->name('projects.toggle-status');
    });

    Route::apiResource('projects', ProjectController::class)->except(['index', 'show']);
});
