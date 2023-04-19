<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;
use App\Mixpanel\Utils\UserProfileUtils;
use App\Models\PubnubChannel;
use App\Queries\ConnectionOrazioSessionQueries;
use App\Queries\PubnubChannelQueries;

class EventPauseConnectionClick extends MixpanelEventBuilder
{
    public $type = 'pause_connection_click';

    public function __construct(int $user_id, array $frontend_properties)
    {
        parent::__construct($user_id, $frontend_properties);

        $rookie_id = $user_id;
        $leader_id = $frontend_properties['Leader Id'];

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

        $properties = [
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
            'Is paid connection?' => $channel_queries->isActivePaidConnection(),
            'Connection carousel session' => ConnectionOrazioSessionQueries::toString($leader_id, $rookie_id),
        ];

        $this->properties = array_merge($this->properties, $properties);
    }
}
