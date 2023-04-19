<?php

namespace App\Models;

use App\Queries\RookieSeenQueries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConnectionOrazioSession extends Model
{
    use HasFactory;

    protected $table = 'connection_orazio_sessions';

    protected $fillable = [
        'leader_id',
        'rookie_id',
        'leader_type',
        'source'
    ];

    public static function store(int $leader_id, int $rookie_id): ?ConnectionOrazioSession
    {
        $latest_seen = RookieSeenQueries::getLatestSeen($leader_id, $rookie_id);
        if(!isset($latest_seen)){
            return null;
        }

        return self::create([
            'leader_id' => $leader_id,
            'rookie_id' => $rookie_id,
            'leader_type' => $latest_seen->leader_type,
            'source' => $latest_seen->source,
        ]);
    }

    public function scopeSearch(Builder $query, int $leader_id, int $rookie_id): Builder
    {
        return $query->where('leader_id', $leader_id)->where('rookie_id', $rookie_id);
    }

    public function toString(): ?string
    {
        if(!isset($this->leader_type) && !isset($this->source)){
            return null;
        }

        return "Leader type: {$this->leader_type}" . PHP_EOL . "Rookie source: {$this->source}";
    }
}
