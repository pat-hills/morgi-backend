<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\MicroMorgiController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;
$middleware[] = MiddlewareEnum::MIDDLEWARE_IS_LEADER;

Route::group(['prefix' => 'micromorgi-packages'], function () use ($middleware) {
    Route::group(['middleware' => $middleware], function () {
        Route::get('/', [MicroMorgiController::class, 'indexPackages']);
        Route::post('/{micromorgiPackage}/buy', [MicroMorgiController::class, 'buyMicromorgi']);
    });
});
