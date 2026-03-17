<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {

    Route::get('/test', function () {
        return ['status' => 'OK', 'message' => 'API is running successfully!'];
    });

});
