<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class LeaderSawRookie extends Model
{
    use HasFactory;

    protected $table = 'leaders_saw_rookies';

    protected $fillable = [
        'leader_id',
        'rookie_id',
        'count'
    ];

    public static function storeOrUpdate(int $leader_id, array $rookies_ids): void
    {
        $leaders_saw_rookies = LeaderSawRookie::query()
            ->where('leader_id', $leader_id)
            ->whereIn('rookie_id', $rookies_ids)
            ->get();

        LeaderSawRookie::query()->whereIn('rookie_id', $leaders_saw_rookies->pluck('rookie_id'))->increment('count');

        $leaders_saw_rookies_to_create = [];

        foreach ($rookies_ids as $rookie_id) {

            $leader_saw_rookie = $leaders_saw_rookies->where('rookie_id', $rookie_id)->first();
            if (isset($leader_saw_rookie)) {
                continue;
            }

            $leaders_saw_rookies_to_create[] = [
                'leader_id' => $leader_id,
                'rookie_id' => $rookie_id,
                'count' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }

        LeaderSawRookie::query()->insert($leaders_saw_rookies_to_create);
    }
}
