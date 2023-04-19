<?php

use App\Http\Controllers\CityController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'cities'], function () {
    Route::get('/', [CityController::class, 'index']);
});
