<?php

use App\Http\Controllers\RegionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'regions'], function () {
    Route::get('/', [RegionController::class, 'index']);
});
