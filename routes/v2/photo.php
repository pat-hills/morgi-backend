<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\PhotoController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;

Route::group(['prefix' => 'photo'], function () use ($middleware) {

    Route::post('/', [PhotoController::class, 'store']);

    Route::group(['middleware' => $middleware], function () {

        Route::get('/', [PhotoController::class, 'index']);
        Route::post('/assign', [PhotoController::class, 'assignPhoto']);

        Route::group(['prefix' => '{photo}'], function () {
            Route::post('/delete', [PhotoController::class, 'deletePhoto']);
        });

        Route::group(['prefix' => '{photoHistory}'], function () {
            Route::post('/delete/validation', [PhotoController::class, 'deleteValidationPhoto']);
        });
    });
});
