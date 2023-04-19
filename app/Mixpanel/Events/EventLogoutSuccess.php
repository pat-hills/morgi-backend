<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;
use App\Queries\UserQueries;

class EventLogoutSuccess extends MixpanelEventBuilder
{
    public $type = 'logout_success';

    public static function config(int $user_id): void
    {
        $user_queries = UserQueries::config($user_id);

        $frontend_properties = [
            'Session time in seconds' => $user_queries->getCurrentSessionTimeInSeconds(),
            'Is system log out' => false
        ];

        try {
            (new self($user_id, $frontend_properties))->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
