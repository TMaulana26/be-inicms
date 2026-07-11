<?php

use Illuminate\Support\Facades\Route;
use Modules\Contact\Http\Controllers\ContactMessageController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('contact-messages', ContactMessageController::class)->names('contact-messages');
});
