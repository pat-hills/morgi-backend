<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;
use App\Mixpanel\Utils\UserProfileUtils;
use App\Queries\ConnectionOrazioSessionQueries;
use App\Queries\LeaderSawRookieQueries;
use App\Queries\OrazioSessionQueries;
use App\Queries\RookieSavedQueries;
use App\Queries\RookieSeenQueries;

class EventOpenConnectionSuccess extends MixpanelEventBuilder
{
    public $type = 'open_connection_success';

    public function __construct(int $user_id, array $frontend_properties)
    {
        parent::__construct($user_id, $frontend_properties);

        $rookie_id = $frontend_properties['Rookie Id'];
        $leader_profile = UserProfileUtils::getUserModelArray($user_id);
        $rookie_profile = UserProfileUtils::getUserModelArray($rookie_id);

        $orazio_session = OrazioSessionQueries::getLatestSession($user_id);

        $properties = [
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
            'Avg first reply time in seconds' => $rookie_profile['Avg first reply time in seconds'],
            'Total morgi paid' => $leader_profile['Total morgi paid'],
            'Total morgi paid in dollar' => $leader_profile['Total morgi paid in dollar'],
            'Total micromorgi paid' => $leader_profile['Total micromorgi paid'],
            'Total micromorgi paid in dollar' => $leader_profile['Total micromorgi paid in dollar'],
            'Total Packages Bought' => $leader_profile['Total Packages Bought'],
            'Micromorgi balance' => $rookie_profile['Micromorgi balance'],
            'Rookie state' => $rookie_profile['Rookie state'],
            'Carousel calculated at' => $orazio_session->created_at ?? null,
            'Carousel made of' => $orazio_session->session ?? null,
            'Reason For Calculation' => $orazio_session->reason ?? null,
            'Leader type' => $orazio_session->leader_type ?? null,
            'Gender interested in' => $leader_profile['Gender interested in'],
            'Connection number' => $leader_profile['Total connections'],
            'General / Testing' => RookieSeenQueries::isRookieGeneralOrTesting($user_id, $rookie_id),
            'Connection carousel session' => ConnectionOrazioSessionQueries::toString($user_id, $rookie_id),
        ];

        $this->properties = array_merge($this->properties, $properties);
    }

    public function store(): void
    {
        try {
            parent::store();
            (new EventOpenConnectionSuccessForRookie($this->user->id, $this->properties))->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
