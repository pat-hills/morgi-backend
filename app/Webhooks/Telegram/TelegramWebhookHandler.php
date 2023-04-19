<?php


namespace App\Webhooks\Telegram;


use App\Logger\Logger;
use App\Mixpanel\Events\EventConnectTelegramSuccess;
use App\Models\Rookie;
use App\Models\User;
use App\Models\UserTelegramDataHistory;
use App\Telegram\TelegramMessage;
use App\Telegram\TelegramUtils;
use Illuminate\Support\Str;

class TelegramWebhookHandler
{
    public static function handleStart($message)
    {
        $from = $message['from'];
        $text = $message['text'];
        $chat = $message['chat'];

        /*
         * Attach user for the first time
         */

        $token = explode(" ", $text)[1] ?? null;
        if(!isset($token)){
            TelegramUtils::sendTelegramNotifications($chat['id'], 'invalid_token');
            return;
        }

        $user_already_exists = User::query()->where('telegram_chat_id', $chat['id'])->first();
        $user = User::query()
            ->where('telegram_bot_token', $token)
            ->whereNotIn('status', ['rejected', 'deleted', 'blocked'])
            ->first();

        if(!isset($user)){
            TelegramUtils::sendTelegramNotifications($chat['id'], 'invalid_token');
            return;
        }

        if($user_already_exists || isset($user->joined_telegram_bot_at)){
            TelegramUtils::sendTelegramNotifications($chat['id'], 'already_connected', null, $user->id);
            return;
        }

        if($user->type==='rookie'){
            $rookie = Rookie::query()->find($user->id);
            TelegramUtils::sendTelegramNotifications($chat['id'], 'welcome', ['first_name' => $rookie->first_name], $rookie->id);
        } else {
            TelegramUtils::sendTelegramNotifications($chat['id'], 'welcome-leader', ['first_name' => $user->username], $user->id);
        }

        $user->update([
            'telegram_username' => $from['username'] ?? null,
            'telegram_user_id' => $from['id'],
            'telegram_chat_id' => $chat['id'],
            'joined_telegram_bot_at' => now()
        ]);

        UserTelegramDataHistory::query()->create([
            'user_id' => $user->id,
            'telegram_username' => $from['username'] ?? null,
            'telegram_user_id' => $from['id'],
            'telegram_chat_id' => $chat['id']
        ]);

        try {
            EventConnectTelegramSuccess::config($user->id);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }
    }
}
