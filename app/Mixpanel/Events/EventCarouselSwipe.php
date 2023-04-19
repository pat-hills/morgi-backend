<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;
use App\Mixpanel\Utils\UserProfileUtils;
use App\Queries\LeaderSawRookieQueries;
use App\Queries\OrazioSessionQueries;
use App\Queries\RookieSavedQueries;

class EventCarouselSwipe extends MixpanelEventBuilder
{
    public $type = 'carousel_swipe';

    public function __construct(int $user_id, array $frontend_properties)
    {
        parent::__construct($user_id, $frontend_properties);

        // $user_id can be rookie or leader, so detect the right id here
        $rookie_id = $frontend_properties['Rookie Id'] ;
        $leader_id = $user_id;

        $leader_profile = UserProfileUtils::getUserModelArray($leader_id);
        $rookie_profile = UserProfileUtils::getUserModelArray($rookie_id);

        $orazio_session = OrazioSessionQueries::getLatestSession($leader_id);

        $properties = [
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
            'Rookie state' => $rookie_profile['Rookie state'],
            'Carousel calculated at' => $orazio_session->created_at ?? null,
            'Carousel made of' => $orazio_session->session ?? null,
            'Reason For Calculation' => $orazio_session->reason ?? null,
            'Leader type' => $orazio_session->leader_type ?? null,
            'Total micromorgi bought' => $leader_profile['Total micromorgi bought'],
            'Total Packages Bought' => $leader_profile['Total Packages Bought'],
            'Gender interested in' => $leader_profile['Gender interested in'],
        ];

        $this->properties = array_merge($this->properties, $properties);
    }
}
