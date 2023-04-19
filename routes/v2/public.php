<?php

use App\Http\Controllers\LeaderController;
use App\Http\Controllers\RookieCarouselController;
use App\Http\Controllers\RookieController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'public'], function () {
    Route::get('rookies', [RookieCarouselController::class, 'getPublicRookies']);
    Route::get('leaders', [LeaderController::class, 'getPublicLeaders']);
    Route::get('rookies/{username}', [RookieController::class, 'showPublicRookie']);
    Route::get('signup-attempt', [UserController::class, 'signupAttempt']);
});
