<?php

use Illuminate\Support\Facades\Route;
use Modules\Skill\Http\Controllers\SkillController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('skills', SkillController::class)->names('skill');
});
