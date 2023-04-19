<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;
use App\Mixpanel\Utils\UserProfileUtils;

class EventBuyMicromorgiClick extends MixpanelEventBuilder
{
    public $type = 'buy_micromorgis_click';

    public function __construct(int $user_id, array $frontend_properties)
    {
        parent::__construct($user_id, $frontend_properties);

        $leader_profile = UserProfileUtils::getUserModelArray($user_id);

        $properties = [
            'Leader main path' => $leader_profile['Leader main path'],
            'Leader common paths' => $leader_profile['Leader common paths'],
            'Is paid leader?' => $leader_profile['Total paid connections'] > 0,
            'Total morgi paid' => $leader_profile['Total morgi paid'],
            'Total morgi paid in dollar' => $leader_profile['Total morgi paid in dollar'],
            'Total micromorgi paid' => $leader_profile['Total micromorgi paid'],
            'Total micromorgi paid in dollar' => $leader_profile['Total micromorgi paid in dollar'],
            'Total paid connections' => $leader_profile['Total paid connections'],
            'Total active paid connections' => $leader_profile['Total active paid connections'],
            'Total connections' => $leader_profile['Total connections'],
            'Active connections count' => $leader_profile['Active connections count'],
            'Recurring paid connections count' => $leader_profile['Recurring paid connections count'],
            'Total micromorgi bought' => $leader_profile['Total micromorgi bought'],
            'Gender interested in' => $leader_profile['Gender interested in'],
            'Micromorgi balance' => $leader_profile['Micromorgi balance'],
            'Total paused connections' => $leader_profile['Total paused connections'],
            'Total successful paused connections' => $leader_profile['Total successful paused connections'],
            'Total connections replied' => $leader_profile['Total connections replied'],
            'Total Packages Bought' => $leader_profile['Total Packages Bought'],
        ];

        $this->properties = array_merge($this->properties, $properties);
    }
}
