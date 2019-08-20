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
    public function handle($request, Closure $next) {
        if (\Auth::check() === false) {
            return redirect('/');
        }
        if (strpos($request->path(), 'salon-admin') !== false):
//            dd(\Auth::user());
            if (\Auth::user()->hasRole('admin') === false):
                \Auth::logout();
                return redirect()->route('salon-admin.login')->withErrors(['email' => 'Only Salon admin are allowed to login ']);
            endif;
        elseif (strpos($request->path(), 'admin') !== false):
            if (\Auth::user()->hasRole('super admin') === false):
                \Auth::logout();
                return redirect()->route('admin.login')->withErrors(['email' => 'Only Super admin are allowed to login ']);
            endif;
        endif;
        return $next($request);
    }

}
