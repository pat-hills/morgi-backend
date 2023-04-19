<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\LeaderController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;
$middleware[] = MiddlewareEnum::MIDDLEWARE_IS_ROOKIE;

Route::group(['middleware' => $middleware], function () {
    Route::group(['prefix' => 'leader'], function () {
        Route::get('/', [LeaderController::class, 'chatIndex']);
        Route::group(['prefix' => '/{leader}'], function () {
            Route::get('/', [LeaderController::class, 'show']);
            Route::post('/nickname', [LeaderController::class, 'setNickname']);
            Route::patch('/nickname/{nickname}', [LeaderController::class, 'updateNickname']);
            Route::delete('/nickname/{nickname}', [LeaderController::class, 'removeNickname']);
        });
    });
});
