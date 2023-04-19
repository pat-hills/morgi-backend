<?php

use App\Http\Controllers\GenderController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'genders'], function () {
    Route::get('/', [GenderController::class, 'index']);
});
