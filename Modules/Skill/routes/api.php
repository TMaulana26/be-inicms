<?php

use Illuminate\Support\Facades\Route;
use Modules\Skill\Http\Controllers\SkillController;

// Public Routes
Route::get('skills', [SkillController::class, 'index'])->name('skills.index');
Route::get('skills/{skill}', [SkillController::class, 'show'])->name('skills.show');

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(SkillController::class)->prefix('skills')->group(function () {
        // Bulk Actions
        Route::prefix('bulk')->group(function () {
            Route::post('delete', 'bulkDestroy')->name('skills.bulk-delete');
            Route::patch('restore', 'bulkRestore')->name('skills.bulk-restore');
            Route::patch('toggle-status', 'bulkToggleStatus')->name('skills.bulk-toggle-status');
            Route::post('force-delete', 'bulkForceDelete')->name('skills.bulk-force-delete');
        });

        // Single Actions
        Route::patch('{id}/restore', 'restore')->name('skills.restore');
        Route::delete('{id}/force-delete', 'forceDelete')->name('skills.force-delete');
        Route::patch('{id}/toggle-status', 'toggleStatus')->name('skills.toggle-status');
    });

    Route::apiResource('skills', SkillController::class)->except(['index', 'show']);
});
