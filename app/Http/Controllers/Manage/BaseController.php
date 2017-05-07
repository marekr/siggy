<?php

namespace App\Http\Controllers\Manage;


use App\Http\Controllers\Controller;
use \Auth;

class BaseController extends Controller {

	public $authRequired = 'gadmin';
	public $actionAcl = [];

	public function __construct()
	{
		/*
		$groupID = Auth::$user->groupID;
		$groups = array_keys(Auth::$user->perms());

		if( count($groups) > 0 && !in_array($groupID, $groups) )
		{
			Auth::$user->groupID = $groups[0];
			Auth::$user->save();
		}

		// Check user auth and role
		$action_name = Request::current()->action();

		if ( ($this->auth_required == 'admin' && Auth::$user->isAdmin() === FALSE )
			|| ($this->auth_required == 'gadmin'
			&& !$this->hasAccess( $action_name )
			) )
		{
			$this->access_required();
		}

		View::share('avaliableGroups',self::getAvaliableGroups());
		View::share('controllerName', $this->request->controller());
		View::share('perms', isset(Auth::$user->perms()[ Auth::$user->groupID ]) ? Auth::$user->perms()[ Auth::$user->groupID ] : []);
		View::share('user', Auth::$user);
		View::share('group', Auth::$user->group);
		View::share('actionName',$this->request->action());*/
	}
}