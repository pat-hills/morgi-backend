<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\GivebackController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;
$middleware[] = MiddlewareEnum::MIDDLEWARE_IS_LEADER;

Route::group(['prefix' => 'givebacks'], function () use ($middleware) {
    Route::group(['middleware' => $middleware], function () {
        Route::get('/', [GivebackController::class, 'index']);
    });
});
