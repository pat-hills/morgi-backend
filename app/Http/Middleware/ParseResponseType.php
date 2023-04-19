<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ParseResponseType
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
        $response_types = ['small', 'extended', 'regular','own'];

        if (!in_array($request->response_type, $response_types)) {
            $request->response_type = 'regular';
        }

        return $next($request);
    }
}
