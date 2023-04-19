<?php

namespace App\Services\Chat;

use App\Enums\PubnubChannelTypeEnum;
use App\Enums\PubnubGroupEnum;
use App\Models\Leader;
use App\Models\PubnubChannel;
use App\Models\PubnubChannelSetting;
use App\Models\PubnubGroup;
use App\Models\PubnubGroupChannel;
use App\Models\Rookie;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PubNub\Exceptions\PubNubException;

class Chat
{
    private $pubnub;

    public static function config(int $user_id): Chat
    {
        return new Chat($user_id);
    }

    public function __construct(int $user_id)
    {
        $this->pubnub = PubNub::config($user_id);
    }

    public function userSignup(User $user): void
    {
        foreach (PubnubGroupEnum::CATEGORIES as $category){
            $this->createUserChannelGroup($user, $category);
        }
    }

    public function createUserChannelGroup(User $user, string $category): PubnubGroup
    {
        $default_group_exists = PubnubGroup::query()->where('category', $category)->where('user_id', $user->id)->exists();
        $name = "{$category}_{$user->id}";

        if($default_group_exists){
            $name .= "_" . Str::uuid();
        }

        return PubnubGroup::create([
            'user_id' => $user->id,
            'category' => $category,
            'name' => $name
        ]);
    }

    public function setUserMetadata(User $user): void
    {
        $this->pubnub->setUserMetadata($user);
    }

    public function logout(User $user, string $token): void
    {
        $this->pubnub->revokeAccessTokenFromUUID($user->id, $token);
    }

    public function startDirectChat(User $leader_user,
                                    User $rookie_user,
                                    int $subscription_id = null,
                                    int $users_referral_emails_sent_id = null,
                                    bool $is_referral = false,
                                    bool $is_free_connection = false,
                                    int $goal_id = null): ?PubnubChannel
    {
        try {
            $channel = $this->createChannel(
                $leader_user,
                $rookie_user,
                $subscription_id,
                $users_referral_emails_sent_id,
                $is_referral,
                $is_free_connection,
                $goal_id
            );

            if(!isset($channel)){
                return null;
            }

            $this->addChannelToChannelsGroups($channel, $leader_user, $rookie_user);
            $this->pubnub->setChannelMembers($channel->name, [(string)$leader_user->id, (string)$rookie_user->id]);

            $channel->update(['created' => true]);

            return $channel;

        }catch (PubNubException $exception){
            Utils::storeError($exception, 'startDirectChat');
            throw new \Exception("Pubnub Error " . $exception->getMessage());
        }
    }

    private function createChannel(User $leader_user,
                                   User $rookie_user,
                                   int $subscription_id = null,
                                   int $users_referral_emails_sent_id = null,
                                   bool $is_referral = false,
                                   bool $is_free_connection = false,
                                   int $goal_id = null): PubnubChannel
    {
        if(isset($subscription_id)){
            $channel = PubnubChannel::where('subscription_id', $subscription_id)->first();
            if(isset($channel)){
                $channel->update([
                    'active' => true,
                    'is_paused' => false
                ]);
                return $channel;
            }
        }

        if(isset($users_referral_emails_sent_id)){
            $channel = PubnubChannel::where('users_referral_emails_sent_id', $users_referral_emails_sent_id)->first();
            if(isset($channel)){
                $channel->update([
                    'active' => true,
                    'is_paused' => false
                ]);
                return $channel;
            }
        }

        $channel = PubnubChannel::where('rookie_id', $rookie_user->id)->where('leader_id', $leader_user->id)->first();
        if(isset($channel)){
            $channel->update([
                'active' => true,
                'subscription_id' => $subscription_id,
                'users_referral_emails_sent_id' => $users_referral_emails_sent_id,
                'is_referral' => $is_referral,
                'is_paused' => false
            ]);
            return $channel;
        }

        $type = (isset($users_referral_emails_sent_id) || $is_referral) ? PubnubChannelTypeEnum::REFERRAL : PubnubChannelTypeEnum::SUBSCRIPTION;
        $channel_name = PubnubChannel::getChannelNameByCategory(PubnubGroupEnum::DIRECT_CATEGORY, $leader_user->id, $rookie_user->id);
        $active_channel_setting = PubnubChannelSetting::query()
            ->where('is_active', true)
            ->first();

        if(!isset($active_channel_setting)){
            throw new \Exception("Unable to retrieve an active channel setting");
        }

        $pubnub_channel = PubnubChannel::create([
            'created' => false,
            'active' => true,
            'category' => PubnubGroupEnum::DIRECT_CATEGORY,
            'rookie_id' => $rookie_user->id,
            'leader_id' => $leader_user->id,
            'display_name' => $channel_name,
            'name' => $channel_name,
            'subscription_id' => $subscription_id,
            'users_referral_emails_sent_id' => $users_referral_emails_sent_id,
            'type' => ($is_free_connection) ? PubnubChannelTypeEnum::FREE_CONNECTION : $type,
            'is_referral' => $is_referral,
            'channel_setting_id' => $active_channel_setting->id,
            'last_activity_at' => now(),
            'goal_id' => $goal_id
        ]);

        $rookie = Rookie::find($rookie_user->id);
        if(isset($rookie) && $rookie->is_converter){
            Leader::query()->where('id', $leader_user->id)->update(['has_converter_chat' => true]);
        }

        return $pubnub_channel;
    }

    public function grantUserChannelsWithToken(User $user, string $token): void
    {
        $channel_groups = PubnubGroup::query()->where('user_id', $user->id)->get();
        $pubnub_channels = PubnubChannel::query()->where("{$user->type}_id", $user->id)
            ->whereNull('user_block_id')
            ->get();

        $this->pubnub->grantAccessTokenToUUID($user->id, $token);
        $this->pubnub->grantAccessTokenToChannelGroups($channel_groups->pluck('name')->toArray(), $token);

        if($user->type === 'rookie'){
            $this->pubnub->grantAccessTokenToActiveChannels($pubnub_channels->pluck('name')->toArray(), $token);
            return;
        }

        $active_channels = $pubnub_channels->where('is_paused', false)->pluck('name')->toArray();
        $inactive_channels = $pubnub_channels->where('is_paused', true)->pluck('name')->toArray();

        $this->pubnub->grantAccessTokenToActiveChannels($active_channels, $token);
        $this->pubnub->grantAccessTokenToInactiveChannels($inactive_channels, $token);
    }

    public function grantUserChannelWithToken(User $user, PubnubChannel $pubnub_channel, string $token): void
    {
        if($pubnub_channel->is_paused){
            $this->pubnub->grantAccessTokenToInactiveChannels([$pubnub_channel], $token);
            return;
        }

        $this->pubnub->grantAccessTokenToActiveChannels([$pubnub_channel], $token);
    }

    public function addChannelToChannelsGroups(PubnubChannel $pubnubChannel, User $leader_user, User $rookie_user): void
    {
        $leader_direct_group = $leader_user->latestOrCreateGroupByCategory(PubnubGroupEnum::DIRECT_CATEGORY);
        $rookie_direct_group = $rookie_user->latestOrCreateGroupByCategory(PubnubGroupEnum::DIRECT_CATEGORY);
        $pubnubGroups = collect([$leader_direct_group, $rookie_direct_group]);

        $this->pubnub->addChannelsToChannelsGroups([$pubnubChannel->name], $pubnubGroups->pluck('name')->toArray());

        $rows = [];
        foreach ($pubnubGroups as $pubnubGroup){
            $rows[] = [
                'channel_id' => $pubnubChannel->id,
                'group_id' => $pubnubGroup->id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        PubnubGroupChannel::query()->upsert($rows, [
            'channel_id', 'group_id'
        ]);
    }
}
