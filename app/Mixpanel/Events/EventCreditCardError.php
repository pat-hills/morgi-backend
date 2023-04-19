<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;

class EventCreditCardError extends MixpanelEventBuilder
{
    public $type = 'credit_card_error';

    public static function config(int $user_id, float $dollars = 0, string $error_type = 'Card issue', string $error = null): void
    {
        $frontend_properties = [
            'Dollars Amount' => $dollars,
            'Error type' => $error_type,
            'Error' => $error
        ];

        try {
            (new self($user_id, $frontend_properties))->store();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
