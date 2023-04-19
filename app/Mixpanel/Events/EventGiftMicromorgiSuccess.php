<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;
use App\Mixpanel\Utils\UserProfileUtils;
use App\Models\PubnubChannel;
use App\Models\Transaction;
use App\Queries\ConnectionOrazioSessionQueries;
use App\Queries\LeaderSawRookieQueries;
use App\Queries\PubnubChannelQueries;
use App\Queries\RookieSavedQueries;
use App\Queries\RookieSeenHistoryQueries;

class EventGiftMicromorgiSuccess extends MixpanelEventBuilder
{
    public $type = 'gift_micromorgis_success';

    public static function config(int $user_id, Transaction $transaction): void
    {
        $rookie_id = $transaction->rookie_id;
        $leader_profile = UserProfileUtils::getUserModelArray($user_id);
        $rookie_profile = UserProfileUtils::getUserModelArray($rookie_id);

        $channel = PubnubChannel::search($user_id, $rookie_id)->first();
        $channel_queries = PubnubChannelQueries::config($channel->id);

        $channel_photos_count_rookie = $channel_queries->getAttachmentsCount($rookie_id,'photo');
        $channel_photos_count_leader = $channel_queries->getAttachmentsCount($user_id, 'photo');
        $channel_videos_count_rookie = $channel_queries->getAttachmentsCount($rookie_id,'video');
        $channel_videos_count_leader = $channel_queries->getAttachmentsCount($user_id, 'video');
        $messages_count_leader = $channel_queries->getMessagesCount($user_id);
        $messages_count_rookie = $channel_queries->getMessagesCount($rookie_id);

        $frontend_properties = [
            'Micromorgi Amount' => $transaction->micromorgi,
            'Times saw rookie' => LeaderSawRookieQueries::timesLeaderSawRookie($user_id, $rookie_id),
            'Was saved?' => RookieSavedQueries::isSaved($user_id, $rookie_id),
            'Age' => $rookie_profile['Age'],
            'Beauty score' => $rookie_profile['Beauty score'],
            'Has video?' => $rookie_profile['Has video?'],
            'Photos count' => $rookie_profile['Photos count'],
            'Rookie path' => $rookie_profile['Rookie path'],
            'Rookie subpath' => $rookie_profile['Rookie subpath'],
            'Leader main path' => $leader_profile['Leader main path'],
            'Leader common paths' => $leader_profile['Leader common paths'],
            'Is paid leader?' => $leader_profile['Total paid connections'] > 0,
            'Is converter?' => $rookie_profile['Is converter?'],
            'Rookie Id' => $rookie_id,
            'Leader Id' => $user_id,
            'Chat photos count' => $channel_photos_count_leader + $channel_photos_count_rookie,
            'Chat photos count Rookie' => $channel_photos_count_rookie,
            'Chat photos count Leader' => $channel_photos_count_leader,
            'Chat videos count' => $channel_videos_count_rookie + $channel_videos_count_leader,
            'Chat videos count Rookie' => $channel_videos_count_rookie,
            'Chat videos count Leader' => $channel_videos_count_leader,
            'Chat messages count' => $messages_count_leader + $messages_count_rookie,
            'Chat messages count Rookie' => $messages_count_rookie,
            'Chat messages count Leader' => $messages_count_leader,
            'Is connection paused?' => $channel->is_paused,
            'Was connection ever paused?' => $channel->was_ever_paused,
            'Connection opened at' => $channel->created_at,
            'Avg first reply time in seconds' => $rookie_profile['Avg first reply time in seconds'],
            'Total morgi paid' => $leader_profile['Total morgi paid'],
            'Total morgi paid in dollar' => $leader_profile['Total morgi paid in dollar'],
            'Total micromorgi paid' => $leader_profile['Total micromorgi paid'],
            'Total micromorgi paid in dollar' => $leader_profile['Total micromorgi paid in dollar'],
            'Micromorgi balance' => $leader_profile['Micromorgi balance'],
            'Rookie state' => $rookie_profile['Rookie state'],
            'Total micromorgi bought' => $leader_profile['Total micromorgi bought'],
            'Gender interested in' => $leader_profile['Gender interested in'],
            'Total paused connections' => $leader_profile['Total paused connections'],
            'Total successful paused connections' => $leader_profile['Total successful paused connections'],
            'Total paid connections' => $leader_profile['Total paid connections'],
            'Total active paid connections' => $leader_profile['Total active paid connections'],
            'Total connections' => $leader_profile['Total connections'],
            'Active connections count' => $leader_profile['Active connections count'],
            'Recurring paid connections count' => $leader_profile['Recurring paid connections count'],
            'Total Packages Bought' => $leader_profile['Total Packages Bought'],
            'Connection carousel session' => ConnectionOrazioSessionQueries::toString($user_id, $rookie_id),
        ];

        try {
            (new self($user_id, $frontend_properties))->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    public function store(): void
    {
        try {
            parent::store();
            (new EventGiftMicromorgiSuccessForRookie($this->user->id, $this->properties))->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
