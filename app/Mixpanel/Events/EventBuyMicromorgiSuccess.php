<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;
use App\Mixpanel\Utils\UserProfileUtils;

class EventBuyMicromorgiSuccess extends MixpanelEventBuilder
{
    public $type = 'buy_micromorgi_success';

    public static function config(int $user_id, float $amount, bool $is_one_click_payment): void
    {
        $leader_profile = UserProfileUtils::getUserModelArray($user_id);

        $frontend_properties = [
            'Micromorgi Amount' => $amount,
            'Leader main path' => $leader_profile['Leader main path'],
            'Leader common paths' => $leader_profile['Leader common paths'],
            'Is paid leader?' => $leader_profile['Total paid connections'] > 0,
            'Total morgi paid' => $leader_profile['Total morgi paid'],
            'Total morgi paid in dollar' => $leader_profile['Total morgi paid in dollar'],
            'Total micromorgi paid' => $leader_profile['Total micromorgi paid'],
            'Total micromorgi paid in dollar' => $leader_profile['Total micromorgi paid in dollar'],
            'Is one click pay?' => $is_one_click_payment,
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

        try {
            (new self($user_id, $frontend_properties))->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
