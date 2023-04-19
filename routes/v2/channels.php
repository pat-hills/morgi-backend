<?php

use App\Enums\MiddlewareEnum;
use App\Http\Controllers\ChannelReadTimetokenController;
use App\Http\Controllers\ChatAttachmentController;
use App\Http\Controllers\PubnubChannelController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\PubnubMessagesController;
use Illuminate\Support\Facades\Route;

$middleware = MiddlewareEnum::BASE_MIDDLEWARE;

Route::group(['prefix' => 'channels'], function () use ($middleware) {

    Route::group(['middleware' => $middleware], function () {

        Route::get('/', [PubnubChannelController::class, 'index']);
        Route::get('/old', [PubnubChannelController::class, 'oldIndex']);
        Route::post('/init', [PubnubChannelController::class, 'init']);
        Route::get('/reports/categories', [ComplaintController::class, 'indexTypes']);

        Route::group(['prefix' => '{pubnubChannel}'], function () {

            Route::prefix('messages')->group(function () {
                Route::post('/', [PubnubMessagesController::class, 'store']);
            });

            Route::prefix('reads')->group(function () {
                Route::post('/', [ChannelReadTimetokenController::class, 'updateOrCreate']);
            });

            Route::prefix('reports')->group(function () {
                Route::post('/', [ComplaintController::class, 'store']);
            });

            Route::group(['prefix' => 'attachments'], function () {
                Route::post('/send_{type}', [ChatAttachmentController::class, 'store']);
                Route::prefix('/{chatAttachment}')->group(function () {
                    Route::get('/', [ChatAttachmentController::class, 'show']);
                });
            });

            Route::group(['middleware' => MiddlewareEnum::MIDDLEWARE_IS_ROOKIE], function () {
                Route::post('/pause', [PubnubChannelController::class, 'pause']);
                Route::post('/resume', [PubnubChannelController::class, 'resume']);
            });
        });
    });
});
