<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LocalizationMiddleware
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

        $local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') : 'en-US';

        app()->setLocale($local);

        switch ($local) {
            case 'en-US':
            default:
                setlocale(LC_TIME, 'en_US.UTF-8');
                break;
        }

        return $next($request);
    }
}
