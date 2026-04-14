<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\BlogController;

Route::middleware(['auth:sanctum'])->group(function () {
    require __DIR__ . '/categories.php';
    require __DIR__ . '/posts.php';
});
