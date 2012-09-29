<?php defined('SYSPATH') or die('No direct script access.');

require_once APPPATH.'classes/access.php';
require_once APPPATH.'classes/groupUtils.php';
require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';

class FrontController extends Controller 
{
	protected $access=null;
	
	protected $groupData = array();
	protected $trusted = false;
	protected $igb = false;
	
	//new 
	protected $authStatus = false;
	
	protected $charID = 0;
	protected $corpID = 0;
	protected $charName = '';
	
	protected $apiCharInfo = array();
	
	function __construct(Kohana_Request $request, Kohana_Response $response)
	{
		$this->igb = miscUtils::isIGB();
		$this->trusted = miscUtils::getTrust();	
		
		$this->access = new access();
		
		Cookie::$salt = 'y[$e.swbDs@|Gd(ndtUSy^';
		
		$this->authStatus = $this->access->authenticate();
		$this->groupData =& $this->access->accessData;			
		
		parent::__construct($request, $response);
	}
	
	public function siggyredirect($url)
	{
		
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			echo json_encode(array('redirect' => $url));
			die();
		}
		else
		{
			$this->request->redirect($url);
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

		
		if( $this->igb )
		{
				if( $this->authStatus == AuthStatus::GPASSWRONG )
				{
						$this->siggyredirect('/doGroupAuth');
				}
				elseif( $this->authStatus == AuthStatus::APILOGINNOACCESS )
				{
						$this->siggyredirect('/account/noAPIAccess');
				}
				elseif( $this->authStatus == AuthStatus::APILOGINREQUIRED )
				{
						$this->siggyredirect('/account/login');
				}
				else
				{
								$view = View::factory('siggy/accessMessage');
								$view->groupData = $this->groupData;
								$view->trusted = $this->trusted;
								$view->set('offlineMode', false);
								$this->response->body($view);
				}
		}
		else
		{
			if( $this->authStatus == AuthStatus::APILOGINREQUIRED )
			{
					$this->siggyredirect('/account/login');
			}
			elseif ( $this->authStatus == AuthStatus::APILOGININVALID )
			{
					$this->siggyredirect('/account/noAPIAccess');
			}
			else
			{
				//	$this->siggyredirect('/account/noAPIAccess');
			}
		}
    }
	
}
?>