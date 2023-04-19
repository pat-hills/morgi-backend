<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\UserBlockController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;

Route::group(['prefix' => 'users'], function () use ($middleware) {
    Route::group(['middleware' => $middleware], function () {
        Route::get('/avatars', [UserController::class, 'getRandomAvatars']);
        Route::group(['prefix' => '/{user}'], function () {
            Route::post('/block', [UserBlockController::class, 'store']);
            Route::delete('/block', [UserBlockController::class, 'delete']);
        });
    });
});

Route::post('discord/auth', [UserController::class, 'discordAuth']);
