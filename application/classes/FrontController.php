<?php defined('SYSPATH') or die('No direct script access.');

require_once APPPATH.'classes/groupUtils.php';
require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';
require_once APPPATH.'classes/formRenderer.php';


class FrontController extends Controller {
	protected $groupData = array();
	protected $trusted = false;

	//new
	protected $authStatus = false;

	protected $charID = 0;
	protected $corpID = 0;
	protected $charName = '';

	protected $apiCharInfo = array();

	protected $ajaxRequest = false;

	protected $noAutoAuthRedirects = false;

	public $template = '';

	public $auto_render = true;

	function __construct(Kohana_Request $request, Kohana_Response $response)
	{
		Auth::initialize();
		$this->authStatus = Auth::authenticate();

		if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' )
		{
			$this->ajaxRequest = true;
		}

		parent::__construct($request, $response);
	}

	public function siggyAccessGranted()
	{
		if(	 $this->authStatus != AuthStatus::ACCEPTED )
		{
			return FALSE;
		}
		return TRUE;
	}

	public function siggyredirect($url)
	{
		if( $this->ajaxRequest )
		{
			echo json_encode(array('redirect' => ltrim($url,'/')));
			die();
		}
		else
		{
			HTTP::redirect($url);
		}
	}

	public function validateCSRF()
	{
		$csrf = isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : ( isset($_POST['_token']) ? $_POST['_token'] : '');

		if( Auth::$session->sessionData['csrf_token'] != $csrf )
		{
			http_response_code(403);
			$this->siggyredirect('/');
		}
	}

	public function before()
	{
		//we are not caching any of our pages insanely
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1

		$offline = false;
		if( $offline == true )
		{
			$this->siggyredirect('/offline');
		}

		if( !$this->noAutoAuthRedirects  )
		{
			$this->authCheckAndRedirect();
		}

		if( !$this->ajaxRequest && $this->template != '' )
		{
			$this->template = View::factory( $this->template );


			$settings = $this->loadSettings();
			$this->template->settings = $settings;

			$this->template->group = Auth::$session->group;
			$this->template->accessData = Auth::$session->accessData;
					

			$this->template->apilogin = Auth::loggedIn();
		}
	}

	protected function loadSettings()
	{
		$default_settings = ['theme_id' => 0,'combine_scan_intel' => 0, 'zoom' => '1.0', 'language' => 'en', 'default_activity' => '' ];

		if( Auth::$session->charID != 0)
		{
				$settings = DB::query(Database::SELECT, "SELECT * FROM character_settings
								WHERE char_id=:charID")
							->param(':charID', Auth::$session->charID)
							->execute()
							->current();

				if( isset($settings['char_id']) )
				{
					if( $settings['language'] != 'en' )
					{
						i18n::lang($settings['language']);
					}

					return $settings;
				}
		}

		return $default_settings;
	}

	public function authCheckAndRedirect()
	{
		if( $this->authStatus == AuthStatus::GPASSWRONG )
		{
			$this->siggyredirect('/access/group_password');
		}
		elseif( $this->authStatus == AuthStatus::BLACKLISTED )
		{
			$this->siggyredirect('/access/blacklisted');
		}
		elseif( $this->authStatus != AuthStatus::ACCEPTED )
		{
			if( Auth::loggedIn() )
			{
				$this->siggyredirect('/account/characters');
			}
			else if( $this->authStatus == AuthStatus::GUEST )
			{
				$this->siggyredirect('/pages/welcome');
			}
			else
			{
				$this->siggyredirect('/pages/no-group-access');
			}
		}
	}

	public function after()
	{
		if( !$this->ajaxRequest && $this->template != '' && $this->auto_render )
		{
			$this->response->body($this->template->render());
		}
	}
}
