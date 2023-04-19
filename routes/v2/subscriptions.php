<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\CcbillController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;

Route::group(['prefix' => 'subscriptions', 'middleware' => $middleware], function() {
    Route::middleware(MiddlewareEnum::MIDDLEWARE_IS_ROOKIE)->group(function() {
        Route::get('/leaders', [LeaderController::class, 'leadersList']);
    });
});

Route::group(['prefix' => 'subscriptions', 'middleware' => $middleware], function() {

    Route::middleware(MiddlewareEnum::MIDDLEWARE_IS_LEADER)->group(function() {

        Route::get('/packages', [SubscriptionController::class, 'indexPackages']);
        Route::get('/rookies', [LeaderController::class, 'activeMorgiGifting']);

        Route::prefix('/renew')->group(function() {
            Route::post('/', [CcbillController::class, 'renewSubscriptions']);
            Route::get('/', [CcbillController::class, 'indexToRenew']);
            Route::get('/credit-cards', [CcbillController::class, 'indexToRenewWithCc']);
        });

        Route::prefix('/{subscription}')->group(function() {
            Route::patch('/update', [SubscriptionController::class, 'update']);
            Route::post('/reactivate', [SubscriptionController::class, 'reactivate']);
            Route::post('/renew', [SubscriptionController::class, 'renew']);

        });
    });
});
