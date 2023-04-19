<?php

namespace App\Mixpanel\Events\Interfaces;

use App\Mixpanel\Api\Events;
use App\Models\User;

class MixpanelEventBuilder implements MixpanelEvent
{
    public $type;
    public $user;
    public $properties = [];

    public function __construct(int $user_id, array $frontend_properties)
    {
        $this->user = User::find($user_id);
        if(!empty($frontend_properties)){
            $this->properties = array_merge($this->properties, $frontend_properties);
        }
    }

    public function store(): void
    {
        try {
            Events::config($this->type, $this->user->id)->setProperties($this->properties)->create();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
