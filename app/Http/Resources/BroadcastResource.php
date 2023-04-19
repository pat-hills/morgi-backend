<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;

class BroadcastResource extends Resource
{
    protected $attributes = [
        'id',
        'sender_id',
        'is_goal',
        'display_name',
        'goals',
        'users',
        'sender',
        'messages',
        'image_link',
        'created_at'
    ];
}
