<?php

namespace App\Queries;

use App\Models\LeaderSawRookie;

class LeaderSawRookieQueries
{
    public static function timesLeaderSawRookie(int $leader_id, int $rookie_id): int
    {
        $leader_saw_rookie = LeaderSawRookie::where('leader_id', $leader_id)
            ->where('rookie_id', $rookie_id)
            ->first();

        return (isset($leader_saw_rookie)) ? $leader_saw_rookie->count : 0;
    }
}
