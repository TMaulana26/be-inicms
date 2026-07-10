<?php

use Illuminate\Support\Facades\Route;
use Modules\Contact\Http\Controllers\ContactMessageController;

// Public Route
Route::post('contact-messages', [ContactMessageController::class, 'store'])->name('contact-messages.store');

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(ContactMessageController::class)->prefix('contact-messages')->group(function () {
        // Bulk actions
        Route::prefix('bulk')->group(function () {
            Route::post('delete', 'bulkDestroy')->name('contact-messages.bulk-delete');
            Route::patch('restore', 'bulkRestore')->name('contact-messages.bulk-restore');
            Route::patch('toggle-status', 'bulkToggleStatus')->name('contact-messages.bulk-toggle-status');
            Route::patch('toggle-read', 'bulkToggleRead')->name('contact-messages.bulk-toggle-read');
            Route::post('force-delete', 'bulkForceDelete')->name('contact-messages.bulk-force-delete');
        });

        // Single actions
        Route::patch('{id}/restore', 'restore')->name('contact-messages.restore');
        Route::delete('{id}/force-delete', 'forceDelete')->name('contact-messages.force-delete');
        Route::patch('{id}/toggle-status', 'toggleStatus')->name('contact-messages.toggle-status');
        Route::patch('{id}/toggle-read', 'toggleRead')->name('contact-messages.toggle-read');
    });

    Route::apiResource('contact-messages', ContactMessageController::class)->except(['store']);
});
