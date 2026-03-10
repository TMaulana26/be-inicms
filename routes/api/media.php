<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(MediaController::class)->prefix('media')->group(function () {
        Route::patch('{media}/toggle-status', 'toggleStatus');
        Route::patch('{id}/restore', 'restore');
        Route::delete('{id}/force', 'forceDelete');
        Route::patch('bulk-toggle-status', 'bulkToggleStatus');
        Route::post('bulk-destroy', 'bulkDestroy');
        Route::patch('bulk-restore', 'bulkRestore');
        Route::post('bulk-force-delete', 'bulkForceDelete');
    });
    Route::apiResource('media', MediaController::class)->parameters(['media' => 'media']);
});
