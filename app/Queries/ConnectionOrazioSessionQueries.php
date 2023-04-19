<?php

namespace App\Queries;

use App\Models\ConnectionOrazioSession;

class ConnectionOrazioSessionQueries
{
    public static function toString(int $leader_id, int $rookie_id): ?string
    {
        $session = ConnectionOrazioSession::search($leader_id, $rookie_id)->first();
        return (isset($session)) ? $session->toString() : null;
    }
}
