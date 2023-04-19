<?php

namespace App\Mixpanel\Api;

use Illuminate\Support\Str;

class Events
{
    private $action = 'track';
    private $event;
    private $insert_id; // UUID of the event
    private $distinct_id; // This will be the user_id on mixpanel
    private $properties = []; // Contains new attributes to set

    public static function config(string $event, int $distinct_id): Events
    {
        return new self($event, $distinct_id);
    }

    public function __construct(string $event, int $distinct_id)
    {
        $this->distinct_id = $distinct_id;
        $this->event = $event;
        $this->insert_id = Str::orderedUuid() . Str::uuid() . Str::random();
    }

    public function setProperties(array $properties): Events
    {
        $this->properties = $properties;
        return $this;
    }

    public function create(): void
    {
        try {
            ApiCurl::config()
                ->setAction($this->action)
                ->setBody([$this->getBody()])
                ->exec();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    private function getBody(): array
    {
        $this->properties['token'] = env('MIXPANEL_TOKEN');
        $this->properties['time'] = now()->timestamp;
        $this->properties['$insert_id'] = $this->insert_id;
        $this->properties['distinct_id'] = $this->distinct_id;

        return [
            'event' => $this->event,
            'properties' => $this->properties
        ];
    }
}
