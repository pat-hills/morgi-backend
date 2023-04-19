<?php

namespace App\Queries;

use App\Enums\RookieSeenEnum;
use App\Models\RookieSeen;
use App\Models\RookieSeenHistory;

class RookieSeenQueries
{
    public static function isRookieGeneralOrTesting(int $leader_id, int $rookie_id): string
    {
        $rookie_seen = RookieSeen::query()->where('leader_id', $leader_id)
            ->where('rookie_id', $rookie_id)
            ->latest()
            ->first();

        if(!isset($rookie_seen)){
            return 'Testing';
        }

        return (in_array($rookie_seen->source, RookieSeenEnum::TESTING_SOURCES, true)) ? 'Testing' : 'General';
    }

    public static function getLatestSeen(int $leader_id, int $rookie_id)
    {
        $rookie_seen = RookieSeen::query()->where('leader_id', $leader_id)
            ->where('rookie_id', $rookie_id)
            ->latest()
            ->first();

        if(isset($rookie_seen)){
            return $rookie_seen;
        }

        return RookieSeenHistory::query()->where('leader_id', $leader_id)
            ->where('rookie_id', $rookie_id)
            ->latest()
            ->first();
    }
}
