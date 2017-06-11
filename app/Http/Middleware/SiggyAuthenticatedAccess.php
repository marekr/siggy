<?php

namespace App\Http\Middleware;

use Closure;
use \Auth;
use \AuthStatus;

class SiggyAuthenticatedAccess
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
		list($controller, $action) = explode('@',  $request->route()->getActionName());
		$controller = str_replace('App\Http\Controllers\\', '', $controller);

		if( Auth::$authStatus == AuthStatus::GPASSWRONG && $controller != "AccessController" )
		{
			return $this->siggyRedirect($request->ajax(), '/access/group_password');
		}
		elseif( Auth::$authStatus == AuthStatus::BLACKLISTED )
		{
			return $this->siggyRedirect($request->ajax(), '/access/blacklisted');
		}
		elseif( Auth::$authStatus == AuthStatus::GROUP_SELECT_REQUIRED )
		{
			return $this->siggyRedirect($request->ajax(), '/access/groups');
		}
		elseif( Auth::$authStatus != AuthStatus::ACCEPTED && Auth::$authStatus != AuthStatus::GPASSWRONG)
		{
			if( Auth::loggedIn() )
			{
				return $this->siggyRedirect($request->ajax(), '/account/characters');
			}
			else if( \Auth::$authStatus == AuthStatus::GUEST )
			{
				return $this->siggyRedirect($request->ajax(), '/pages/welcome');
			}
			else
			{
				return $this->siggyRedirect($request->ajax(), '/pages/no-group-access');
			}
		}

        return $next($request);
    }
}