<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

use Closure;
use \Auth;
use \AuthStatus;

class SiggyCanManage
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
		if( !Auth::loggedIn() )
		{
			return redirect('/');
		}

		$groupID = Auth::$user->groupID;
		$groups = array_keys(Auth::$user->perms());


		View::share('avaliableGroups',$this->getAvaliableGroups());

		list($controller, $action) = explode('@',  $request->route()->getActionName());
		$controller = str_replace('App\Http\Controllers\Manage\\', '', $controller);
		View::share('controllerName', $controller);
		View::share('actionName', $action);
		
		View::share('perms', isset(Auth::$user->perms()[ Auth::$user->groupID ]) ? Auth::$user->perms()[ Auth::$user->groupID ] : []);
		View::share('user', Auth::$user);
		View::share('group', Auth::$user->group);

		return $next($request);
	}
	
	public function getAvaliableGroups()
	{
		$baseSQL = "SELECT g.id, g.name FROM groups g";


		//if NOT AN ADMIN
		if( !Auth::$user->isAdmin() )
		{
			$baseSQL .= " JOIN users_group_acl a ON (g.id = a.group_ID)
							WHERE a.user_id = ".intval( Auth::$user->id );
		}

		$baseSQL .= " ORDER BY g.name ASC";


		$groups = DB::select($baseSQL);

		return $groups;
	}
}