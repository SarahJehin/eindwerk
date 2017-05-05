<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Trainer
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
        if (Auth::user() && min($user_roles) < 40) {
            return $next($request);
        }

        return redirect('/');
    }
}
