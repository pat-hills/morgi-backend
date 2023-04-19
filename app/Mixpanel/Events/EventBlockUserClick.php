<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;
use App\Models\PubnubChannel;
use App\Queries\ComplaintQueries;
use App\Queries\ConnectionOrazioSessionQueries;
use App\Queries\PubnubChannelQueries;
use App\Queries\TransactionQueries;
use App\Queries\UserBlockQueries;

class EventBlockUserClick extends MixpanelEventBuilder
{
    public $type = 'block_user_click';

    public function __construct(int $user_id, array $frontend_properties)
    {
        parent::__construct($user_id, $frontend_properties);

        // $user_id can be rookie or leader, so detect the right id here
        $rookie_id = $frontend_properties['Rookie Id'] ?? $user_id;
        $leader_id = $frontend_properties['Leader Id'] ?? $user_id;

        $to_user_id = ($user_id === $rookie_id) ? $leader_id : $rookie_id;

        $channel = PubnubChannel::search($leader_id, $rookie_id)->first();
        $channel_queries = PubnubChannelQueries::config($channel->id);

        $channel_photos_count_rookie = $channel_queries->getAttachmentsCount($rookie_id,'photo');
        $channel_photos_count_leader = $channel_queries->getAttachmentsCount($leader_id, 'photo');
        $channel_videos_count_rookie = $channel_queries->getAttachmentsCount($rookie_id,'video');
        $channel_videos_count_leader = $channel_queries->getAttachmentsCount($leader_id, 'video');
        $messages_count_leader = $channel_queries->getMessagesCount($leader_id);
        $messages_count_rookie = $channel_queries->getMessagesCount($rookie_id);

        $properties = [
            'Chat photos count' => $channel_photos_count_leader + $channel_photos_count_rookie,
            'Chat photos count Rookie' => $channel_photos_count_rookie,
            'Chat photos count Leader' => $channel_photos_count_leader,
            'Chat videos count' => $channel_videos_count_rookie + $channel_videos_count_leader,
            'Chat videos count Rookie' => $channel_videos_count_rookie,
            'Chat videos count Leader' => $channel_videos_count_leader,
            'Chat messages count' => $messages_count_leader + $messages_count_rookie,
            'Chat messages count Rookie' => $messages_count_rookie,
            'Chat messages count Leader' => $messages_count_leader,
            'Connection opened at' => $channel->created_at,
            'Micromorgi given' => TransactionQueries::micromorgiGiven($leader_id, $rookie_id),
            'Morgi given' => TransactionQueries::morgiGiven($leader_id, $rookie_id),
            'Dollars given' => TransactionQueries::dollarsGiven($leader_id, $rookie_id),
            'Did user report on chat?' => ComplaintQueries::getReportsCount($user_id, $to_user_id),
            'Is active paid connection?' => $channel_queries->isActivePaidConnection(),
            'Last morgi gift time' => TransactionQueries::latestTransactionByType($leader_id, $rookie_id, 'gift')->created_at ?? null,
            'Last micromorgi gift time' => TransactionQueries::latestTransactionByType($leader_id, $rookie_id, 'chat')->created_at ?? null,
            'How many blocked me' => UserBlockQueries::blockedUserCount($user_id),
            'How many I blocked' => UserBlockQueries::blockCount($user_id),
            'Connection carousel session' => ConnectionOrazioSessionQueries::toString($leader_id, $rookie_id),
        ];

        $this->properties = array_merge($this->properties, $properties);
    }
}
