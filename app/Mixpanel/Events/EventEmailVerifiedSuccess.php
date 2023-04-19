<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;
use App\Mixpanel\Utils\UserProfileUtils;

class EventEmailVerifiedSuccess extends MixpanelEventBuilder
{
    public $type = 'email_verified_success';

    public static function config(int $user_id): void
    {
        $user_profile = UserProfileUtils::getUserModelArray($user_id);
        $properties = [
            'Type' => $user_profile['Type'],
        ];

        try {
            (new self($user_id, $properties))->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
