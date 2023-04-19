<?php

namespace App\Events;

use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class UserEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $notification;

    public function __construct(int $user_id, Notification $notification)
    {
        $this->user_id = $user_id;

        $fake_request = new Request();
        $this->notification = NotificationResource::compute($fake_request, $notification, 'small')->first();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->user_id);
    }

    public function broadcastWith()
    {
        return [
            'type' => 'user_notification',
            'data' => [$this->notification]
        ];
    }

    public function broadcastAs()
    {
        return 'user_notification';
    }
}
