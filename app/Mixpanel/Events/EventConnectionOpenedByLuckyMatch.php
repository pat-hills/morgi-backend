<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;
use App\Mixpanel\Utils\UserProfileUtils;
use App\Queries\LeaderSawRookieQueries;
use App\Queries\RookieSavedQueries;

class EventConnectionOpenedByLuckyMatch extends MixpanelEventBuilder
{
    public $type = 'connection_opened_by_lucky_match';

    public static function config(int $leader_id, int $rookie_id): void
    {
        $leader_profile = UserProfileUtils::getUserModelArray($leader_id);
        $rookie_profile = UserProfileUtils::getUserModelArray($rookie_id);

        $frontend_properties = [
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
            'Avg first reply time in seconds' => $rookie_profile['Avg first reply time in seconds'],
            'Total morgi paid' => $leader_profile['Total morgi paid'],
            'Total morgi paid in dollar' => $leader_profile['Total morgi paid in dollar'],
            'Total micromorgi paid' => $leader_profile['Total micromorgi paid'],
            'Total micromorgi paid in dollar' => $leader_profile['Total micromorgi paid in dollar'],
            'Total Packages Bought' => $leader_profile['Total Packages Bought'],
            'Micromorgi balance' => $rookie_profile['Micromorgi balance'],
            'Rookie state' => $rookie_profile['Rookie state'],
            'Gender interested in' => $leader_profile['Gender interested in'],
            'Connection number' => $leader_profile['Total connections'],
        ];

        try {
            (new self($leader_id, $frontend_properties))->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    public function store(): void
    {
        try {
            parent::store();
            (new EventConnectionOpenedByLuckyMatchForRookie($this->user->id, $this->properties))->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
