<?php


namespace App\Utils;


use App\Events\MorgiPulseEvent;
use App\Events\UserEvent;
use App\Models\Notification;
use App\Models\NotificationType;
use App\Models\Transaction;

class NotificationUtils
{
    public static function sendNotification($user_id, $notification_type, $event_at, $misc = null)
    {
        $notification_type_id = NotificationType::query()
            ->where('type', $notification_type)
            ->first()
            ->id;

        $notification_attribute = [
            'user_id' => $user_id,
            'notification_type_id' => $notification_type_id,
            'event_at' => $event_at,
        ];

        if(isset($misc)){
            $notification_attribute = array_merge($notification_attribute, $misc);
        }

        $notification = Notification::create($notification_attribute);
        event(new UserEvent($user_id, $notification));
    }

    public static function morgiPulse()
    {
        $interval_in_minutes = env('pulse_interval_in_min', 10);
        $pulse_max_transactions = env('pulse_max_transactions', 20);

        $coefficient = $pulse_max_transactions / $interval_in_minutes;

        $ranges = [
            1 => [0, $coefficient],
            2 => [$coefficient, $coefficient*2],
            3 => [$coefficient*2, $coefficient*3],
            4 => [$coefficient*3, $coefficient*4],
            5 => [$coefficient*4, $coefficient*5]
        ];

        $num_transactions = Transaction::query()->select('id')
            ->whereIn('type', ['gift', 'chat'])
            ->where('created_at', '>=', now()->subMinutes($interval_in_minutes))
            ->whereNull('refund_type')
            ->count();

        foreach ($ranges as $key => $range){
            if($num_transactions >= $range[0] && $num_transactions <= $range[1]){
                event(new MorgiPulseEvent($key));
                return;
            }
        }

        event(new MorgiPulseEvent(5));
    }
}
