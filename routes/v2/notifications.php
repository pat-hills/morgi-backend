<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;

Route::group(['prefix' => 'notifications'], function () use ($middleware) {
    Route::group(['middleware' => $middleware], function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/last-read', [NotificationController::class, 'lastRead']);
        Route::post('/seen', [NotificationController::class, 'seen']);
    });
});
