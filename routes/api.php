<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/v1/test', function () {
    return ['status' => 'OK', 'message' => 'API is running successfully!'];
});
