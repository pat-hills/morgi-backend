<?php

namespace App\Utils;

use App\Models\ChatBadWord;
use App\Models\Complaint;
use App\Models\ComplaintType;
use App\Models\PubnubBroadcast;
use App\Models\PubnubChannel;
use App\Models\PubnubMessage;
use App\Models\Rookie;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Mailer\Mailer;
use App\Telegram\TelegramUtils;
use Carbon\Carbon;

class PubnubMessageUtils
{
    public static function checkSystemComplaint(object $message): void
    {
        if(!isset($message->text)){
            return;
        }

        $complaint_type_id = ComplaintType::query()->where('key_name', 'suspicious_text')->first()->id;
        $bad_words = array_map(
            'strtolower',
            ChatBadWord::query()->select('name')->pluck('name')->toArray()
        );

        $words = explode(" ",
            strtolower($message->text)
        );

        foreach ($words as $word){
            if(in_array($word, $bad_words, true)){
                $bad_word = $word;
                break;
            }
        }

        if(!isset($bad_word)){
            return;
        }

        $json_message = json_encode([
            'text' => $message->text,
            'user_id' => $message->sender_id,
            'type' => 'message'
        ]);

        if (isset($message->receivers_ids)) {
            $complaints = [];
            foreach ($message->receivers_ids as $receiver_id) {
                $complaints[] = [
                    'user_reported' => $message->sender_id,
                    'reported_by' => $receiver_id,
                    'type_id' => $complaint_type_id,
                    'message' => $json_message,
                    'notes' => "Complaint created from system for the following word: $bad_word",
                    'by_system' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            Complaint::insert($complaints);
            return;
        }

        Complaint::query()->create([
            'user_reported' => $message->sender_id,
            'reported_by' => $message->receiver_id,
            'type_id' => $complaint_type_id,
            'message' => $json_message,
            'notes' => "Complaint created from system for the following word: $bad_word",
            'by_system' => true
        ]);
    }

    public static function sendTelegramNotifications(PubnubChannel $pubnubChannel, object $message): void
    {
        $sender = User::find($message->sender_id);
        $receiver = User::find($message->receiver_id);

        if (!isset($receiver, $receiver->joined_telegram_bot_at) || !$receiver->can_receive_telegram_message) {
            return;
        }

        $is_online = Carbon::parse($receiver->last_activity_at)->addMinute() <= Carbon::now();
        if (!$is_online) {
            if($receiver->type !== 'rookie'){
                return;
            }

            $rookie = Rookie::find($receiver->id);
            if(!$rookie->is_converter){
                return;
            }
        }

        /*
         * Sending to rookie
         */
        if ($receiver->type === 'rookie') {
            $data = [
                'leader_username' => $sender->username,
                'message_center' => env('FRONTEND_URL') . '/message-center/' . $pubnubChannel->name
            ];
            TelegramUtils::sendTelegramNotifications($receiver->telegram_chat_id, 'rookie_message_ping', $data, $receiver->id);
            return;
        }

        /*
         * Sending to leader
         */
        $rookie = Rookie::find($sender->id);

        $data = [
            'message_center' => env('FRONTEND_URL') . '/message-center/' . $pubnubChannel->name,
            'rookie_name' => $rookie->first_name . ' ' . $rookie->last_name
        ];

        switch ($message->type) {
            case 'message':
                $type = 'new_message';
                break;
            case 'image':
            case 'photo':
                $type = 'new_photo';
                break;
            case 'video':
                $type = 'new_video';
                break;
        }

        $subscription = Subscription::query()->find($pubnubChannel->subscription_id);
        $previous_message = PubnubMessage::query()->where('channel_id', $pubnubChannel->id)
            ->orderByDesc('sent_at')
            ->skip(1)
            ->first();

        /*
         * If channel's latest message is a system_broadcast and the broadcast is a gift_transaction, notification's type should be new_reply
         */
        if(isset($subscription, $previous_message) && $previous_message->type === 'system_broadcast'){
            $pubnub_broadcast = PubnubBroadcast::query()
                ->where('channel_id', $pubnubChannel->id)
                ->where('type', 'gift_transaction')
                ->where('message_id', $previous_message->id)
                ->first();

            if(isset($pubnub_broadcast)){
                $type = 'new_reply';
            }
        }

        if (isset($type)) {
            TelegramUtils::sendTelegramNotifications($receiver->telegram_chat_id, $type, $data, $receiver->id);
        }
    }

    public static function sendLeaderPingEmail(PubnubChannel $pubnubChannel, int $leader_id, int $rookie_id): void
    {
        if(isset($pubnubChannel->leader_received_ping_email_at)){
            return;
        }

        $leader_user = User::find($leader_id);

        /*
         * If leader is connected to telegram bot, don't send email ping
         */
        if(isset($leader_user->joined_telegram_bot_at)){
            return;
        }

        /*
         * If leader was online in the past minute, don't send email ping
         */
        if(!(Carbon::parse($leader_user->last_activity_at)->addMinute() <= Carbon::now())){
            return;
        }

        $leader_message_ping_emails_sent_count = PubnubChannel::query()
            ->where('leader_id', $leader_user->id)
            ->whereNotNull('leader_received_ping_email_at')
            ->where('leader_received_ping_email_at', '>=', $leader_user->last_activity_at)
            ->count();
        if($leader_message_ping_emails_sent_count >= 3){
            return;
        }

        $rookie_user = User::find($rookie_id);
        $rookie = Rookie::query()->find($rookie_user->id);
        $misc = [
            'rookie_name' => $rookie->first_name,
            'rookie_avatar' => $rookie_user->getOwnAvatar()->url ?? null,
            'channel_link' => env('FRONTEND_URL') . "/message-center/{$pubnubChannel->name}"
        ];

        try {
            Mailer::create($leader_user)
                ->setMisc($misc)
                ->setTemplate('LEADER_MESSAGE_PING')
                ->sendAndCreateUserEmailSentRow();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        $pubnubChannel->update([
            'leader_received_ping_email_at' => now()
        ]);
    }

    public static function getAvgResponseTime(PubnubChannel $pubnubChannel)
    {
        if (isset($pubnubChannel->avg_response_time)) {
            return null;
        }

        if (!isset($pubnubChannel->rookie_first_message_at)) {
            return null;
        }

        $rookie_first_message_at = strtotime($pubnubChannel->rookie_first_message_at);

        $leader_first_message_at = strtotime($pubnubChannel->leader_first_message_at) ?? strtotime($pubnubChannel->created_at);

        if($leader_first_message_at > $rookie_first_message_at){
            $leader_first_message_at = strtotime($pubnubChannel->created_at);
        }

        return $rookie_first_message_at - $leader_first_message_at;
    }
}
