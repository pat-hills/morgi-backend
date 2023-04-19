<?php

namespace App\Queries;

use App\Models\Transaction;

class TransactionQueries
{
    public static function micromorgiGiven(int $leader_id, int $rookie_id): float
    {
        return Transaction::query()
            ->where('leader_id', $leader_id)
            ->where('rookie_id', $rookie_id)
            ->where('type', 'chat')
            ->whereNotNull('micromorgi')
            ->sum('micromorgi');
    }

    public static function morgiGiven(int $leader_id, int $rookie_id): float
    {
        return Transaction::query()
            ->where('leader_id', $leader_id)
            ->where('rookie_id', $rookie_id)
            ->where('type', 'gift')
            ->whereNotNull('morgi')
            ->sum('morgi');
    }

    public static function dollarsGiven(int $leader_id, int $rookie_id): float
    {
        return Transaction::query()
            ->where('leader_id', $leader_id)
            ->where('rookie_id', $rookie_id)
            ->whereIn('type', ['gift', 'chat'])
            ->whereNotNull('dollars')
            ->sum('dollars');
    }

    public static function latestTransactionByType(int $leader_id, int $rookie_id, string $type): ?Transaction
    {
        return Transaction::where('leader_id', $leader_id)
            ->where('rookie_id', $rookie_id)
            ->where('type', $type)
            ->latest()
            ->first();
    }
}
