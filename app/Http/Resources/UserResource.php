<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\Country;
use App\Models\Gender;
use App\Models\Leader;
use App\Models\Photo;
use App\Models\PhotoHistory;
use App\Models\PubnubChannel;
use App\Models\PubnubGroup;
use App\Models\ChatTopic;
use App\Models\ChatTopicsUsers;
use App\Models\FavoriteActivitiesUsers;
use App\Models\FavoriteActivity;
use App\Models\Rookie;
use App\Models\RookieSaved;
use App\Models\Subscription;
use App\Models\UserABGroup;
use App\Models\Video;
use App\Models\VideoHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserResource extends Resource
{
    /*
     * Added this attribute to don't repeat "$this->resources->pluck('id')->toArray()". This attribute boosts performance
     */
    private $resources_ids;

    public function __construct(Request $request, $resources)
    {
        parent::__construct($request, $resources);
        $this->resources_ids = $this->resources->pluck('id')->toArray();
    }

    public function small(): UserResource
    {
        $this->attributes = [
            'id',
            'type',
            'full_name',
            'username',
            'description',
            'is_online',
            'pubnub_uuid',
            'created_at',
        ];

        $this->addGenderToResource();
        $this->addAvatarToResource();
        $this->addVideoToResource();
        $this->addTypeAttributesToResource();
        $this->addChatTopicToResource();
        $this->addFavoriteActiviyToResource();  

        return $this;
    }

    public function regular(): UserResource
    {
        $this->small();
        return $this;
    }

    public function extended(): UserResource
    {
        if(isset($this->requesting_user)){
            $this->addSubscriptionToResource();
            $this->addChannelToResource();
            $this->addSavedToResource();
        }

        $this->regular();
        return $this;
    }

    public function own(): UserResource
    {
        $this->attributes = [
            'id',
            'signup_source',
            'status',
            'type',
            'full_name',
            'username',
            'description',
            'email',
            'sent_first_message',
            'updated_username',
            'telegram_bot_token',
            'language',
            'currency',
            'referral_code',
            'persona',
            'advertisement_id',
            'joined_telegram_bot_at',
            'telegram_chat_id',
            'referred_by',
            'is_online',
            'total_subscriptions_count',
            'total_successful_paused_connections',
            'total_paused_connections',
            'pubnub_uuid',
            'created_at',
            'profile_alerts',
            'id_verified',
        ];

        $this->addGenderToResource();
        $this->addOwnAvatarToResource();
        $this->addOwnVideoToResource();
        $this->addGroupToResource();
        $this->addSignupCountryToResource();
        $this->addPubnubGroupsToResource();
        $this->addOwnDescriptionToResource();
        $this->addTypeAttributesToResource('own');
        $this->addChatTopicToResource();
        $this->addFavoriteActiviyToResource();  


        return $this;
    }

    private function addOwnDescriptionToResource()
    {
        foreach ($this->resources as $resource) {
            $resource->description = $resource->getOwnDescription();
        }

        $this->attributes[] = 'description';
    }

    private function addGenderToResource()
    {
        $cache_reference = 'genders';
        $tags = ['resources', 'users'];

        $genders = Cache::tags($tags)->get($cache_reference);

        if (!isset($genders)){
            $genders = GenderResource::compute(
                $this->request,
                Gender::query()->get(),
                'small'
            )->get();

            Cache::tags($tags)->put($cache_reference, $genders, 86400);
        }

        foreach ($this->resources as $resource) {
            $resource->gender = $genders->where('id', $resource->gender_id)->first();
        }

        $this->attributes[] = 'gender';
    }

    //TODO: creare una resource per user ab group, p4
    private function addGroupToResource()
    {
        $cache_reference = 'ab_groups';
        $tags = ['resources', 'users'];

        $groups = Cache::tags($tags)->get($cache_reference);

        if (!isset($groups)){
            $groups = UserABGroup::query()->get();

            Cache::tags($tags)->put($cache_reference, $groups, 86400);
        }

        foreach ($this->resources as $resource) {
            $resource->group = $groups->where('id', $resource->group_id)->first();
        }

        $this->attributes[] = 'group';
    }

    private function addAvatarToResource()
    {
        $avatars = PhotoResource::compute(
            $this->request,
            Photo::query()->whereIn('user_id', $this->resources_ids)->where('main', true)->get(),
            'small'
        )->get();

        foreach ($this->resources as $resource) {
            $resource->avatar = $avatars->where('user_id', $resource->id)->first();
        }

        $this->attributes[] = 'avatar';
    }

    private function addOwnAvatarToResource()
    {
        $avatars_history = PhotoHistory::query()
            ->whereIn('user_id', $this->resources_ids)
            ->whereNotIn('status', ['approved', 'declined'])
            ->where('main', true)
            ->get();

        $avatars_history_resources = PhotoHistoryResource::compute(
            $this->request,
            $avatars_history,
            'small'
        )->get();

        $avatars = Photo::query()
            ->whereIn('user_id', $this->resources_ids)
            ->where('main', true)
            ->get();

        $avatars_resources = PhotoResource::compute(
            $this->request,
            $avatars,
            'small'
        )->get();

        foreach ($this->resources as $resource) {

            $avatar_history = $avatars_history_resources->where('user_id', $resource->id)->first();
            $avatar = $avatars_resources->where('user_id', $resource->id)->first();

            $resource->avatar = $avatar_history ?? $avatar;
        }

        $this->attributes[] = 'avatar';
    }

    private function addTypeAttributesToResource(string $response_type = 'small')
    {
        $leaders = $this->resources->where('type', 'leader');
        $rookies = $this->resources->where('type', 'rookie');

        $leaders_type_attributes = LeaderResource::compute(
            $this->request,
            Leader::query()->whereIn('id', $leaders->pluck('id'))->get(),
            $response_type
        )->get();

        $rookies_type_attributes = RookieResource::compute(
            $this->request,
            Rookie::query()->whereIn('id', $rookies->pluck('id'))->get(),
            $response_type
        )->get();

        foreach ($leaders as $resource) {
            $resource->type_attributes = $leaders_type_attributes->where('id', $resource->id)->first();
        }

        foreach ($rookies as $resource) {
            $resource->type_attributes = $rookies_type_attributes->where('id', $resource->id)->first();
        }

        $this->attributes[] = 'type_attributes';
    }

    private function addOwnVideoToResource()
    {
        $videos = VideoResource::compute(
            $this->request,
            Video::query()->whereIn('user_id', $this->resources_ids)->get(),
            'small'
        )->get();

        $videos_history = VideoHistoriesResource::compute(
            $this->request,
            VideoHistory::query()->whereIn('user_id', $this->resources_ids)->where('status', 'to_check')->get(),
            'small'
        )->get();

        foreach ($this->resources as $resource) {

            $video_history = $videos_history->where('user_id', $resource->id)->first();
            $video = $videos->where('user_id', $resource->id)->first();

            $resource->video = $video_history ?? $video;
        }

        $this->attributes[] = 'video';
    }

    private function addVideoToResource()
    {
        $videos = VideoResource::compute(
            $this->request,
            Video::query()->whereIn('user_id', $this->resources_ids)->get(),
            'small'
        )->get();

        foreach ($this->resources as $resource) {
            $resource->video = $videos->where('user_id', $resource->id)->first();
        }

        $this->attributes[] = 'video';
    }

    private function addSubscriptionToResource()
    {
        if(!isset($this->requesting_user)){
            return;
        }

        $column_to_search = ($this->requesting_user->type === 'leader') ? 'leader_id' : 'rookie_id';
        $column_to_pluck = ($this->requesting_user->type === 'leader') ? 'rookie_id' : 'leader_id';

        $subscriptions = Subscription::query()
            ->where($column_to_search, $this->requesting_user->id)
            ->whereIn($column_to_pluck, $this->resources_ids)
            ->get();

        $subscriptions_resources = SubscriptionResource::compute(
            $this->request,
            $subscriptions,
            'small'
        )->get();

        foreach ($this->resources as $resource){
            $resource->subscription = $subscriptions_resources->where($column_to_pluck, $resource->id)->first();
        }

        $this->attributes[] = 'subscription';
    }

    private function addChannelToResource()
    {
        if(!isset($this->requesting_user)){
            return;
        }

        $column_to_search = ($this->requesting_user->type === 'leader') ? 'leader_id' : 'rookie_id';
        $column_to_pluck = ($this->requesting_user->type === 'leader') ? 'rookie_id' : 'leader_id';

        $channels = PubnubChannel::query()
            ->where($column_to_search, $this->requesting_user->id)
            ->whereIn($column_to_pluck, $this->resources_ids)
            ->get();

        foreach ($this->resources as $resource){
            $resource->channel = $channels->where($column_to_pluck, $resource->id)->first();
        }

        $this->attributes[] = 'channel';
    }

    private function addSignupCountryToResource()
    {
        $cache_reference = 'countries';
        $tags = ['resources', 'users'];

        $countries = Cache::tags($tags)->get($cache_reference);

        if (!isset($countries)) {
            $countries = CountryResource::compute(
                $this->request,
                Country::query()->get(),
                'small'
            )->get();

            Cache::tags($tags)->put($cache_reference, $countries, 86400);
        }

        foreach ($this->resources as $resource){
            $resource->signup_country = $countries->where('id', $resource->signup_country_id)->first();
        }

        $this->attributes[] = 'signup_country';
    }

    private function addPubnubGroupsToResource()
    {
        $pubnub_groups = PubnubGroupResource::compute(
            $this->request,
            PubnubGroup::query()->whereIn('user_id', $this->resources_ids)->get(),
            'small'
        )->get();

        foreach ($this->resources as $resource){
            $resource->pubnub_groups = $pubnub_groups->where('user_id', $resource->id)->values();
        }

        $this->attributes[] = 'pubnub_groups';
    }

    private function addSavedToResource()
    {
        if(!isset($this->requesting_user)){
            return;
        }

        $column_to_search = ($this->requesting_user->type === 'leader') ? 'leader_id' : 'rookie_id';
        $column_to_pluck = ($this->requesting_user->type === 'leader') ? 'rookie_id' : 'leader_id';

        $saves = RookieSaved::query()
            ->where($column_to_search, $this->requesting_user->id)
            ->whereIn($column_to_pluck, $this->resources_ids)
            ->get();

        foreach ($this->resources as $resource){
            $resource->saved = $saves->where($column_to_pluck, $resource->id)->first();
        }

        $this->attributes[] = 'saved';
    }

    private function addChatTopicToResource()
    {
        $user_chat_topics = ChatTopicsUsers::query()
            ->whereIn('users_id', $this->resources_ids)
            ->get(['chat_topics_id']);
        $chat_topics = ChatTopic::query()->findMany($user_chat_topics);
        $chat_topics = ChatTopicResource::compute(
            $this->request,
            $chat_topics
        )->get();

        foreach ($this->resources as $resource) {
            $resource->chat_topics = $chat_topics->where('user_id', $resource->id)->values();
        }

        $this->attributes[] = 'chat_topics';
    }


    private function addFavoriteActiviyToResource()
    {
        $user_favorite_activities = FavoriteActivitiesUsers::query()
            ->whereIn('users_id', $this->resources_ids)
            ->get(['favorite_activities_id']);
        $favorite_activities = FavoriteActivity::query()->findMany($user_favorite_activities);
        $favorite_activities = FavoriteActivityResource::compute(
            $this->request,
            $favorite_activities
        )->get();

        foreach ($this->resources as $resource) {
            $resource->favorite_activities = $favorite_activities->values();
        }

        $this->attributes[] = 'favorite_activities';
    }



    public static function compute(Request $request, $resources, string $response_type = null): Resource
    {
        $class = static::class;
        $class_instance = new $class($request, $resources);

        if(isset($response_type) && $response_type === 'own'){
            $class_instance->own();
            return $class_instance;
        }

        return parent::compute($request, $resources, $response_type);
    }
}
