<?php


namespace App\Telegram;


class TelegramMessage
{
    public static function getInvalidTokenMessages()
    {
        $customer_support_url = env('FRONTEND_URL');
        $messages = \App\Models\TelegramMessage::query()->where('type', 'invalid_token')->orderBy('order')->get();

        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace(['{{customer_support_url}}'], [$customer_support_url], $message->message)
            ];
        }

        return $response;
    }

    public static function getDisconnectedMessages()
    {
        $front_bot_page = env('FRONTEND_URL') . '/telegram-bot';
        $messages = \App\Models\TelegramMessage::query()
            ->where('type', 'disconnect')
            ->where('order', '!=', 2)
            ->orderBy('order')
            ->get();

        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace(['{{front_bot_page}}'], [$front_bot_page], $message->message)
            ];
        }

        return $response;
    }

    public static function getForceDisconnectedMessages($rookie_first_name)
    {
        $messages = \App\Models\TelegramMessage::query()->where('type', 'force_disconnect')->orderBy('order')->get();
        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace(['{{rookie_first_name}}'], [$rookie_first_name], $message->message)
            ];
        }

        return $response;
    }

    public static function getAlreadyConnectedMessages()
    {
        $front_bot_page = env('FRONTEND_URL') . '/telegram-bot';
        $messages = \App\Models\TelegramMessage::query()->where('type', 'already_connected')->orderBy('order')->get();

        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace(['{{front_bot_page}}'], [$front_bot_page], $message->message)
            ];
        }

        return $response;
    }

    public static function getRookieMessagePingMessages($leader_username, $message_center)
    {
        $messages = \App\Models\TelegramMessage::query()->where('type', 'rookie_message_ping')->orderBy('order')->get();
        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace(['{{leader_username}}', '{{message_center}}'], [$leader_username, $message_center], $message->message)
            ];
        }

        return $response;
    }

    public static function getWelcomeMessages($first_name)
    {
        $messages = \App\Models\TelegramMessage::query()->where('type', 'welcome')->orderBy('order')->get();

        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace(['{{first_name}}'], [$first_name], $message->message)
            ];
        }

        return $response;
    }

    public static function getWelcomeLeaderMessages($first_name)
    {
        $messages = \App\Models\TelegramMessage::query()->where('type', 'welcome-leader')->orderBy('order')->get();
        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace(['{{first_name}}'], [$first_name], $message->message)
            ];
        }

        return $response;
    }

    public static function getFirstGiftMessages($leader_username, $amount, $channel_name)
    {
        $channel_link = env('FRONTEND_URL') . "/message-center/$channel_name";
        $messages = \App\Models\TelegramMessage::query()->where('type', 'first_gift')->orderBy('order')->get();
        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace(['{{leader_username}}', '{{amount}}', '{{channel_link}}'], [$leader_username, $amount, $channel_link], $message->message)
            ];
        }

        return $response;
    }

    public static function getRecurringGiftMessages($leader_username, $amount, $channel_name)
    {
        $channel_link = env('FRONTEND_URL') . "/message-center/$channel_name";
        $messages = \App\Models\TelegramMessage::query()->where('type', 'recurring_gift')->orderBy('order')->get();
        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace(['{{leader_username}}', '{{amount}}', '{{channel_link}}'], [$leader_username, $amount, $channel_link], $message->message)
            ];
        }

        return $response;
    }

    public static function getMicroMorgiReceivedMessages($leader_username, $amount, $channel_name)
    {
        $channel_link = env('FRONTEND_URL') . "/message-center/$channel_name";
        $messages = \App\Models\TelegramMessage::query()->where('type', 'micromorgi_received')->orderBy('order')->get();
        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace(['{{leader_username}}', '{{amount}}', '{{channel_link}}'], [$leader_username, $amount, $channel_link], $message->message)
            ];
        }

        return $response;
    }

    public static function getUnreadMessagesReminderMessages($rookie_first_name, $unread_messages)
    {
        $messages = \App\Models\TelegramMessage::query()->where('type', 'unread_messages_reminder')->orderBy('order')->get();
        $message_center = env('FRONTEND_URL') . '/message-center/';
        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace(['{{rookie_first_name}}', '{{unread_messages}}', '{{message_center}}'], [$rookie_first_name, $unread_messages, $message_center], $message->message)
            ];
        }

        return $response;
    }

    public static function getFreeConnectionMessages($leader_username, $channel)
    {
        $messages = \App\Models\TelegramMessage::query()->where('type', 'free_connection')->orderBy('order')->get();
        $message_center = env('FRONTEND_URL') . '/message-center/' . $channel->name;
        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace(['{{leader_username}}', '{{message_center}}'], [$leader_username, $message_center], $message->message)
            ];
        }

        return $response;
    }

    public static function getNewPhotoMessages($rookie_name, $message_center)
    {
        $messages = \App\Models\TelegramMessage::query()->where('type', 'new_photo')->orderBy('order')->get();
        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace(['{{rookie_name}}', '{{message_center}}'], [$rookie_name, $message_center], $message->message)
            ];
        }

        return $response;
    }

    public static function getNewVideoMessages($rookie_name, $message_center)
    {
        $messages = \App\Models\TelegramMessage::query()->where('type', 'new_video')->orderBy('order')->get();
        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace(['{{rookie_name}}', '{{message_center}}'], [$rookie_name, $message_center], $message->message)
            ];
        }

        return $response;
    }

    public static function getNewMessageTypeMessages($rookie_name, $message_center)
    {
        $messages = \App\Models\TelegramMessage::query()->where('type', 'new_message')->orderBy('order')->get();
        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace(['{{rookie_name}}', '{{message_center}}'], [$rookie_name, $message_center], $message->message)
            ];
        }

        return $response;
    }

    public static function getNewReplyMessages($rookie_name, $message_center)
    {
        $messages = \App\Models\TelegramMessage::query()->where('type', 'new_reply')->orderBy('order')->get();
        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace(['{{rookie_name}}', '{{message_center}}'], [$rookie_name, $message_center], $message->message)
            ];
        }

        return $response;
    }

    public static function getInactiveLeaderMessages($first_name, $message_center)
    {
        $messages = \App\Models\TelegramMessage::query()->where('type', 'inactive_leader')->orderBy('order')->get();
        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace(['{{first_name}}', '{{message_center}}'], [$first_name, $message_center], $message->message)
            ];
        }

        return $response;
    }

    public static function getInactiveLeaderPingMessages($first_name, $message_center, $rookies_names, $messages_count)
    {
        $messages = \App\Models\TelegramMessage::query()->where('type', 'inactive_leader_ping')->orderBy('order')->get();
        $response = [];

        foreach ($messages as $message){
            $response[] = [
                'media_type' => $message->media_type,
                'message' => str_replace([
                    '{{first_name}}', '{{message_center}}', '{{rookies_names}}', '{{messages_count}}'
                ], [
                    $first_name, $message_center, $rookies_names, $messages_count
                ], $message->message)
            ];
        }

        return $response;
    }
}
