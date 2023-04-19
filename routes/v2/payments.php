<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;
$middleware[] = MiddlewareEnum::MIDDLEWARE_IS_ROOKIE;

Route::group(['prefix' => 'payments'], function () use ($middleware) {
    Route::group(['middleware' => $middleware], function () {

        Route::get('/', [PaymentController::class, 'index']);
        Route::post('/', [PaymentController::class, 'store']);
        Route::get('/platforms', [PaymentController::class, 'platforms']);

        Route::group(['prefix' => '/{paymentPlatformRookie}'], function () {
            Route::get('/', [PaymentController::class, 'show']);
            Route::delete('/', [PaymentController::class, 'delete']);
            Route::post('/', [PaymentController::class, 'update']);
        });
    });
});
