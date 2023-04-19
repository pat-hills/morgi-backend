<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;
use App\Queries\UserQueries;

class EventLoginSuccess extends MixpanelEventBuilder
{
    public $type = 'login_success';

    public static function config(int $user_id, string $source): void
    {
        $user_queries = UserQueries::config($user_id);

        $frontend_properties = [
            'Last login at' => $user_queries->getLastLoginAt(),
            'Login number' => $user_queries->getLoginCount() + 1,
            'Logged in with' => $source
        ];

        try {
            (new self($user_id, $frontend_properties))->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
