<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class YouthBoard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_roles = Auth::user()->roles->pluck('level')->toArray();
        if (Auth::user() && $user_roles && min($user_roles) < 30) {
            return $next($request);
        }

        return redirect('/');
    }
}
