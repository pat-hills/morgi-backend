<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialiteAuth;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

$middleware = [
    MiddlewareEnum::MIDDLEWARE_AUTH,
    MiddlewareEnum::MIDDLEWARE_UPDATE_LAST_ACTIVITY_AT
];

Route::group(['prefix' => 'auth'], function () use ($middleware) {

    Route::group(['prefix' => 'signup'], function () {
        Route::post('/', [UserController::class, 'signup']);
        Route::post('/facebook', [SocialiteAuth::class, 'facebookSignup']);
        Route::post('/google', [SocialiteAuth::class, 'googleSignup']);
        Route::get('/activate/{token}', [AuthController::class, 'signupActivate']);
    });

    Route::group(['prefix' => 'attach/{user}'], function () {
        Route::post('/facebook', [SocialiteAuth::class, 'facebookUserAttach']);
        Route::post('/google', [SocialiteAuth::class, 'googleUserAttach']);
    });

    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::group(['middleware' => $middleware], function () {
        Route::get('/logout', [AuthController::class, 'logout']);
    });
});
