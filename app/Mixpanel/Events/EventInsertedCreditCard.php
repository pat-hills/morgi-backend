<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;

class EventInsertedCreditCard extends MixpanelEventBuilder
{
    public $type = 'inserted_credit_card';

    public static function config(int $user_id, float $dollars = 0): void
    {
        $frontend_properties = [
            'Dollars Amount' => $dollars,
        ];

        try {
            (new self($user_id, $frontend_properties))->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
