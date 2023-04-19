<?php

namespace App\Services\Chat;

use App\Enums\PubnubGroupEnum;
use App\Enums\PubnubMessageEnum;
use App\Http\Controllers\Controller;
use App\Models\ChatAttachment;
use App\Models\PubnubChannel;
use App\Models\PubnubError;
use App\Models\PubnubGroup;
use App\Models\PubnubMessage;
use App\Models\User;
use Carbon\Carbon;
use PubNub\Exceptions\PubNubException;

class Utils
{
    public static function storeError(PubNubException $pubNubException, string $api_name): void
    {
        $exception = $pubNubException->getStatus();
        PubnubError::create([
            'users' => json_encode($exception->getAffectedUsers()),
            'channels' => json_encode($exception->getAffectedChannels()),
            'channels_groups' => json_encode($exception->getAffectedChannelGroups()),
            'status_code' => $exception->getStatusCode(),
            'message' => $exception->getOriginalResponse(),
            'api_name' => $api_name
        ]);
    }

    public static function getChannelMessages(PubnubChannel $channel, int $start_at, int $end_at, bool $need_to_date_time = false)
    {
        $messages = PubNub::config($channel->leader_id)->getChannelMessages($channel->name, $start_at, $end_at);
        $response = [];

        foreach ($messages as $message){
            $message['sent_at'] = ($need_to_date_time) ? Carbon::createFromTimestamp($message['sent_at'])->toDateTimeString() : $message;
            $message['receiver_id'] = ($message['user_id']===$channel->rookie_id) ? $channel->leader_id : $channel->rookie_id;
            $response[] = $message;
        }

        return $response;
    }

    public static function getUsersChannelMessages(int $leader_id, int $rookie_id, int $page_size = 15, string $custom_date_format = null)
    {
        $channel_name = PubnubChannel::getChannelNameByCategory(PubnubGroupEnum::DIRECT_CATEGORY, $leader_id, $rookie_id);
        $messages = PubNub::config($leader_id)->getChannelMessages($channel_name, null, null, $custom_date_format);

        $response = [];

        foreach ($messages as $message){

            if(in_array(strtolower($message['type']), PubnubMessageEnum::TYPES)){

                switch ($message['type']){
                    case "micromorgi_transaction":
                        $message['micromorgi_amount'] = $message['meta']['micromorgiAmount'] ?? 0;
                        break;
                    case "video":
                    case "photo":
                        $attachment = ChatAttachment::query()->find($message['meta']['attachmentId'] ?? null);
                        $message['url'] = $attachment->url ?? null;
                        break;
                    default:
                        break;
                }

                $final_messages = [];

                foreach (PubnubMessageEnum::ATTRIBUTES[$message['type']] as $map){
                    $final_messages[$map] = $message[$map] ?? null;
                }

                $response[] = $final_messages;
            }
        }

        return Controller::customPaginate(array_reverse($response), $page_size);
    }

    public static function getTimestampFromTimetoken(int $timetoken): int
    {
        return (int)round($timetoken/10000000);
    }

    public static function getTimetokenFromTimestamp(int $timestamp): int
    {
        return (int)round($timestamp * 10000000);
    }

    public static function initChat(User $user, string $token): void
    {
        try {
            $chat = Chat::config($user->id);
            $chat->grantUserChannelsWithToken($user, $token);
        }catch (PubNubException $exception){
            self::storeError($exception, 'initChat');
            throw new \Exception($exception->getMessage());
        }
    }

    public static function initSingleChannel(User $user, PubnubChannel $pubnubChannel, string $token): void
    {
        try {
            $chat = Chat::config($user->id);
            $chat->grantUserChannelWithToken($user, $pubnubChannel, $token);
        }catch (PubNubException $exception){
            self::storeError($exception, 'initSingleChannel');
            throw new \Exception($exception->getMessage());
        }
    }
}
