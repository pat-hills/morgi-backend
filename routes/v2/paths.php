<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\PathController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;

Route::group(['prefix' => 'paths'], function () use ($middleware) {
    Route::get('/', [PathController::class, 'index']);
    Route::group(['middleware' => $middleware], function () {
        Route::group(['middleware' => [MiddlewareEnum::MIDDLEWARE_IS_LEADER]], function () {
            Route::get('/unlocked', [PathController::class, 'getUnlockedPaths']);
            Route::get('/locked', [PathController::class, 'getLockedPaths']);
        });
    });
});
