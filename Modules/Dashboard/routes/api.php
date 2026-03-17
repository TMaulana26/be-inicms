<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Http\Controllers\StatsController;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('stats', [StatsController::class, 'index']);
});
