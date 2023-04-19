<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsOperator
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

        $user= Auth::user();
        if($user->type!="operator" || $user->type!="admin"){
            return response()->json(['message' => "You are not a operator!"], 403);
        }

        return $next($request);
    }
}
