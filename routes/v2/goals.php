<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\RookieGoalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\GoalTypeController;
use App\Http\Controllers\LeaderGoalController;
use App\Http\Controllers\SavedGoalController;

Route::get('/public/goals/{goal}', [RookieGoalController::class, 'publicShow']);

Route::group(['middleware' => MiddlewareEnum::BASE_MIDDLEWARE], function () {

    Route::group(['middleware' => MiddlewareEnum::MIDDLEWARE_IS_ROOKIE], function () {
        Route::group(['prefix' => 'goals'], function () {
            Route::post('/', [RookieGoalController::class, 'store']);
            Route::get('/types', [GoalTypeController::class, 'index']);

            Route::group(['prefix' => '{goal}'], function () {
                Route::get('/transactions', [TransactionController::class, 'goalTransactions']);
                Route::post('/request-review', [RookieGoalController::class, 'requestReview']);
                Route::post('/cancel', [RookieGoalController::class, 'cancel']);
                Route::post('/retrieve', [RookieGoalController::class, 'withdraw']);
                Route::patch('/', [RookieGoalController::class, 'update']);
                Route::delete('/', [RookieGoalController::class, 'delete']);

                Route::group(['prefix' => 'proofs'], function () {
                    Route::post('/', [RookieGoalController::class, 'submitProof']);
                    Route::delete('/{proof}', [RookieGoalController::class, 'removeProof']);
                });
                Route::get('/supporters', [RookieGoalController::class, 'supporters']);
            });
        });
    });

    Route::group(['middleware' => MiddlewareEnum::MIDDLEWARE_IS_LEADER], function () {

        Route::group(['prefix' => 'rookies/{rookie}/goals/{goal}'], function () {
            Route::post('/donate', [LeaderGoalController::class, 'donate']);
        });

        Route::group(['prefix' => 'goals'], function (){
            Route::get('/', [LeaderGoalController::class, 'index']);
            Route::get('/supported', [LeaderGoalController::class, 'supportedGoals']);
            Route::get('/saved', [SavedGoalController::class, 'index']);
            Route::get('/paths', [LeaderGoalController::class, 'getPaths']);

            Route::group(['prefix' => '{goal}/save'], function () {
                Route::post('/', [SavedGoalController::class, 'store']);
                Route::delete('/', [SavedGoalController::class, 'unSave']);
            });
        });
    });

    Route::group(['prefix' => 'rookies'], function () {
        Route::group(['prefix' => '{rookie}'], function () {
            Route::group(['prefix' => 'goals'], function () {
                Route::get('/past', [RookieGoalController::class, 'indexPastGoals']);
                Route::get('/', [RookieGoalController::class, 'index']);
                Route::get('/{goal}', [RookieGoalController::class, 'show']);
            });
        });
    });

    Route::group(['prefix' => 'goals'], function (){
        Route::get('/{goal}', [LeaderGoalController::class, 'show']);
    });
});
