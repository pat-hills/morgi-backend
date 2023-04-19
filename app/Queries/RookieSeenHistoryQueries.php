<?php

namespace App\Queries;

use App\Models\RookieSeenHistory;

class RookieSeenHistoryQueries
{
    public static function timesLeaderSawRookie(int $leader_id, int $rookie_id): int
    {
        return RookieSeenHistory::query()
            ->where('rookie_id', $rookie_id)
            ->where('leader_id', $leader_id)
            ->count();
    }
}
