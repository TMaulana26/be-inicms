<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    require __DIR__ . '/users.php';
    require __DIR__ . '/rbac.php';
});
