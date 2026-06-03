<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\CategoryController;
use Modules\Blog\Http\Controllers\PostController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Categories
    Route::apiResource('categories', CategoryController::class);
    Route::controller(CategoryController::class)->prefix('categories')->group(function () {
        Route::prefix('bulk')->group(function () {
            Route::post('delete', 'bulkDestroy')->name('categories.bulk-delete');
            Route::post('restore', 'bulkRestore')->name('categories.bulk-restore');
            Route::post('force-delete', 'bulkForceDelete')->name('categories.bulk-force-delete');
            Route::post('toggle-status', 'bulkToggleStatus')->name('categories.bulk-toggle-status');
        });

        Route::patch('{category}/toggle-status', 'toggleStatus')->name('categories.toggle-status');
        Route::post('{category}/restore', 'restore')->name('categories.restore');
        Route::delete('{category}/force', 'forceDelete')->name('categories.force-delete');
    });

    // Posts
    Route::apiResource('posts', PostController::class);
    Route::controller(PostController::class)->prefix('posts')->group(function () {
        Route::prefix('bulk')->group(function () {
            Route::post('delete', 'bulkDestroy')->name('posts.bulk-delete');
            Route::post('restore', 'bulkRestore')->name('posts.bulk-restore');
            Route::post('force-delete', 'bulkForceDelete')->name('posts.bulk-force-delete');
        });

        Route::post('{post}/restore', 'restore')->name('posts.restore');
        Route::delete('{post}/force', 'forceDelete')->name('posts.force-delete');
    });
});
