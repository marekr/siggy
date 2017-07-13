<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Manage\BaseController;

use Closure;
use App\Facades\Auth;
use \AuthStatus;

class SiggyCanManage
{
	protected function hasAccess( BaseController $controller, string $action )
	{
		if( Auth::user()->admin )
		{
			return TRUE;
		}

		if( !isset( Auth::user()->perms()[ Auth::user()->groupID ] ) )
		{
			return FALSE;
		}
		
		if( isset( $controller->actionAcl[ $action ] ) )
		{
			$perms = Auth::user()->perms()[ Auth::user()->groupID ]->toArray();
			foreach( $perms as $k => $v )
			{
				if( $v == 1 )
				{
					if( in_array( $k, $controller->actionAcl[ $action ] ) )
					{
						return TRUE;
					}
				}
			}
		}
		else
		{
			//unprotected
			return TRUE;
		}

		return FALSE;
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
		if( !Auth::check() )
		{
			return redirect('/');
		}

		$groupID = Auth::user()->groupID;
		$groups = array_keys(Auth::user()->perms());

		if( !count($groups) )
		{
			return redirect('/');
		}

		if( !in_array($groupID, $groups) && !Auth::user()->admin )
		{
			Auth::user()->groupID = $groups[0];
			Auth::user()->save();
		}

		list($controller, $action) = explode('@',  $request->route()->getActionName());
		$controller = str_replace('App\Http\Controllers\Manage\\', '', $controller);


		View::share('avaliableGroups',$this->getAvaliableGroups());
		
		$controller = $request->route()->getController();
		if ( ($controller->authRequired == 'admin' && Auth::user()->isAdmin() === FALSE )
			|| ($controller->authRequired == 'gadmin'
			&& !$this->hasAccess( $controller, $action )
			) )
		{
			return redirect('/');
		}

		View::share('controllerName', $controller);
		View::share('actionName', $action);
		
		View::share('perms', isset(Auth::user()->perms()[ Auth::user()->groupID ]) ? Auth::user()->perms()[ Auth::user()->groupID ] : []);
		View::share('user', Auth::user());
		View::share('group', Auth::user()->group);

		return $next($request);
	}
	
	public function getAvaliableGroups()
	{
		$baseSQL = "SELECT g.id, g.name FROM groups g";

		//if NOT AN ADMIN
		if( !Auth::user()->isAdmin() )
		{
			$baseSQL .= " JOIN users_group_acl a ON (g.id = a.group_ID)
							WHERE a.user_id = ".intval( Auth::user()->id );
		}

		$baseSQL .= " ORDER BY g.name ASC";

		$groups = DB::select($baseSQL);

		return $groups;
	}
}