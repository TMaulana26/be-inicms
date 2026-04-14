<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Http\Controllers\StatsController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('stats', [StatsController::class, 'index']);
});
