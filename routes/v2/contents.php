<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\ContentEditorController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;
$middleware[] = MiddlewareEnum::MIDDLEWARE_IS_ROOKIE;

Route::group(['prefix' => 'contents'], function () use ($middleware) {
    Route::group(['middleware' => $middleware], function () {
        Route::get('/inspiration', [ContentEditorController::class, 'getInspirationContents']);
        Route::get('/news-update', [ContentEditorController::class, 'getNewsUpdateContents']);
    });
});
