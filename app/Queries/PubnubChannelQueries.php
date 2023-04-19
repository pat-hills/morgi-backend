<?php

namespace App\Queries;

use App\Models\ChatAttachment;
use App\Models\PubnubChannel;
use App\Models\PubnubMessage;
use App\Models\Subscription;

class PubnubChannelQueries
{
    private $channel;

    public static function config(int $channel_id): PubnubChannelQueries
    {
        return new PubnubChannelQueries($channel_id);
    }

    public function __construct(int $channel_id)
    {
        $this->channel = PubnubChannel::find($channel_id);
    }

    public function getAttachmentsCount(int $sender_id, string $type = null): int
    {
        $receiver_id = ($this->channel->rookie_id === $sender_id)
            ? $this->channel->leader_id
            : $this->channel->rookie_id;

        $query = ChatAttachment::query()
            ->where('sender_id', $this->channel->rookie_id)
            ->where('sender_id', $sender_id)
            ->where('receiver_id', $receiver_id);

        if(isset($type)){
            $query = $query->where('type', $type);
        }

        return $query->count();
    }

    public function getMessagesCount(int $sender_id = null): int
    {
        $query = PubnubMessage::query()->where('channel_id', $this->channel->id);

        if(isset($sender_id)){
            $query = $query->where('sender_id', $sender_id);
        }

        return $query->count();
    }

    public function isActivePaidConnection(): bool
    {
        if(!isset($this->channel->subscription_id)){
            return false;
        }

        $subscription = Subscription::find($this->channel->subscription_id);
        if(!isset($subscription) || $subscription->status !== 'active'){
            return false;
        }

        return true;
    }
}
