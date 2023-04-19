<?php

namespace App\Queries;

use App\Models\OrazioSession;

class OrazioSessionQueries
{
    public static function getLatestSession(int $leader_id): ?OrazioSession
    {
        return OrazioSession::where('leader_id', $leader_id)->latest()->first();
    }
}
