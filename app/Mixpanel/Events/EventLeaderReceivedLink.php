<?php

namespace App\Mixpanel\Events;

use App\Mixpanel\Events\Interfaces\MixpanelEventBuilder;

class EventLeaderReceivedLink extends MixpanelEventBuilder
{
    public $type = 'leader_received_link';

    public function __construct(int $user_id, array $frontend_properties)
    {
        $leader_id = $frontend_properties['Leader Id'];
        parent::__construct($leader_id, $frontend_properties);
    }
}
