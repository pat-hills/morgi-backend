<?php


namespace App\Utils;


use App\Models\ActivityLog;

class ActivityLogsUtils
{
    public static function generateInternalId($user_id)
    {
        $internal_id = rand(10000, 99999999) . $user_id;

        if(!ActivityLog::query()->where('internal_id', $internal_id)->exists()){
            return $internal_id;
        }

        while (ActivityLog::query()->where('internal_id', $internal_id)->first()){
            $internal_id = rand(10000, 99999999) . $user_id;
        }

        return $internal_id;
    }
}
