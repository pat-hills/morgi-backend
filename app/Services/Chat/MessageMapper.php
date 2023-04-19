<?php

namespace App\Services\Chat;

use App\Models\ChatAttachment;
use App\Models\PubnubChannel;
use Carbon\Carbon;

class MessageMapper
{
    private $message;
    private $custom_date_format;
    private $channel;

    private $sender_id;
    private $receiver_id;
    private $type;
    private $sent_at;
    private $text;
    private $micromorgi_amount;
    private $url;

    public static function config(array $entry, PubnubChannel $channel, string $custom_date_format = null): MessageMapper
    {
        $entry = json_decode(json_encode($entry));
        return new MessageMapper($entry, $channel, $custom_date_format);
    }

    public function __construct(object $entry, PubnubChannel $channel, string $custom_date_format = null)
    {
        $this->message = $entry;
        $this->sent_at = now()->timestamp;
        $this->custom_date_format = $custom_date_format;
        $this->channel = $channel;

        $this->map();
    }

    public function map(): void
    {
        $this->mapType();
        $this->mapText();
        $this->mapUrl();
        $this->mapMicromorgiAmount();
        $this->sender_id = $this->message->user_id;
        $this->mapReceiverId();
        $this->sent_at = (isset($this->custom_date_format))
            ? Carbon::createFromTimestamp($this->sent_at)->format($this->custom_date_format)
            : $this->sent_at;
    }

    public function mapReceiverId(): void
    {
        $this->receiver_id = ($this->channel->rookie_id === $this->sender_id) ? $this->channel->leader_id : $this->channel->rookie_id;
    }

    public function mapType(): void
    {
        if($this->message->type === 'text'){
            $this->type = 'message';
            return;
        }

        $this->type = $this->message->type;
    }

    public function mapMicromorgiAmount(): void
    {
        if($this->message->type !== 'micromorgi_transaction'){
            return;
        }

        if(!isset($message->meta) || !isset($message->meta->micromorgiAmount)){
            return;
        }

        $this->micromorgi_amount = $message->meta->micromorgiAmount;
    }

    public function mapUrl(): void
    {
        if(!in_array($this->message->type, ['photo', 'video'])){
            return;
        }

        if(!isset($message->meta) || !isset($message->meta->attachmentId)){
            return;
        }

        $chat_attachment = ChatAttachment::query()->find($message->meta->attachmentId);
        if(!isset($chat_attachment)){
            return;
        }

        $this->url = $chat_attachment->url ?? null;
    }

    public function mapText(): void
    {
        if($this->message->type === 'text'){
            $this->text = $this->message->message;
            return;
        }

        if($this->message->type === 'message'){
            $this->text = $this->message->text;
        }
    }

    public function toObject(): object
    {
        return (object)[
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'type' => $this->type,
            'text' => $this->text,
            'micromorgi_amount' => $this->micromorgi_amount,
            'url' => $this->url,
            'sent_at' => $this->sent_at
        ];
    }
}
