<?php

namespace App\Queries;

use App\Models\Complaint;

class ComplaintQueries
{
    public static function getReportsCount(int $from_user_id, int $to_user_id): int
    {
        return Complaint::query()
            ->where('reported_by', $from_user_id)
            ->where('user_reported', $to_user_id)
            ->where('by_system', false)
            ->count();
    }
}
