<?php defined('SYSPATH') or die('No direct script access.');


require_once APPPATH.'classes/auth.php';

/**
 * App controller class.
 *
 * @author Mikito Takada
 * @package default
 * @version 1.0
 */
class Controller_Manage extends Controller
{
	public $template = 'template/default';


	public $auto_render = TRUE;

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


		$groups = DB::query(Database::SELECT, $baseSQL)->execute()->as_array();

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

		if ($this->auto_render)
		{

			// only load the template if the template has not been set..
			$this->template = View::factory($this->template);

			$this->template->perms = isset(Auth::$user->perms()[ Auth::$user->groupID ]) ? Auth::$user->perms()[ Auth::$user->groupID ] : array();
			// Initialize empty values
			// Page title
			$this->template->title   = '';
			// Page content
			$this->template->content = '';
			// Styles in header
			$this->template->styles = array();
			// Scripts in header
			$this->template->scripts = array();
			// ControllerName will contain the name of the Controller in the Template
			$this->template->controllerName = $this->request->controller();
			// ActionName will contain the name of the Action in the Template
			$this->template->actionName = $this->request->action();
			// next, it is expected that $this->template->content is set e.g. by rendering a view into it.
		}

		$this->template->avaliableGroups = self::getAvaliableGroups();
	}

	/**
	* The after() method is called after your controller action.
	* In our template controller we override this method so that we can
	* make any last minute modifications to the template before anything
	* is rendered.
	*/
	public function after()
	{
		if ($this->auto_render === TRUE)
		{
			$styles = array( 'css/manage.css' => 'screen');
			$scripts = array();

			$this->template->styles = array_merge( $this->template->styles, $styles );
			$this->template->scripts = array_merge( $this->template->scripts, $scripts );
			// Assign the template as the request response and render it
			$this->response->body( $this->template );
		}
		parent::after();
	}
}
