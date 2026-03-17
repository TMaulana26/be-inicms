<?php

use Illuminate\Support\Facades\Route;
use Modules\Acl\Http\Controllers\RoleController;
use Modules\Acl\Http\Controllers\PermissionController;

Route::middleware('auth:sanctum')->group(function () {
    // Role Routes
    Route::controller(RoleController::class)->prefix('roles')->group(function () {
        Route::patch('{role}/toggle-status', 'toggleStatus');
        Route::patch('{id}/restore', 'restore');
        Route::delete('{id}/force-delete', 'forceDelete');
        Route::patch('bulk-toggle-status', 'bulkToggleStatus');
        Route::post('bulk-destroy', 'bulkDestroy');
        Route::patch('bulk-restore', 'bulkRestore');
        Route::post('bulk-force-delete', 'bulkForceDelete');
        Route::post('{role}/sync-permissions', 'syncPermissions');
        Route::post('{role}/give-permissions', 'givePermissions');
        Route::post('{role}/revoke-permissions', 'revokePermissions');
    });
    Route::apiResource('roles', RoleController::class);

    // Permission Routes
    Route::controller(PermissionController::class)->prefix('permissions')->group(function () {
        Route::patch('{permission}/toggle-status', 'toggleStatus');
        Route::patch('{id}/restore', 'restore');
        Route::delete('{id}/force-delete', 'forceDelete');
        Route::patch('bulk-toggle-status', 'bulkToggleStatus');
        Route::post('bulk-destroy', 'bulkDestroy');
        Route::patch('bulk-restore', 'bulkRestore');
        Route::post('bulk-force-delete', 'bulkForceDelete');
        Route::post('{permission}/sync-roles', 'syncRoles');
        Route::post('{permission}/assign-roles', 'assignRoles');
        Route::post('{permission}/remove-roles', 'removeRoles');
    });
    Route::apiResource('permissions', PermissionController::class);
});
