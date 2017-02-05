<?php defined('SYSPATH') or die('No direct script access.');

use Illuminate\Database\Capsule\Manager as DB;

use Siggy\View;

/**
 * App controller class.
 *
 * @author Mikito Takada
 * @package default
 * @version 1.0
 */
class Controller_Manage extends Controller
{
	public $auth_required = FALSE;
	public $secure_actions = array();

	public function access_required()
	{
		HTTP::redirect('/');
	}

	public function login_required()
	{
		HTTP::redirect('account/login?bounce=manage');
	}

	public function __construct(Kohana_Request $request, Kohana_Response $response)
	{
		Auth::initialize();

		parent::__construct($request, $response);
	}

	static function getAvaliableGroups()
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

	protected function hasAccess( $action )
	{
		if( Auth::$user->admin )
		{
			return TRUE;
		}

		if( !isset( Auth::$user->perms()[ Auth::$user->groupID ] ) )
		{
			return FALSE;
		}

		if( isset( $this->secure_actions[ $action ] ) )
		{
			$perms = Auth::$user->perms()[ Auth::$user->groupID ]->toArray();
			foreach( $perms as $k => $v )
			{
				if( $v == 1 )
				{
					if( in_array( $k, $this->secure_actions[ $action ] ) )
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

	public function before()
	{
		// Execute parent::before first
		parent::before();

		if( !Auth::loggedIn() )
		{
			$this->login_required();
		}

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
		View::share('actionName',$this->request->action());
	}

	/**
	* The after() method is called after your controller action.
	* In our template controller we override this method so that we can
	* make any last minute modifications to the template before anything
	* is rendered.
	*/
	public function after()
	{
		parent::after();
	}
}
