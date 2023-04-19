<?php

namespace App\Queries;

use App\Models\UserBlock;

class UserBlockQueries
{
    public static function blockCount(int $user_id): int
    {
        return UserBlock::query()
            ->where('from_user_id', $user_id)
            ->count();
    }

    public static function blockedUserCount(int $user_id): int
    {
        return UserBlock::query()
            ->where('to_user_id', $user_id)
            ->count();
    }
}
