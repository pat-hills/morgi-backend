<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsSendgrid
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

        $sendgrid_signature = $request->header('x-twilio-email-event-webhook-signature');
        if(isset($sendgrid_signature)){
            return $next($request);
        }

        return response()->json([], 404);
    }
}
