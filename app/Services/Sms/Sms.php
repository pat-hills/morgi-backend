<?php

namespace App\Services\Sms;

use App\Models\SmsSent;

class Sms
{
    public static function send(int $requesting_user_id, string $receiver_number, string $message): void
    {
        $sms_sent = SmsSent::query()->create([
            'user_id' => $requesting_user_id,
            'telephone' => $receiver_number,
            'message' => $message
        ]);

        try {
            $twilio_sms = new TwilioSms();
            $twilio_sms->setReceiver($receiver_number)->send($message);
            $sms_sent->update(['is_sent' => true]);
        }catch (\Exception $exception){
            $sms_sent->update(['error' => $exception->getMessage()]);
            throw new \Exception("Unable to send SMS");
        }
    }
}
