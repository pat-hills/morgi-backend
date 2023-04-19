<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MorgiPulseEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pulse_value;

    public function __construct($pulse_value)
    {
        $this->pulse_value = $pulse_value;
    }

    public function broadcastOn()
    {
        return new Channel('morgi-pulse');
    }

    public function broadcastWith()
    {
        return [
            'type' => 'morgi_pulse',
            'value' => $this->pulse_value
        ];
    }

    public function broadcastAs()
    {
        return 'morgi_pulse';
    }
}
