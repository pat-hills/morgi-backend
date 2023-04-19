<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;
use App\Mixpanel\Utils\UserProfileUtils;
use App\Models\PubnubChannel;
use App\Queries\ConnectionOrazioSessionQueries;
use App\Queries\LeaderSawRookieQueries;
use App\Queries\OrazioSessionQueries;
use App\Queries\PubnubChannelQueries;
use App\Queries\RookieSavedQueries;
use App\Queries\RookieSeenHistoryQueries;
use App\Queries\RookieSeenQueries;
use App\Queries\TransactionQueries;

class EventRebillMorgiSuccess extends MixpanelEventBuilder
{
    public $type = 'rebill_morgi_success';

    public static function config(int $user_id, int $rookie_id, float $amount): void
    {
        $leader_id = $user_id;

        $leader_profile = UserProfileUtils::getUserModelArray($leader_id);
        $rookie_profile = UserProfileUtils::getUserModelArray($rookie_id);

        $channel = PubnubChannel::search($leader_id, $rookie_id)->first();
        $channel_queries = PubnubChannelQueries::config($channel->id);

        $channel_photos_count_rookie = $channel_queries->getAttachmentsCount($rookie_id,'photo');
        $channel_photos_count_leader = $channel_queries->getAttachmentsCount($leader_id, 'photo');
        $channel_videos_count_rookie = $channel_queries->getAttachmentsCount($rookie_id,'video');
        $channel_videos_count_leader = $channel_queries->getAttachmentsCount($leader_id, 'video');
        $messages_count_leader = $channel_queries->getMessagesCount($leader_id);
        $messages_count_rookie = $channel_queries->getMessagesCount($rookie_id);

        $orazio_session = OrazioSessionQueries::getLatestSession($leader_id);

        $frontend_properties = [
            'Morgi Amount' => $amount,
            'Times saw rookie' => LeaderSawRookieQueries::timesLeaderSawRookie($leader_id, $rookie_id),
            'Was saved?' => RookieSavedQueries::isSaved($leader_id, $rookie_id),
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
            'Leader Id' => $leader_id,
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
            'Rookie state' => $rookie_profile['Rookie state'],
            'Carousel calculated at' => $orazio_session->created_at ?? null,
            'Carousel made of' => $orazio_session->session ?? null,
            'Reason For Calculation' => $orazio_session->reason ?? null,
            'Leader type' => $orazio_session->leader_type ?? null,
            'Micromorgi given' => TransactionQueries::micromorgiGiven($leader_id, $rookie_id),
            'Total micromorgi bought' => $leader_profile['Total micromorgi bought'],
            'Gender interested in' => $leader_profile['Gender interested in'],
            'General / Testing' => RookieSeenQueries::isRookieGeneralOrTesting($user_id, $rookie_id),
            'Total Packages Bought' => $leader_profile['Total Packages Bought'],
            'Micromorgi balance' => $leader_profile['Micromorgi balance'],
            'Connection carousel session' => ConnectionOrazioSessionQueries::toString($leader_id, $rookie_id),
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
            (new EventRebillMorgiSuccessForRookie($this->user->id, $this->properties))->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
