<?php

namespace App\Utils;

use App\Logger\Logger;
use App\Models\BroadcastAttachment;
use App\Models\BroadcastMessage;
use App\Models\BroadcastTeaser;
use App\Models\ChatAttachment;
use App\Models\GoalDonation;
use App\Models\Nickname;
use App\Models\PubnubChannel;
use App\Models\User;
use App\Services\Chat\Chat;
use App\Services\Chat\PubNub;

class BroadcastUtils
{
    public static function checkComplaints($sender, $message, $broadcast)
    {
        $message = (object)[
            'sender_id' => $sender->id,
            'text' => $message,
            'receivers_ids' => $broadcast->users->pluck('id')->toArray()
        ];

        try {
            PubnubMessageUtils::checkSystemComplaint($message);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }
    }

    public static function sendMessage($sender, $message, $channels, $metadata, $broadcast)
    {
        self::checkComplaints($sender, $message, $broadcast);

        $pubnub = new PubNub($sender->id);
        $original_message = $message;

        foreach ($channels as $channel){

            if(str_contains($original_message, "{{friend_name}}")){

                $nickname = Nickname::query()
                    ->where('nicknamer_id', $sender->id)
                    ->where('nicknamed_id', $channel->leader_id)
                    ->latest()->first();

                if(!empty($nickname)){
                    $nickname = $nickname->nickname;
                } else {
                    $leader = User::find($channel->leader_id);
                    $nickname = $leader->username;
                }

                $message = str_replace(['{{friend_name}}'], [$nickname], $original_message);
            }

            $message_icon = 'broadcast';

            if ($broadcast->is_goal) {
                $message_icon = 'group';
            }

            $metadata['message_icon'] = $message_icon;
            $pubnub->setUserMetadata($sender);
            $pubnub->publishMessage($message, $channel, $sender, $metadata);
        }
    }

    public static function sendMediaBroadcast($sender, $message, $channels, $media, $type, $broadcast)
    {
        self::checkComplaints($sender, $message, $broadcast);

        $pubnub = new PubNub($sender->id);
        foreach ($channels as $channel){
            $pubnub->setUserMetadata($sender);
            $attachment_type = ($type == 'image') ? 'photo' : $type; // Because we hate consistency
            $chat_attachment = ChatAttachment::create(
                [
                    'path_location' => $media,
                    'type' => $attachment_type,
                    'sender_id' => $sender->id,
                    'receiver_id' => $channel->leader_id
                ]
            );
            $message_icon = 'broadcast';
            if ($broadcast->is_goal) {
                $message_icon = 'group';
            }
            $metadata = ['attachmentId' => $chat_attachment->id, 'message_icon' => $message_icon];
            $pubnub->publishMessage($message, $channel, $sender, $metadata, $type);
        }

    }

    public static function sendTeaserBroadcast($sender, $message, $channels, $goal_id, $broadcast)
    {
        self::checkComplaints($sender, $message, $broadcast);

        $pubnub = new PubNub($sender->id);
        foreach ($channels as $channel){
            $pubnub->setUserMetadata($sender);
            $message_icon = 'broadcast';
            if ($broadcast->is_goal) {
                $message_icon = 'group';
            }
            $metadata = ['goal_id' => $goal_id, 'message_icon' => $message_icon];
            $type = "goal_teaser";
            $pubnub->publishMessage($message, $channel, $sender, $metadata, $type);
        }

    }

    public static function broadcastMessage(array $goals, array $users, $message, $sender, $broadcast, $metadata = null): void
    {
        // Non-optimal way of doing this
        // We aggregate all the leaders_id to get the existing channels and create the missing channels

        $goal_users = GoalDonation::query()
            ->whereIn('goal_id', $goals)
            ->where('status', 'successful')
            ->get()
            ->pluck('leader_id');

        $leader_ids = array_merge($goal_users->toArray(), $users);

        $leaders = User::query()
            ->findMany($leader_ids)
            ->keyBy('id');

        $channel_leaders = PubnubChannel::query()
            ->where('rookie_id', $sender->id)
            ->whereIn('leader_id', $leader_ids)
            ->get();

        $missing_channel_leaders = array_diff(
            $leader_ids,
            $channel_leaders->pluck('leader_id')->toArray()
        );

        foreach ($missing_channel_leaders as $missing_channel) {
            Chat::config($sender->id)->setUserMetadata($leaders[$missing_channel]);
            Chat::config($sender->id)->startDirectChat($leaders[$missing_channel], $sender);
        }

        $channel_leaders = PubnubChannel::query()
            ->where('rookie_id', $sender->id)
            ->whereIn('leader_id', $leader_ids)
            ->get();

        self::sendMessage($sender, $message, $channel_leaders, $metadata, $broadcast);

        BroadcastMessage::query()
            ->create([
                'broadcast_id' => $broadcast->id,
                'message' => $message
            ]);
    }

    public static function broadcastMediaMessage(array $goals, array $users, $message, $sender, $broadcast, $media, $type): void
    {
        // Non-optimal way of doing this
        // We aggregate all the leaders_id to get the existing channels and create the missing channels

        $goal_users = GoalDonation::query()
            ->whereIn('goal_id', $goals)
            ->where('status', 'successful')
            ->get()
            ->pluck('leader_id');

        $leader_ids = array_merge($goal_users->toArray(), $users);
        $leaders = User::query()
            ->findMany($leader_ids)
            ->keyBy('id');
        $channel_leaders = PubnubChannel::query()
            ->where('rookie_id', $sender->id)
            ->whereIn('leader_id', $leader_ids)
            ->get();
        $missing_channel_leaders = array_diff($leader_ids, $channel_leaders->pluck('leader_id')->toArray());
        foreach ($missing_channel_leaders as $missing_channel) {
            Chat::config($sender->id)->setUserMetadata($leaders[$missing_channel]);
            Chat::config($sender->id)->startDirectChat($leaders[$missing_channel], $sender);
        }
        $channel_leaders = PubnubChannel::query()
            ->where('rookie_id', $sender->id)
            ->whereIn('leader_id', $leader_ids)
            ->get();

        self::sendMediaBroadcast($sender, $message, $channel_leaders, $media, $type, $broadcast);

        $message = BroadcastMessage::query()
            ->create([
                'broadcast_id' => $broadcast->id,
                'message' => $message
            ]);

        BroadcastAttachment::query()
            ->create([
                'broadcast_message_id' => $message->id,
                'url' => $media,
                'type' => $type
            ]);
    }

    public static function broadcastTeaserMessage(array $goals, array $users, $message, $sender, $broadcast, $goal_id): void
    {
        // Non-optimal way of doing this
        // We aggregate all the leaders_id to get the existing channels and create the missing channels

        $goal_users = GoalDonation::query()
            ->whereIn('goal_id', $goals)
            ->where('status', 'successful')
            ->get()
            ->pluck('leader_id');

        $leader_ids = array_merge($goal_users->toArray(), $users);
        $leaders = User::query()
            ->findMany($leader_ids)
            ->keyBy('id');
        $channel_leaders = PubnubChannel::query()
            ->where('rookie_id', $sender->id)
            ->whereIn('leader_id', $leader_ids)
            ->get();
        $missing_channel_leaders = array_diff($leader_ids, $channel_leaders->pluck('leader_id')->toArray());
        foreach ($missing_channel_leaders as $missing_channel) {
            Chat::config($sender->id)->setUserMetadata($leaders[$missing_channel]);
            Chat::config($sender->id)->startDirectChat($leaders[$missing_channel], $sender);
        }
        $channel_leaders = PubnubChannel::query()
            ->where('rookie_id', $sender->id)
            ->whereIn('leader_id', $leader_ids)
            ->get();

        self::sendTeaserBroadcast($sender, $message, $channel_leaders, $goal_id, $broadcast);

        $message = BroadcastMessage::query()
            ->create([
                'broadcast_id' => $broadcast->id,
                'message' => $message
            ]);

        BroadcastTeaser::query()
            ->create([
                'broadcast_message_id' => $message->id,
                'goal_id' => $goal_id,
                'type' => "goal_teaser"
            ]);
    }
}
