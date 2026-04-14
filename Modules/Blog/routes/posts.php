<?php

use Modules\Blog\Http\Controllers\PostController;

Route::apiResource('posts', PostController::class);

Route::prefix('posts')->group(function () {
    // Bulk Operations
    Route::prefix('bulk')->group(function () {
        Route::post('delete', [PostController::class, 'bulkDestroy'])->name('posts.bulk-delete');
        Route::post('restore', [PostController::class, 'bulkRestore'])->name('posts.bulk-restore');
        Route::post('force-delete', [PostController::class, 'bulkForceDelete'])->name('posts.bulk-force-delete');
    });

    Route::post('{post}/restore', [PostController::class, 'restore'])->name('posts.restore');
    Route::delete('{post}/force', [PostController::class, 'forceDelete'])->name('posts.force-delete');
});
