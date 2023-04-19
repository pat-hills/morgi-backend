<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\BroadcastController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;
$middleware[] = MiddlewareEnum::MIDDLEWARE_IS_ROOKIE;

Route::group(['middleware' => $middleware], function () {
    Route::group(['prefix' => 'broadcasts'], function () {
        Route::post('/', [BroadcastController::class, 'store']);
        Route::get('/', [BroadcastController::class, 'index']);
        Route::group(['prefix' => '{broadcast}'], function () {
            Route::get('/', [BroadcastController::class, 'show']);
            //Route::patch('/', [BroadcastController::class, 'update']);
            //Route::delete('/', [BroadcastController::class, 'delete']);
            Route::post('/messages', [BroadcastController::class, 'sendMessage']);
        });
    });
});
