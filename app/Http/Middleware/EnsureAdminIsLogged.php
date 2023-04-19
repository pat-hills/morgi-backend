<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class EnsureAdminIsLogged
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
        /** @var  $user User */
        if(empty(session('user')) || !in_array(session('user')->type, ['admin', 'operator'])){
            return redirect('/login');
        }

        return $next($request);
    }
}
