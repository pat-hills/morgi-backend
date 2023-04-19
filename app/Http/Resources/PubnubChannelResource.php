<?php

namespace App\Http\Resources;

use App\Enums\PubnubChannelSettingEnum;
use App\Http\Resources\Parents\Resource;
use App\Models\ChannelReadTimetoken;
use App\Models\Gender;
use App\Models\Leader;
use App\Models\PubnubChannel;
use App\Models\PubnubChannelSetting;
use App\Models\Rookie;
use App\Models\Subscription;
use App\Models\User;
use App\Utils\PubnubChannelUtils;

class PubnubChannelResource extends Resource
{
    public function small(): PubnubChannelResource
    {
        $this->addAttributes([
            'id',
            'type',
            'leader_awaiting_reply',
            'active',
            'is_paused',
            'is_referral',
            'leader_first_message_at',
            'rookie_first_message_at',
            'created_at',
        ]);

        $this->addIsChatAttachmentsBlurredToResources();
        $this->addChannelReadsTimeTokensToResources();
        $this->addSubscriptionToResources();
        $this->addRookieToResources();
        $this->addLeaderToResources();

        return $this;
    }

    public function extended(): PubnubChannelResource
    {
        $this->small();
        return $this;
    }

    private function addLeaderToResources()
    {
        $leaders_ids = $this->resources->pluck('leader_id');
        $leaders = User::query()->whereIn('id', $leaders_ids)->get();
        $leaders_resources = UserResource::compute($this->request, $leaders, 'small')->get();

        foreach ($this->resources as $resource) {
            $resource->leader =  $leaders_resources->where('id', $resource->leader_id)->first();
        }

        $this->attributes[] = 'leader';
    }

    private function addRookieToResources()
    {
        $rookies_ids = $this->resources->pluck('rookie_id');
        $rookies = User::query()->whereIn('id', $rookies_ids)->get();
        $rookies_resources = UserResource::compute($this->request, $rookies, 'small')->get();

        foreach ($this->resources as $resource) {
            $resource->rookie = $rookies_resources->where('id', $resource->rookie_id)->first();
        }

        $this->attributes[] = 'rookie';
    }

    private function addChannelReadsTimeTokensToResources()
    {
        $user = $this->request->user();
        if(!isset($user)){
            return;
        }

        $channels_reads_timetokens = ChannelReadTimetoken::query()
            ->whereIn('channel_id', $this->resources->pluck('id'))
            ->where('user_id', $user->id)
            ->get();

        foreach ($this->resources as $resource) {
            $channel_reads_timetokens = $channels_reads_timetokens->where('channel_id', $resource->id);
            $resource->last_reads_timetokens = ($channel_reads_timetokens->isNotEmpty())
                ? $channel_reads_timetokens->values()
                : null;
        }

        $this->attributes[] = 'last_reads_timetokens';
    }

    private function addSubscriptionToResources()
    {
        $subscriptions_ids = $this->resources->pluck('subscription_id');
        $channels_subscriptions = SubscriptionResource::compute(
            $this->request,
            Subscription::query()->findMany($subscriptions_ids),
            'small'
        )->get();

        foreach ($this->resources as $resource) {
            $resource->subscription = $channels_subscriptions->where('id', $resource->subscription_id)->first();
        }

        $this->attributes[] = 'subscription';
    }

    private function addIsChatAttachmentsBlurredToResources()
    {
        $user = $this->request->user();
        if(!isset($user)){
            return;
        }

        $field_to_pluck = ($user->type === 'rookie') ? 'leader_id' : 'rookie_id';
        $channels_users = User::query()->findMany($this->resources->pluck($field_to_pluck));
        $channels_settings = PubnubChannelSetting::query()->get();

        $rookies_ids = $this->resources->pluck('rookie_id');

        $converters = Rookie::query()
            ->select("id", "is_converter")
            ->whereIn('id', $rookies_ids)
            ->where('is_converter', true);

        $converters_ids = $converters->pluck('id')->toArray();
        $subscriptions_ids = $this->resources->pluck('subscription_id');

        $channels_subscriptions = Subscription::query()
            ->findMany($subscriptions_ids);

        foreach ($this->resources as $resource){
            $channel_user = $channels_users->where('id', $resource->$field_to_pluck)->first();
            $channel_setting = $channels_settings->where('id', $resource->channel_setting_id)->first();
            $channel_setting_type = isset($channel_setting) ? $channel_setting->type : PubnubChannelSettingEnum::TYPE_NONE;

            $subscription = isset($resource->subscription_id)
                ? $channels_subscriptions->where('id', $resource->subscription_id)->first()
                : null;

            $resource->is_chat_attachments_blurred = PubnubChannelUtils::isChatAttachmentsBlurred($channel_setting_type, $user, $channel_user, $converters_ids, $subscription);
            $resource->is_chat_attachments_blurred_to_receiver = PubnubChannelUtils::isChatAttachmentsBlurred($channel_setting_type, $channel_user, $user, $converters_ids, $subscription);
        }

        $this->addAttributes([
            'is_chat_attachments_blurred',
            'is_chat_attachments_blurred_to_receiver'
        ]);
    }
}
