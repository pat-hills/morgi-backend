<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CcbillController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\RookieController;
use App\Http\Controllers\RookiesConverterRequestController;
use App\Http\Controllers\RookieScoreController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'profile'], function () {

    Route::get('/unsubscribe/{token}', [UserController::class, 'unsubscribe']);
    Route::group(['middleware' => [MiddlewareEnum::MIDDLEWARE_AUTH, MiddlewareEnum::MIDDLEWARE_UPDATE_LAST_ACTIVITY_AT, MiddlewareEnum::MIDDLEWARE_IS_UNTRUSTED, MiddlewareEnum::MIDDLEWARE_IS_ACTIVE, MiddlewareEnum::MIDDLEWARE_IS_LEADER]], function () {
        Route::get('/latest-payment', [LeaderController::class, 'latestPayment']);
        Route::post('/credit-card', [CcbillController::class, 'addCreditCard']);
    });
});

Route::group(['prefix' => 'profile'], function () {

    Route::get('/unsubscribe/{token}', [UserController::class, 'unsubscribe']);
    Route::group(['middleware' => [MiddlewareEnum::MIDDLEWARE_AUTH, MiddlewareEnum::MIDDLEWARE_UPDATE_LAST_ACTIVITY_AT]], function () {

        Route::get('/', [AuthController::class, 'user']);
        Route::post('/delete', [UserController::class, 'delete']);

        Route::group(['middleware' => [MiddlewareEnum::MIDDLEWARE_IS_UNTRUSTED, MiddlewareEnum::MIDDLEWARE_IS_ACTIVE]], function () {
            Route::post('/update', [UserController::class, 'update']);
            Route::delete('/telegram', [UserController::class, 'disconnectTelegramBot']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
            Route::post('/check-password', [AuthController::class, 'checkCurrentPassword']);

            Route::group(['middleware' => [MiddlewareEnum::MIDDLEWARE_IS_ROOKIE]], function () {
                Route::post('/win-seen', [RookieController::class, 'seenRookieWin']);
                Route::get('/score', [RookieScoreController::class, 'profilePerformance']);

                Route::group(['prefix' => 'converters-request'], function () {
                    Route::post('/', [RookiesConverterRequestController::class, 'store']);
                    Route::patch('/{rookiesConverterRequest}', [RookiesConverterRequestController::class, 'update']);
                });
            });
        });
    });
});
