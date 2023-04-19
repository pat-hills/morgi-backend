<?php

namespace App\Http\Middleware;

use App\Services\Chat\Utils;
use Closure;
use Illuminate\Http\Request;

class SyncPubnub
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
        $token = $request->bearerToken();

        if(isset($user) && !empty($token)){
            try {
                Utils::initChat($user, $token);
            }catch (\Exception $exception){
            }
        }

        return $next($request);
    }
}
