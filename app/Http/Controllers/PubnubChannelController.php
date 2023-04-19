<?php

namespace App\Http\Controllers;

use App\Enums\PubnubChannelSettingEnum;
use App\Enums\PubnubChannelTypeEnum;
use App\Http\Resources\PubnubChannelResource;
use App\Logger\Logger;
use App\Mixpanel\Utils\UserProfileUtils;
use App\Models\ChannelReadTimetoken;
use App\Models\ConnectionOrazioSession;
use App\Models\Gender;
use App\Models\Leader;
use App\Models\Path;
use App\Models\PubnubChannel;
use App\Models\PubnubChannelSetting;
use App\Models\PubnubMessage;
use App\Models\Rookie;
use App\Models\Subscription;
use App\Models\User;
use App\Orazio\OrazioHandler;
use App\Services\Chat\Chat;
use App\Services\Chat\PubNub;
use App\Services\Chat\Utils;
use App\Telegram\TelegramUtils;
use App\Utils\NotificationUtils;
use App\Utils\PubnubChannelUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PubNub\Exceptions\PubNubException;

class PubnubChannelController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $channels = PubnubChannel::query()
            ->where("{$user->type}_id", $user->id)
            ->whereNull('user_block_id');

        if($user->type === 'rookie' && Rookie::find($user->id)->is_converter){
            $channels = $channels->whereNotNull('leader_first_message_at');
        }

        $channels = $channels->orderByDesc('last_activity_at')->get();
        $pubnub_resources = PubnubChannelResource::compute($request, $channels)->get();

        return response()->json([
            'active' => $pubnub_resources->where('is_paused', false),
            'inactive' => $pubnub_resources->where('is_paused', true)
        ]);
    }

    public function oldIndex(Request $request)
    {
        $user = $request->user();
        $field_to_pluck = ($user->type==='rookie')
            ? 'leader_id'
            : 'rookie_id';

        $channels = PubnubChannel::query()
            ->where("{$user->type}_id", $user->id)
            ->whereNull('user_block_id');

        $is_requesting_user_converter = $user->type === 'rookie' && Rookie::find($user->id)->is_converter;
        if($is_requesting_user_converter){
            $channels = $channels->whereNotNull('leader_first_message_at');
        }

        $channels = $channels->orderByDesc('last_activity_at')->get();

        $response = [
            'active' => [],
            'inactive' => []
        ];

        $channels_settings = PubnubChannelSetting::query()->get();
        $channels_users = User::query()
            ->whereIn('id', $channels->pluck($field_to_pluck)->toArray())
            ->where('active', true)
            ->get();

        $channels_subscriptions = Subscription::query()->selectRaw("id, subscription_at, valid_until_at, status")->findMany(
            $channels->pluck('subscription_id')->toArray()
        );
        $converters = Rookie::query()->selectRaw("id, is_converter")->whereIn(
            'id', $channels->pluck('rookie_id')->toArray()
        )->where('is_converter', true);

        $converter_ids = $converters->pluck('id')->toArray();
        $channels_reads_timetokens = ChannelReadTimetoken::query()
            ->whereIn('channel_id', $channels->pluck('id')->toArray())
            ->where('user_id', $user->id)
            ->get();

        foreach ($channels as $channel){

            $channel_status = ($channel->is_paused) ? 'inactive' : 'active';
            $channel_user = $channels_users->where('id', $channel->$field_to_pluck)->first();

            if(!isset($channel_user)){
                continue;
            }

            $channel_setting = $channels_settings->where('id', $channel->channel_setting_id)->first();
            $channel_setting_type = (isset($channel_setting))
                ? $channel_setting->type
                : PubnubChannelSettingEnum::TYPE_NONE;

            $subscription = (isset($channel->subscription_id))
                ? $channels_subscriptions->where('id', $channel->subscription_id)->first()
                : null;

            $channel_response = [
                'avatar' => $channel_user->getPublicAvatar(),
                'leader_first_message_at' => $channel->leader_first_message_at,
                'rookie_first_message_at' => $channel->rookie_first_message_at,
                'subscription_id' => $channel->subscription_id,
                'username' => $channel_user->username,
                'full_name' => $channel_user->full_name,
                'id' => $channel_user->id,
                'channel_id' => $channel->id,
                'is_online' => $channel_user->is_online,
                'leader_awaiting_reply' => $channel->leader_awaiting_reply,
                'is_paused' => $channel->is_paused,
                'type' => $channel->type,
                'created_at' => $channel->created_at,
                'is_chat_attachments_blurred' => PubnubChannelUtils::isChatAttachmentsBlurred($channel_setting_type, $user, $channel_user, $converter_ids, $subscription),
                'is_chat_attachments_blurred_to_receiver' => PubnubChannelUtils::isChatAttachmentsBlurred($channel_setting_type, $channel_user, $user, $converter_ids, $subscription),
                'gender' => Gender::query()->find($channel_user->gender_id)
            ];

            if(isset($subscription)){
                $channel_response['subscription_at'] = $subscription->subscription_at;
                $channel_response['valid_until_at'] = $subscription->valid_until_at;
                $channel_response['subscription_status'] = $subscription->status;
            }else{
                $channel_response['subscription_at'] = null;
                $channel_response['valid_until_at'] = null;
                $channel_response['subscription_status'] = null;
            }

            if($channel_user->type === 'rookie'){
                $channel_response['is_converter'] = in_array($channel_user->id, $converter_ids);
                $rookie_path = Path::query()->selectRaw('paths.id, paths.name, paths.key_name, paths.is_subpath')
                    ->join('users_paths', 'users_paths.path_id', '=', 'paths.id')
                    ->where('users_paths.user_id', $channel_user->id)
                    ->where('paths.is_subpath', false)
                    ->first();
            } else {
                $rookie_path = Path::query()->selectRaw('paths.id, paths.name, paths.key_name, paths.is_subpath')
                    ->join('users_paths', 'users_paths.path_id', '=', 'paths.id')
                    ->where('users_paths.user_id', $user->id)
                    ->where('paths.is_subpath', false)
                    ->first();
            }

            $channel_response['rookie_path'] = $rookie_path;

            /*
             * Channel last reads timetokens
             */
            $channel_reads_timetokens = $channels_reads_timetokens->where('channel_id', $channel->id);
            $channel_response['last_reads_timetokens'] = ($channel_reads_timetokens->isNotEmpty())
                ? $channel_reads_timetokens->values()
                : null;

            $response[$channel_status][] = $channel_response;
        }

        return response()->json([
            'active' => $response['active'],
            'inactive' => $response['inactive']
        ]);
    }

    public function store(Request $request, Rookie $rookie)
    {
        $validator = Validator::make($request->all(), [
            'message' => ['required', 'string', 'min:2', 'max:120'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $leader_user = $request->user();
        $rookie_user = User::find($rookie->id);

        /*
         * Check if leader can subscribe to that rookie
         */
        if (!$rookie->active){
            return response()->json(['message' => 'Rookie is not active', 'type' => 'generic'], 403);
        }

        if ($rookie->hasBlockedLeader($leader_user->id)){
            return response()->json(['message' => 'This rookie has blocked you', 'type' => 'generic'], 403);
        }

        $leader = Leader::find($leader_user->id);
        if ($leader->blockedRookie($rookie->id)){
            return response()->json(['message' => 'You blocked this rookie', 'type' => 'generic'], 403);
        }

        $channel_exists = PubnubChannel::where('leader_id', $leader->id)->where('rookie_id', $rookie->id)->exists();
        if($channel_exists){
            return response()->json(['message' => "You already connected to this rookie"], 400);
        }

        try {
            $channel = Chat::config($leader->id)->startDirectChat($leader_user,
                $rookie_user,
                null,
                null,
                false,
                true
            );
        } catch (\Exception $exception) {
            Logger::logException($exception);
            return response()->json(['message' => "Unable to create connection, internal server error!"], 500);
        }

        if(!isset($channel)){
            return response()->json(['message' => "Unable to create connection, internal server error!"], 500);
        }

        $message = [
            'type' => 'text',
            'message' => $request->message,
            'user_id' => $leader->id
        ];

        try {
            PubNub::config($leader_user->id)->broadcast($channel->name, $message);
            PubnubMessage::query()->create([
                'type' => 'message',
                'sender_id' => $channel->leader_id,
                'receiver_id' => $channel->rookie_id,
                'channel_id' => $channel->id,
                'sent_at' => now()
            ]);
            $channel->update(['leader_first_message_at' => now()]);

            ChannelReadTimetoken::updateOrCreate([
                'user_id' => $channel->leader_id,
                'channel_id' => $channel->id
            ], [
                'timetoken' => \App\Services\Chat\Utils::getTimetokenFromTimestamp(now()->addSecond()->timestamp)
            ]);
        }catch (\Exception $exception){
            Logger::logException($exception);
            return response()->json(['message' => "Channel created but unable to send the first message! Say hi to the rookie!"], 500);
        }

        if($rookie->is_converter){
            try {
                OrazioHandler::freshSeen($leader->id, 'Connected with converter', true);
            }catch (\Exception $exception){
                Logger::logException($exception);
            }
        }

        try {
            UserProfileUtils::storeOrUpdate($rookie->id);
            UserProfileUtils::storeOrUpdate($leader->id);
            ConnectionOrazioSession::store($leader->id, $rookie->id);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        NotificationUtils::sendNotification($leader->id, 'leader_free_subscription', now(), [
            'ref_user_id' => $rookie->id
        ]);

        NotificationUtils::sendNotification($rookie->id, 'rookie_free_subscription', now(), [
            'ref_user_id' => $leader->id
        ]);

        if(isset($rookie_user->telegram_chat_id)){
            TelegramUtils::sendTelegramNotifications($rookie_user->telegram_chat_id, 'free_connection', [
                'leader_username' => $leader_user->username, 'channel' => $channel
            ], $rookie_user->id);
        }

        try {
            Utils::initSingleChannel($leader_user, $channel, $request->bearerToken());
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return response()->json([], 201);
    }

    public function pause(Request $request, PubnubChannel $pubnubChannel)
    {
        $rookie_user = $request->user();
        if($pubnubChannel->rookie_id !== $rookie_user->id){
            return response()->json(['message' => "This is not your channel"], 403);
        }

        if($pubnubChannel->is_paused){
            return response()->json(['message' => "You dont have an active connection with this leader"], 400);
        }

        $pubnubChannel->update([
            'is_paused' => true,
            'active' => false,
            'was_ever_paused' => true
        ]);

        NotificationUtils::sendNotification($pubnubChannel->leader_id, 'leader_pause_connection', now(), [
            'ref_user_id' => $rookie_user->id
        ]);

        $rookie_user->increment('total_paused_connections');
        User::find($pubnubChannel->leader_id)->increment('total_paused_connections');

        try {
            UserProfileUtils::storeOrUpdate($rookie_user->id);
            UserProfileUtils::storeOrUpdate($pubnubChannel->leader_id);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        $response = PubnubChannelResource::compute(
            $request,
            $pubnubChannel
        )->first();

        return response()->json($response);
    }

    public function resume(Request $request, PubnubChannel $pubnubChannel)
    {
        $rookie = $request->user();
        if($pubnubChannel->rookie_id !== $rookie->id){
            return response()->json(['message' => "This is not your channel"], 403);
        }

        if($pubnubChannel->type !== PubnubChannelTypeEnum::FREE_CONNECTION || !$pubnubChannel->is_paused){
            return response()->json(['message' => "You dont have an active connection with this leader"], 400);
        }

        $pubnubChannel->update([
            'is_paused' => false,
            'active' => true
        ]);

        $response = PubnubChannelResource::compute(
            $request,
            $pubnubChannel
        )->first();

        return response()->json($response);
    }

    public function init(Request $request)
    {
        $user = $request->user();
        $token = $request->bearerToken();

        try {
            Utils::initChat($user, $token);
        }catch (\Exception $exception){
            return response()->json(['message' => 'Chat service is unavailable'], 503);
        }

        return response()->json(['uuid' => $user->id, 'authKey' => $token]);
    }
}
