<?php

namespace App\Utils;

use App\Models\PubnubChannel;

class FreeConnectionChannelUtils
{
    public static function pausedChannelExists(int $leader_id, int $rookie_id): bool
    {
        $channel = PubnubChannel::search($leader_id, $rookie_id)->first();
        if(!isset($channel)){
            return false;
        }

        return $channel->is_paused;
    }
}
