<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\BalanceTransactionController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;

Route::group(['prefix' => 'transactions'], function () use ($middleware) {

    Route::group(['middleware' => $middleware], function () {

        Route::get('/', [TransactionController::class, 'index']);

        Route::group(['prefix' => 'micromorgi'], function () {

            Route::group(['middleware' => [MiddlewareEnum::MIDDLEWARE_IS_ROOKIE]], function () {
                Route::get('/{leader}', [BalanceTransactionController::class, 'rookieMicroMorgiTransactionsFromLeader']);
            });

            Route::group(['middleware' => [MiddlewareEnum::MIDDLEWARE_IS_LEADER]], function () {
                Route::get('/', [BalanceTransactionController::class, 'leaderMicroMorgiTransactions']);
            });
        });

        Route::group(['prefix' => 'morgi'], function () {
            Route::group(['middleware' => [MiddlewareEnum::MIDDLEWARE_IS_ROOKIE]], function () {
                Route::get('/', [BalanceTransactionController::class, 'rookieMorgiTransactions']);
                Route::get('/{leader}', [BalanceTransactionController::class, 'rookieMorgiTransactionsFromLeader']);
            });
        });

        Route::group(['prefix' => 'coupons'], function () {
            Route::group(['middleware' => [MiddlewareEnum::MIDDLEWARE_IS_LEADER]], function () {
                Route::get('/', [CouponController::class, 'indexTransactions']);
            });
        });
    });
});
