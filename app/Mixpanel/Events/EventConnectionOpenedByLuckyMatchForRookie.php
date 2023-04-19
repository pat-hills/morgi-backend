<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;

class EventConnectionOpenedByLuckyMatchForRookie extends MixpanelEventBuilder
{
    public $type = 'connection_opened_by_lucky_match_for_rookie';

    public function __construct(int $user_id, array $frontend_properties)
    {
        $rookie_id = $frontend_properties['Rookie Id'];
        parent::__construct($rookie_id, $frontend_properties);
    }
}
