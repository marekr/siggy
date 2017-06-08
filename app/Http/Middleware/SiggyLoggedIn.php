<?php

namespace App\Http\Middleware;

use Closure;
use \Auth;
use \AuthStatus;

class SiggyLoggedIn
{
	public function siggyRedirect($ajax, $url)
	{
		if( $ajax )
		{
			return response()->json(['redirect' => ltrim($url,'/')]);
		}
		else
		{
            return redirect($url);
		}
	}

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		if( !Auth::loggedIn() )
		{
			return $this->siggyRedirect($request->ajax(), '/account/login');
		}

		return $next($request);
	}
}