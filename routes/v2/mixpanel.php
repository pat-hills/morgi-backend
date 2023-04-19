<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\MixpanelController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;

Route::group(['prefix' => 'mixpanel'], function () use ($middleware) {
    Route::group(['middleware' => $middleware], function () {
        Route::group(['prefix' => 'events'], function () {
            Route::post('/', [MixpanelController::class, 'storeEvent']);

            Route::group(['middleware' => MiddlewareEnum::MIDDLEWARE_IS_LEADER], function() {
                Route::post('/carousel-swipe', [MixpanelController::class, 'storeEventCarouselSwipe']);

            });
        });

    });
});
