<?php

use Illuminate\Support\Facades\Route;
use Modules\Portfolio\Http\Controllers\ProjectController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('projects-web', ProjectController::class)->names('projects-web');
});
