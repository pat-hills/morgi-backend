<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\BalanceTransactionController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\MicroMorgiController;
use App\Http\Controllers\PubnubChannelController;
use App\Http\Controllers\RookieCarouselController;
use App\Http\Controllers\RookieController;
use App\Http\Controllers\RookieSavedController;
use App\Http\Controllers\RookieSeenController;
use App\Http\Controllers\RookieWinnerHistoryController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;

Route::group(['prefix' => 'rookies'], function () use ($middleware) {
    Route::group(['middleware' => $middleware], function () {
        Route::group(['middleware' => [MiddlewareEnum::MIDDLEWARE_IS_ROOKIE]], function () {
            Route::get('/of-the-day', [RookieWinnerHistoryController::class, 'rookieOfTheDay']);
            Route::get('/winners', [RookieWinnerHistoryController::class, 'morgiWinners']);
        });
    });
});

Route::group(['prefix' => 'rookies'], function () use ($middleware) {
    Route::group(['middleware' => $middleware], function () {

        Route::group(['middleware' => [MiddlewareEnum::MIDDLEWARE_IS_LEADER]], function () {
            Route::get('/', [RookieCarouselController::class, 'getRookies']);
            Route::get('/birthday', [RookieCarouselController::class, 'rookiesTodayBirthdays']);
            Route::get('/saved', [RookieSavedController::class, 'getRookiesSaved']);
            Route::get('/seen', [RookieCarouselController::class, 'getRookiesSeen']);
            Route::get('/{identifier}', [RookieController::class, 'showRookie']);
        });

        Route::group(['middleware' => [MiddlewareEnum::MIDDLEWARE_IS_LEADER]], function () {
            Route::post('/seen', [RookieSeenController::class, 'seenRookie']);
        });

        Route::group(['prefix' => '{rookie}'], function () {

            Route::group(['middleware' => [MiddlewareEnum::MIDDLEWARE_IS_LEADER]], function () {

                Route::group(['prefix' => 'save'], function () {
                    Route::post('/', [RookieSavedController::class, 'saveRookie']);
                    Route::delete('/', [RookieSavedController::class, 'unsaveRookie']);
                });
                Route::post('/channels', [PubnubChannelController::class, 'store']);

                Route::group(['prefix' => 'subscriptions'], function () {
                    Route::post('/', [SubscriptionController::class, 'store']);
                    Route::post('/old', [SubscriptionController::class, 'subscribeOLD']);
                    Route::delete('/', [SubscriptionController::class, 'delete']);
                });

                Route::group(['prefix' => 'micromorgi'], function () {
                    Route::get('/', [BalanceTransactionController::class, 'microMorgiGivenToRookie']);
                    Route::post('/{amount}', [MicroMorgiController::class, 'sendMicromorgi']);
                });
            });
        });
    });
});
