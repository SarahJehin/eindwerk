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
        //$user_roles = Auth::user()->roles->pluck('level')->toArray();
        $trainer_roles_array = array();
        for($i = 30; $i < 40; $i ++) {
            array_push($trainer_roles_array, $i);
        }
        //dd($trainer_roles_array);
        $user_has_trainer_role = Auth::user()->roles->whereIn('level', $trainer_roles_array);
        //dd($user_has_trainer_role);
        if (Auth::user() && !$user_has_trainer_role->isEmpty()) {
            return $next($request);
        }

        return redirect('/');
    }
}
