<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;
use App\Mixpanel\Utils\UserProfileUtils;

class EventSignupSuccess extends MixpanelEventBuilder
{
    public $type = 'signup_success';

    public static function config(int $user_id): void
    {
        $user_profile = UserProfileUtils::getUserModelArray($user_id);
        try {
            (new self($user_id, $user_profile))->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
