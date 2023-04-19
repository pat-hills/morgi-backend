<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;

class EventDisconnectTelegramSuccess extends MixpanelEventBuilder
{
    public $type = 'disconnect_telegram_success';

    public static function config(int $user_id, bool $is_system_click): void
    {
        $frontend_properties = [
            'Is system kick?' => $is_system_click
        ];

        try {
            (new self($user_id, $frontend_properties))->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

}
