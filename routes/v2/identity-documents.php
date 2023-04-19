<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\IdentityVerifyController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;

Route::group(['prefix' => 'identity-documents'], function () use ($middleware) {
    Route::group(['middleware' => $middleware], function () {
        Route::post('/', [IdentityVerifyController::class, 'verify']);
    });
});
