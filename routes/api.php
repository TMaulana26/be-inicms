<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatsController;

Route::prefix('v1')->group(function () {

    Route::get('/test', function () {
        return ['status' => 'OK', 'message' => 'API is running successfully!'];
    });

    // Modular Routes
    require __DIR__ . '/api/auth.php';
    require __DIR__ . '/api/users.php';
    require __DIR__ . '/api/rbac.php';
    require __DIR__ . '/api/media.php';
    require __DIR__ . '/api/settings.php';
    require __DIR__ . '/api/menus.php';

    // Stats Routes
    Route::middleware('auth:sanctum')->get('stats', [StatsController::class, 'index']);

});
