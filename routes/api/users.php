<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::patch('{user}/toggle-status', 'toggleStatus');
        Route::patch('{id}/restore', 'restore');
        Route::delete('{id}/force-delete', 'forceDelete');
        Route::patch('bulk-toggle-status', 'bulkToggleStatus');
        Route::post('bulk-destroy', 'bulkDestroy');
        Route::patch('bulk-restore', 'bulkRestore');
        Route::post('bulk-force-delete', 'bulkForceDelete');
        Route::post('{user}/sync-roles', 'syncRoles');
        Route::post('{user}/assign-roles', 'assignRoles');
        Route::post('{user}/remove-roles', 'removeRoles');
    });
    Route::apiResource('users', UserController::class);
});
