<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;

class EventConnectTelegramSuccess extends MixpanelEventBuilder
{
    public $type = 'connect_telegram_success';

    public static function config(int $user_id): void
    {
        try {
            (new self($user_id, []))->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
