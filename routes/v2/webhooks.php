<?php

use App\Enums\MiddlewareEnum;
use App\Webhooks\Ccbill\CCbillWebhook;
use App\Webhooks\ElasticTranscoder\ElasticTranscoderWebhook;
use App\Webhooks\Sendgrid\SendgridWebhook;
use App\Webhooks\Telegram\TelegramWebhook;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => MiddlewareEnum::MIDDLEWARE_IS_CCBILL], function () {
    Route::post('ccbill/webhook', [CCbillWebhook::class, 'store']);
});

Route::group(['middleware' => MiddlewareEnum::MIDDLEWARE_IS_SENDGRID], function() {
    Route::post('sendgrid/webhook', [SendgridWebhook::class, 'sendgridWebhook']);
});

Route::post('transcoder/webhook', [ElasticTranscoderWebhook::class, 'store']);

Route::post('telegram', [TelegramWebhook::class, 'store']);
