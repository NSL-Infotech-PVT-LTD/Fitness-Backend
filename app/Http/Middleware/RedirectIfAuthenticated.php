<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null) {
//dd(\Auth::check());

        if (\Auth::check()) {
//            $user = \Auth::user();
//            dd($user);
            return redirect('/admin');
        }
        return redirect('/');
//        return $next($request);
    }

}
