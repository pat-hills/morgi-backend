<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;
use App\Mixpanel\Utils\UserProfileUtils;

class EventDeleteAccountSuccess extends MixpanelEventBuilder
{
    public $type = 'delete_account_success';

    public static function config(int $user_id): void
    {
        $user_profile = UserProfileUtils::getUserModelArray($user_id);

        $frontend_properties = [
            'Total active paid connections' => $user_profile['Total active paid connections'],
            'Active connections count' => $user_profile['Active connections count'],
        ];

        if($user_profile['Type'] === 'rookie'){
            $frontend_properties['Morgi balance'] = $user_profile['Morgi balance'];
            $frontend_properties['Micromorgi balance'] = $user_profile['Micromorgi balance'];
        }

        try {
            (new self($user_id, $frontend_properties))->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
