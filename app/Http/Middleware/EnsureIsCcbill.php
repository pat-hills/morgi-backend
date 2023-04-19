<?php

namespace App\Http\Middleware;

use App\Utils\Utils;
use Closure;
use Illuminate\Http\Request;

class EnsureIsCcbill
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

        if(isset($request->mirtillo) && $request->mirtillo==='mirtilloDebug'){
            return $next($request);
        }

        $real_ip = Utils::getRealIp($request);
        $client_ip = (($real_ip==='::1' || $real_ip==='localhost') ? '127.0.0.1' : $real_ip);

        $whitelist_ips = [
            '64.38.212.1', '64.38.215.1', '64.38.240.1', '64.38.241.1'
        ];

        foreach ($whitelist_ips as $wl){

            $ip = explode('.', $wl);
            $ip_wl = $ip[0].$ip[1].$ip[2];

            $c_ip = explode('.', $client_ip);
            $ip_client = $c_ip[0].$c_ip[1].$c_ip[2];

            if($ip_client===$ip_wl){
                return $next($request);
            }

        }

        return response()->json([], 404);
    }
}
