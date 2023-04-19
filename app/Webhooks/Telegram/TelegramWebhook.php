<?php


namespace App\Webhooks\Telegram;


use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TelegramWebhook
{
    /*
     * Config
     * https://api.telegram.org/bot2138255616:AAGUEzkjtD9oCGMUb_pDIymVa13duyJs348/setWebhook?url=https://api-morgi.k-stage.dev/api/telegram&allowed_updates[]=message&max_connections=100
     * https://api.telegram.org/bot2138255616:AAGUEzkjtD9oCGMUb_pDIymVa13duyJs348/getWebhookInfo
     *
     */

    public function store(Request $request)
    {
        $message = $request->message;

        if(isset($message) && isset($message['from']) && isset($message['text']) && isset($message['chat'])){

            $text = $message['text'];

            if(Str::contains($text, ['/start'])){
                TelegramWebhookHandler::handleStart($message);
            }
        }
    }
}
