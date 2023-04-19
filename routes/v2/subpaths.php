<?php

use App\Http\Controllers\PathController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'subpaths'], function () {
    Route::get('/', [PathController::class, 'indexSubpath']);
});
