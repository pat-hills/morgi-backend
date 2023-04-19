<?php

namespace App\Services\Chat;

use App\Enums\PubnubGroupEnum;
use App\Models\ChannelReadTimetoken;
use App\Models\PubnubGroup;
use App\Models\User;
use Carbon\Carbon;
use PubNub\PNConfiguration;

class PubNub
{
    private $pubnub;
    private $ttl;

    public static function config(int $user_id): PubNub
    {
        return new PubNub($user_id);
    }

    public function __construct(int $user_id)
    {
        $pubnub_config = new PNConfiguration();
        $pubnub_config->setPublishKey(env("PUBNUB_PUBLISH_KEY"));
        $pubnub_config->setSubscribeKey(env("PUBNUB_SUBSCRIBE_KEY"));
        $pubnub_config->setSecretKey(env("PUBNUB_SECRET_KEY"));
        $pubnub_config->setUuid((string)$user_id);

        $this->pubnub = new \PubNub\PubNub($pubnub_config);

        $this->ttl = env('PUBNUB_TOKEN_TTL', 1440);
    }

    public function revokeAccessTokenFromUUID(string $uuid, string $token): void
    {
        $this->pubnub->grant()
            ->uuids([$uuid])
            ->authKeys($token)
            ->get(false)
            ->read(false)
            ->write(false)
            ->delete(false)
            ->update(false)
            ->sync();
    }

    public function grantAccessTokenToUUID(string $uuid, string $token): void
    {
        $this->pubnub->grant()
            ->uuids([$uuid])
            ->authKeys($token)
            ->ttl($this->ttl)
            ->get(true)
            ->delete(true)
            ->update(true)
            ->sync();
    }

    public function grantAccessTokenToActiveChannels(array $channels, string $token): void
    {
        if (empty($channels)) {
            return;
        }

        $channel_permissions = PubnubGroupEnum::CHANNEL_GROUPS_PERMISSIONS;
        foreach ($channels as $channel) {
            $channels[] = $channel . '-pnpres';
            //$this->setChannelMetadata($channel);
        }

        $batches = self::splitChannelsInBatches($channels);

        foreach ($batches as $batch){
            $this->pubnub->grant()
                ->channels($batch)
                ->authKeys($token)
                ->ttl($this->ttl)
                ->get($channel_permissions['get'])
                ->read($channel_permissions['read'])
                ->write($channel_permissions['write'])
                ->delete($channel_permissions['delete'])
                ->update($channel_permissions['update'])
                ->manage($channel_permissions['manage'])
                ->join($channel_permissions['join'])
                ->sync();
        }
    }

    public function setChannelMetadata(string $channel): void
    {
        $this->pubnub->setChannelMetadata()
            ->channel($channel)
            ->meta([
                "name" => $channel,
                "custom" => [
                    "type" => PubnubGroupEnum::DIRECT_CATEGORY,
                ]
            ])->sync();
    }

    public function grantAccessTokenToInactiveChannels(array $channels, string $token): void
    {
        if (empty($channels)) {
            return;
        }

        $batches = self::splitChannelsInBatches($channels);

        foreach ($batches as $batch){
            $this->pubnub->grant()
                ->channels($batch)
                ->authKeys($token)
                ->ttl($this->ttl)
                ->get(true)
                ->read(true)
                ->join(true)
                ->write(false)
                ->delete(false)
                ->update(false)
                ->manage(false)
                ->sync();
        }
    }

    public function addChannelsToChannelsGroups(array $channels, array $channel_groups_name): void
    {
        foreach ($channel_groups_name as $channel_group_name){
            $this->pubnub->addChannelToChannelGroup()
                ->channels($channels)
                ->channelGroup($channel_group_name)
                ->sync();
        }
    }

    public function grantAccessTokenToChannelGroups(array $channel_groups_names, string $token): void
    {
        /*
         * Add to the list of channel groups presence channels
         */
        foreach ($channel_groups_names as $channel_group_name){
            $channel_groups_names[] = "$channel_group_name-pnpres";
        }

        $this->pubnub->grant()
            ->channelGroups($channel_groups_names)
            ->authKeys([$token])
            ->read(true)
            ->manage(true)
            ->ttl($this->ttl)
            ->sync();
    }

    public function setUserMetadata(User $user): void
    {
        $this->pubnub->setUUIDMetadata()
            ->uuid((string)$user->id)
            ->meta([
                'name' => $user->full_name,
                'custom' => [
                    'type' => $user->type,
                    'username' => $user->username,
                    'description' => $user->description,
                    'avatar_url' => $user->getPublicAvatar()->url ?? null
                ]])
            ->sync();
    }

    public function setChannelMembers(string $channel, array $uuids): void
    {
        foreach ($uuids as $uuid){
            $this->pubnub->setMemberships()
                ->channels([$channel])
                ->uuid($uuid)
                ->sync();
        }
    }

    public function removeChannelMembers(string $channel, array $uuids): void
    {
        foreach ($uuids as $uuid){
            $this->pubnub->setMemberships()
                ->channels([$channel])
                ->uuid($uuid)
                ->sync();
        }
    }

    public function getChannelMessages(string $channel_name, int $start_at = null, int $end_at = null, string $custom_date_format = null): array
    {
        $entries = $this->pubnub->history()->channel($channel_name);

        if(isset($start_at)){
            $entries = $entries->start($start_at * 10000000);
        }

        if(isset($end_at)){
            $entries = $entries->end($end_at * 10000000);
        }

        $entries = $entries->includeTimetoken(true)->sync()->getMessages();

        $messages = [];

        foreach ($entries as $entry){
            $message = (array)$entry->getEntry();
            $timestamp = (int)round($entry->getTimetoken()/10000000);

            $message['sent_at'] = (isset($custom_date_format) && is_string($custom_date_format))
                ? Carbon::createFromTimestamp($timestamp)->format($custom_date_format)
                : $timestamp;

            if($message['type'] === 'text'){
                $message['type'] = 'message';
                if(array_key_exists('message', $message)){
                    $message['text'] = $message['message'];
                    unset($message['message']);
                }
            }

            $messages[] = $message;
        }

        return $messages;
    }

    public function broadcast(string $channel_name, array $message, array $metadata = null): void
    {
        /*
         * Message base structure:
         * ['type' => 'text', 'text' => $message]
         */
        $this->pubnub->publish()
            ->channel($channel_name)
            ->message($message)
            ->meta($metadata)
            ->sync();
    }

    public function publishMessage($message, $channel, $sender, $metadata = null, $type = 'text'){

        $payload = [
            "pn_gcm" => [
                "notification" => [
                    'body' => $message,
                    'title' => $channel->display_name
                ],
                "data" => [
                    'alert' => $message
                ]
            ],
            'type' => $type,
            'user_id' => $sender->id,
            'senderId' => $sender->pubnub_uuid,
            'text' => $message,
            'meta' => $metadata
        ];

        ChannelReadTimetoken::updateOrCreate([
            'user_id' => $sender->id,
            'channel_id' => $channel->id
        ], [
            'timetoken' => \App\Services\Chat\Utils::getTimetokenFromTimestamp(now()->addSecond()->timestamp)
        ]);

        return $this->pubnub->publish()
            ->channel($channel->name)
            ->message($payload)
            ->meta(["name" => $sender->display_name])
            ->sync();
    }

    private static function splitChannelsInBatches(array $channels): array
    {
        $batch_per_insert = 150;
        $counter = 1;
        $batch_counter = 0;
        $batches = [];

        // Create batches
        foreach ($channels as $row){
            $batches[$batch_counter][] = $row;
            $counter++;

            if($counter >= $batch_per_insert){
                $batch_counter++;
                $counter = 1;
            }
        }

        return $batches;
    }
}
