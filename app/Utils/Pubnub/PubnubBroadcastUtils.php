<?php

namespace App\Utils\Pubnub;

use App\Http\Resources\PubnubBroadcastResource;
use App\Models\ChannelReadTimetoken;
use App\Models\PubnubBroadcast;
use App\Models\PubnubChannel;
use App\Models\PubnubMessage;
use App\Services\Chat\PubNub;
use Illuminate\Http\Request;

class PubnubBroadcastUtils
{
    private $channel;
    private $type;

    private $leader_id = null;
    private $rookie_id = null;
    private $sender_id = null; // Sender could be leader id or rookie id
    private $transaction_id = null;

    public function __construct(PubnubChannel $channel, string $type)
    {
        $this->channel = $channel;
        $this->type = $type;
    }

    public static function config(PubnubChannel $channel, string $type): PubnubBroadcastUtils
    {
        return new PubnubBroadcastUtils($channel, $type);
    }

    public function setLeaderId(int $leader_id): PubnubBroadcastUtils
    {
        $this->leader_id = $leader_id;
        return $this;
    }

    public function setRookieId(int $rookie_id): PubnubBroadcastUtils
    {
        $this->rookie_id = $rookie_id;
        return $this;
    }

    public function setSenderId(int $sender_id): PubnubBroadcastUtils
    {
        $this->sender_id = $sender_id;
        return $this;
    }

    public function setTransactionId(int $transaction_id): PubnubBroadcastUtils
    {
        $this->transaction_id = $transaction_id;
        return $this;
    }

    public function send()
    {
        $pubnub_message = PubnubMessage::query()->create([
            'type' => 'system_broadcast',
            'sender_id' => $this->channel->leader_id,
            'receiver_id' => $this->channel->rookie_id,
            'channel_id' => $this->channel->id,
            'sent_at' => now()
        ]);

        $pubnub_broadcast = PubnubBroadcast::query()->create([
            'channel_id' => $this->channel->id,
            'type' => $this->type,
            'leader_id' => $this->leader_id,
            'rookie_id' => $this->rookie_id,
            'transaction_id' => $this->transaction_id,
            'message_id' => $pubnub_message->id
        ]);

        $fake_request = new Request();
        $message = [
            'type' => 'system_broadcast',
            'system_broadcast' => PubnubBroadcastResource::compute($fake_request, $pubnub_broadcast, 'small')->first(),
            'user_id' => $this->leader_id
        ];

        $pubnub = PubNub::config($this->sender_id);
        $pubnub->broadcast($this->channel->name, $message);

        if(isset($this->sender_id)){
            ChannelReadTimetoken::updateOrCreate([
                'user_id' => $this->sender_id,
                'channel_id' => $this->channel->id
            ], [
                'timetoken' => \App\Services\Chat\Utils::getTimetokenFromTimestamp(now()->addSecond()->timestamp)
            ]);
        }

        if(!isset($this->channel->leader_first_message_at)){
            $this->channel->update(['leader_first_message_at' => now()]);
        }
    }
}
