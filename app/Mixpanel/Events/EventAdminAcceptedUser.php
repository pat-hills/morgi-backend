<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;

class EventAdminAcceptedUser extends MixpanelEventBuilder
{
    public $type = 'admin_accepted_user';

    public static function config(int $user_id): void
    {
        try {
            (new self($user_id, []))->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
