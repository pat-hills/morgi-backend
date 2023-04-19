<?php


namespace App\Orazio;


use App\Models\RookieSeen;

class OrazioHandler
{
    public static function freshSeen(int $leader_id, string $reason = null, bool $force = false): void
    {
        $has_to_run = RookieSeen::query()->where('leader_id', $leader_id)->count() < 50;
        if(!$has_to_run && !$force){
            return;
        }

        try {
            RookieSeen::query()->where('leader_id', $leader_id)->delete();
            (new Orazio($leader_id))->finalize($reason);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}
