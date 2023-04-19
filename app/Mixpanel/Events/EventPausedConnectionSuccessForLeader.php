<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;

class EventPausedConnectionSuccessForLeader extends MixpanelEventBuilder
{
    public $type = 'rookie_paused_your_connection_success';

    public function __construct(int $user_id, array $frontend_properties)
    {
        $leader_id = $frontend_properties['Leader Id'];
        parent::__construct($leader_id, $frontend_properties);
    }
}
