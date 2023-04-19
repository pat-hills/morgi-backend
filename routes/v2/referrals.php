<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\UserReferralController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;

Route::group(['prefix' => 'referrals'], function () use ($middleware) {
    Route::group(['middleware' => $middleware], function () {
        Route::group(['middleware' => ['isRookie']], function () {
            Route::post('/leader', [UserReferralController::class, 'referLeader']);
        });
    });
});

Route::group(['prefix' => 'referrals'], function () use ($middleware) {
    Route::group(['middleware' => $middleware], function () {
        Route::group(['middleware' => ['isLeader']], function () {
            Route::post('/rookie', [UserReferralController::class, 'referRookie']);
        });
    });
});
