<?php

namespace App\Http\Middleware;

use Closure;
use \Auth;

class AuthenticateSession
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
		Auth::initialize();
		Auth::authenticate();

        return $next($request);
    }
}