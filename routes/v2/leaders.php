<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\LeaderController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;
$middleware[] = MiddlewareEnum::MIDDLEWARE_IS_ROOKIE;

Route::group(['prefix' => 'leaders'], function () use ($middleware) {
    Route::group(['middleware' => $middleware], function () {
        Route::group(['prefix' => '/{leader}'], function () {
            Route::get('/', [LeaderController::class, 'show']);
        });
    });
});
