<?php


namespace App\Telegram;


use App\Logger\Logger;
use App\Mixpanel\Events\EventDisconnectTelegramSuccess;
use App\Models\User;
use App\Models\UserTelegramMessageSent;
use Illuminate\Support\Facades\Http;

class TelegramUtils
{
    public static function sendTelegramNotifications($chat_id, $type, $data = null, $user_id = null)
    {
        if($type==='invalid_token'){
            $messages = TelegramMessage::getInvalidTokenMessages();
        }

        if($type==='disconnect'){
            $messages = TelegramMessage::getDisconnectedMessages();
        }

        if($type==='force_disconnect'){
            $rookie_first_name = $data['rookie_first_name'];
            $messages = TelegramMessage::getForceDisconnectedMessages($rookie_first_name);
        }

        if($type==='welcome'){
            $first_name = $data['first_name'];
            $messages = TelegramMessage::getWelcomeMessages($first_name);
        }

        if($type==='welcome-leader'){
            $first_name = $data['first_name'];
            $messages = TelegramMessage::getWelcomeLeaderMessages($first_name);
        }

        if($type==='already_connected'){
            $messages = TelegramMessage::getAlreadyConnectedMessages();
        }

        if($type==='rookie_message_ping'){
            $leader_username = $data['leader_username'];
            $message_center = $data['message_center'];
            $messages = TelegramMessage::getRookieMessagePingMessages($leader_username, $message_center);
        }

        if($type==='first_gift'){

            $leader_username = $data['leader_username'];
            $amount = $data['amount'];
            $channel_name = $data['channel_name'];

            $messages = TelegramMessage::getFirstGiftMessages($leader_username, $amount, $channel_name);
        }

        if($type==='recurring_gift'){

            $leader_username = $data['leader_username'];
            $amount = $data['amount'];
            $channel_name = $data['channel_name'];

            $messages = TelegramMessage::getRecurringGiftMessages($leader_username, $amount, $channel_name);
        }

        if($type==='micromorgi_received'){

            $leader_username = $data['leader_username'];
            $amount = $data['amount'];
            $channel_name = $data['channel_name'];

            $messages = TelegramMessage::getMicroMorgiReceivedMessages($leader_username, $amount, $channel_name);
        }

        if($type==='unread_messages_reminder'){

            $rookie_first_name = $data['rookie_first_name'];
            $unread_messages = $data['unread_messages'];

            $messages = TelegramMessage::getUnreadMessagesReminderMessages($rookie_first_name, $unread_messages);
        }

        if($type==='free_connection'){

            $leader_username = $data['leader_username'];
            $channel = $data['channel'];

            $messages = TelegramMessage::getFreeConnectionMessages($leader_username, $channel);
        }

        if($type==='new_photo'){

            $rookie_name = $data['rookie_name'];
            $message_center = $data['message_center'];

            $messages = TelegramMessage::getNewPhotoMessages($rookie_name, $message_center);
        }

        if($type==='new_video'){

            $rookie_name = $data['rookie_name'];
            $message_center = $data['message_center'];

            $messages = TelegramMessage::getNewVideoMessages($rookie_name, $message_center);
        }

        if($type==='new_message'){

            $rookie_name = $data['rookie_name'];
            $message_center = $data['message_center'];

            $messages = TelegramMessage::getNewMessageTypeMessages($rookie_name, $message_center);
        }

        if($type==='new_reply'){

            $rookie_name = $data['rookie_name'];
            $message_center = $data['message_center'];

            $messages = TelegramMessage::getNewReplyMessages($rookie_name, $message_center);
        }

        if($type==='inactive_leader_ping'){

            $first_name = $data['first_name'];
            $message_center = $data['message_center'];
            $rookies_names = $data['rookies_names'];
            $messages_count = $data['messages_count'];

            $messages = TelegramMessage::getInactiveLeaderPingMessages($first_name, $message_center, $rookies_names, $messages_count);
        }

        if($type==='inactive_leader'){

            $first_name = $data['first_name'];
            $message_center = $data['message_center'];

            $messages = TelegramMessage::getInactiveLeaderMessages($first_name, $message_center);
        }

        if(isset($messages)){

            $metadata = [];

            if(isset($data['unread_messages'])){
                $metadata['unread_messages'] = $data['unread_messages'];
            }

            self::sendMessages($type, $chat_id, $messages, $user_id, $metadata);
        }
    }

    private static function sendMessages($type, $chat_id, $messages, $user_id = null, $metadata = [])
    {
        foreach ($messages as $message){

            if($message['media_type']==='gif'){
                self::sendAnimation($type, $chat_id, $message['message'], $user_id, $metadata);
                continue;
            }

            if($message['media_type']==='text'){
                self::sendMessage($type, $chat_id, $message['message'], $user_id, $metadata);
            }
        }
    }

    private static function sendMessage($type, $chat_id, $message, $user_id = null, $metadata = [])
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        if(!isset($token)){
            Logger::logMessage("Missing telegram bot configurations");
            throw new \Exception('Missing telegram bot configurations');
        }

        $data = [
            'chat_id' => $chat_id,
            'text' => $message,
            'disable_notification' => false,
            'disable_web_page_preview' => false,
            'parse_mode' => 'HTML'
        ];

        $url_api = "https://api.telegram.org/bot$token/sendMessage?";

        $response = Http::get($url_api, $data);

        if($response->status() !== 200) {
            if ($response->status() === 403) {
                $user = User::query()->where('telegram_chat_id', $chat_id)->first();
                if (isset($user)) {
                    $user->unsubscribeFromTelegram();
                    return;
                }
            }

            Logger::logMessage("Unable to send message via Telegram,\nResponse: " . $response->body()
                . "\nPayload: " . json_encode($data)
                . "\nUrl: $url_api"
            );
            throw new \Exception("Unable to send messages");
        }
        
        UserTelegramMessageSent::query()->create([
            'user_id' => $user_id,
            'telegram_chat_id' => $chat_id,
            'message' => $message,
            'type' => $type
        ] + $metadata);
    }

    private static function sendAnimation($type, $chat_id, $url, $user_id = null, $metadata = [])
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        if(!isset($token)){
            Logger::logMessage("Missing telegram bot configurations");
            throw new \Exception('Missing telegram bot configurations');
        }

        $data = [
            'chat_id' => $chat_id,
            'animation' => $url,
            'disable_notification' => false
        ];

        $url_api = "https://api.telegram.org/bot$token/sendAnimation?";

        $response = Http::get($url_api, $data);

        if($response->status()!==200){
            Logger::logMessage("Unable to send message via Telegram,\nResponse: " . $response->body()
                . "\nPayload: " . json_encode($data)
                . "\nUrl: $url_api"
            );
            throw new \Exception("Unable to send messages");
        }

        UserTelegramMessageSent::query()->create([
            'user_id' => $user_id,
            'telegram_chat_id' => $chat_id,
            'message' => $url,
            'type' => $type
        ] + $metadata);
    }
}
