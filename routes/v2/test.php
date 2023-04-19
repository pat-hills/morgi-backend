<?php

use App\Http\Controllers\TestUserController;
use Illuminate\Support\Facades\Route;

if (env('APP_ENV') !== 'prod' && env('APP_ENV') !== 'production') {
    Route::group(['prefix' => 'test'], function () {
        Route::get('/users', [TestUserController::class, 'create']);
    });
}
