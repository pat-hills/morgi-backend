<?php

namespace App\Mixpanel\Api;

class UserProfiles
{
    private $action = 'engage';
    private $distinct_id; // This will be the user_id on mixpanel
    private $properties = []; // Contains new attributes to set

    public static function config(int $distinct_id): UserProfiles
    {
        return new self($distinct_id);
    }

    public function __construct(int $distinct_id)
    {
        $this->distinct_id = $distinct_id;
    }

    public function setProperties(array $properties): UserProfiles
    {
        $this->properties = $properties;
        return $this;
    }

    public function storeOrUpdate(): void
    {
        $this->action .= '#profile-set';

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
        return [
            '$token' => env('MIXPANEL_TOKEN'),
            '$distinct_id' => $this->distinct_id,
            '$set' => $this->properties
        ];
    }
}
