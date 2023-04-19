<?php

namespace App\Queries;

use App\Models\RookieSaved;

class RookieSavedQueries
{
    public static function isSaved(int $leader_id, int $rookie_id): bool
    {
        return RookieSaved::query()
            ->where('rookie_id', $rookie_id)
            ->where('leader_id', $leader_id)
            ->exists();
    }
}
