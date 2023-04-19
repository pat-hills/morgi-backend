<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;
$middleware[] = MiddlewareEnum::MIDDLEWARE_IS_ROOKIE;

Route::group(['prefix' => 'video'], function () use ($middleware) {
    Route::post('/', [VideoController::class, 'store']);
    Route::group(['middleware' => $middleware], function () {
        Route::post('/assign', [VideoController::class, 'assign']);
    });
});
