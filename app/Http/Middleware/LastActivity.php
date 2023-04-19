<?php

namespace App\Http\Middleware;

use App\Models\Rookie;
use App\Models\UserActivityHistory;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class LastActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        $should_update = !isset($user->last_activity_at) || (strtotime($user->last_activity_at) < Carbon::now()->subMinutes(3)->timestamp);
        if(!$should_update){
            return $next($request);
        }

        $user->last_activity_at = Carbon::now();
        $user->timestamps = false;
        $user->can_receive_telegram_message = true;
        $user->save();

        UserActivityHistory::query()->create([
            'user_id' => $user->id
        ]);

        return $next($request);
    }
}
