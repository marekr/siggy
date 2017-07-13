<?php

namespace App\Http\Middleware;

use Closure;
use App\Facades\Auth;
use App\Facades\SiggySession;
use Siggy\AuthStatus;

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

	public function authCheck()
	{
		$session = SiggySession::getFacadeRoot();
		
		if( !$session->character_id  || !Auth::check() )
		{
			return AuthStatus::GUEST;
		}
		
		if( !Auth::user()->validateCorpChar() )
		{
			return AuthStatus::CHAR_CORP_INVALID;
		}

		if( $session->group == null || !$session->validateGroup() )
		{
			$this->authStatus = AuthStatus::GROUP_SELECT_REQUIRED;
			return AuthStatus::GROUP_SELECT_REQUIRED;
		}
		
		$authStatus = AuthStatus::GUEST;
		if( $session->group != null )
		{
			if( count( $session->group->blacklistCharacters() ) &&
						array_key_exists( $session->character_id, $session->group->blacklistCharacters() ) )
			{
				$authStatus = AuthStatus::BLACKLISTED;
			}
			else if( $session->group->password_required )	//group password only?
			{
				$authPassword = Auth::user()->getSavedGroupPassword( $session->group->id );

				if( $authPassword === $session->group->password )
				{
					$authStatus = AuthStatus::ACCEPTED;
				}
				else
				{
					$authStatus = AuthStatus::GPASSWRONG;
				}
			}
			else
			{
				$authStatus = AuthStatus::ACCEPTED;
			}
		}
		else
		{
			$authStatus = AuthStatus::NOACCESS;
		}
		
		return $authStatus;
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

		$authStatus = $this->authCheck();

		if( $authStatus == AuthStatus::GPASSWRONG && $controller != "AccessController" )
		{
			return $this->siggyRedirect($request->ajax(), '/access/group_password');
		}
		elseif( $authStatus == AuthStatus::BLACKLISTED )
		{
			return $this->siggyRedirect($request->ajax(), '/access/blacklisted');
		}
		elseif( $authStatus == AuthStatus::GROUP_SELECT_REQUIRED )
		{
			return $this->siggyRedirect($request->ajax(), '/access/groups');
		}
		elseif( $authStatus != AuthStatus::ACCEPTED && $authStatus != AuthStatus::GPASSWRONG)
		{
			if( Auth::check() )
			{
				return $this->siggyRedirect($request->ajax(), '/account/characters');
			}
			else if( $authStatus == AuthStatus::GUEST )
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