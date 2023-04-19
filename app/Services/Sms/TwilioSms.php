<?php

namespace App\Services\Sms;

use Twilio\Rest\Client;
use function env;

class TwilioSms
{
    private $client;
    private $receiver_number = null;
    private $sender_name = 'Morgi';

    public function __construct()
    {
        $account_sid = env('TWILIO_SID');
        $auth_token = env('TWILIO_TOKEN');

        try {
            $this->client = new Client($account_sid, $auth_token);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    public function setReceiver(string $receiver_number): TwilioSms
    {
        $this->receiver_number = $receiver_number;
        return $this;
    }

    public function send(string $message): void
    {
        if(!isset($this->receiver_number)){
            throw new \Exception("Receivers numbers is empty");
        }

        try {
            $this->client->messages->create($this->receiver_number, [
                'from' => $this->sender_name,
                'body' => $message
            ]);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
