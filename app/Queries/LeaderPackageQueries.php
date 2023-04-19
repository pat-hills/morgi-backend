<?php

namespace App\Queries;

use App\Models\LeaderPackage;

class LeaderPackageQueries
{
    public static function getBoughtPackagesCount(int $leader_id): int
    {
        return LeaderPackage::query()
            ->where('leader_id', $leader_id)
            ->whereNotNull('leader_payment_id')
            ->count();
    }
}
