<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\ChatTopic; 
use App\Models\ChatTopicUsers; 

class ChatTopicResource extends Resource
{
    public function small(): ChatTopicResource
    {
        $this->attributes = [
            'id', 'name', 'key_name','users_chat_topics_count'
        ];

        return $this;
    }

    public function regular(): ChatTopicResource
    {
        $this->small();
        return $this;
    }

    public function extended(): ChatTopicResource
    {
        $this->regular();
        return $this;
    }
}
