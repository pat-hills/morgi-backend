<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\LeaderQuotationController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;
$middleware[] = MiddlewareEnum::MIDDLEWARE_IS_LEADER;

Route::group(['prefix' => 'quotations'], function () use ($middleware) {
    Route::group(['middleware' => $middleware], function () {
        Route::get('/', [LeaderQuotationController::class, 'index']);
        Route::post('/create', [LeaderQuotationController::class, 'create']);
        Route::post('/update', [LeaderQuotationController::class, 'update']);
    });
});
