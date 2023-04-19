<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;

class EventOpenConnectionSuccessForRookie extends MixpanelEventBuilder
{
    public $type = 'open_connection_success_for_rookie';

    public function __construct(int $user_id, array $frontend_properties)
    {
        $rookie_id = $frontend_properties['Rookie Id'];
        parent::__construct($rookie_id, $frontend_properties);
    }
}
