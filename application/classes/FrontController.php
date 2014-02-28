<?php defined('SYSPATH') or die('No direct script access.');

require_once APPPATH.'classes/access.php';
require_once APPPATH.'classes/groupUtils.php';
require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';
require_once APPPATH.'classes/formRenderer.php';
require_once APPPATH.'classes/authsystem.php';

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
	
	protected $ajaxRequest = false;
	
	protected $noAutoAuthRedirects = false;
	
	public $template = '';
    
    public $auto_render = true;
	
	function __construct(Kohana_Request $request, Kohana_Response $response)
	{
		$this->igb = miscUtils::isIGB();
		$this->trusted = miscUtils::getTrust();	
		
		$this->access = new access();
		
		Auth::initialize();
		
		$this->authStatus = $this->access->authenticate();
		$this->groupData =& $this->access->accessData;			
        
		
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
			echo json_encode(array('redirect' => $url));
			die();
		}
		else
		{
			HTTP::redirect($url);
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
			
			
			$this->template->igb = $this->igb;
			$this->template->trusted = $this->trusted;
			
                    
			$settings = $this->loadSettings();
			$this->template->settings = $settings;
			
			$this->template->charID = isset($this->groupData['charID']) ? $this->groupData['charID'] : 0;
			$this->template->corpID = isset($this->groupData['corpID']) ?  $this->groupData['corpID'] : 0;
			$this->template->charName = isset($this->groupData['charName']) ? $this->groupData['charName'] : '';
			$this->template->group = $this->groupData;		
			
			if( $this->igb && isset($this->groupData['authMode']) )
			{
				$this->template->apilogin = ( $this->groupData['authMode'] == 2 ? true : false);
			}
			else
			{
				$this->template->apilogin = true;
			}
		}
    }
    
    
    private function loadSettings()
    {
        $settings = array('theme_id' => 0 );
        
        if( isset($this->groupData['charID']) && $this->groupData['charID'] != 0)
        {
			$settings = DB::query(Database::SELECT, "SELECT * FROM character_settings 
							WHERE char_id=:charID")
						->param(':charID', $this->groupData['charID'])->execute()->current();
						
			if( !isset($settings['char_id']) )
			{
				$settings = array('theme_id' => 0 );
			}
        }
        
        return $settings;
    }
    
    public function authCheckAndRedirect()
    {
    
      if( $this->igb )
      {
          if( $this->authStatus == AuthStatus::GPASSWRONG )
          {
              $this->siggyredirect('/access/group_password');
          }
          elseif( $this->authStatus == AuthStatus::APILOGINNOACCESS )
          {
              $this->siggyredirect('/account/noAPIAccess');
          }
          elseif( $this->authStatus == AuthStatus::APILOGINREQUIRED )
          {
              $this->siggyredirect('/account/login');
          }
          elseif( $this->authStatus != AuthStatus::ACCEPTED )
          {
				if( Auth::loggedIn() )
				{
					$this->siggyredirect('/account/noAPIAccess');
				}
				else
				{
					$this->siggyredirect('/pages/accessMessage');
				}
		  }
      }
      else
      {
        if( $this->authStatus == AuthStatus::APILOGINREQUIRED )
        {
            $this->siggyredirect('/pages/welcome');
        }
        elseif ( $this->authStatus == AuthStatus::APILOGININVALID )
        {
            $this->siggyredirect('/account/noAPIAccess');
        }
		else if( $this->authStatus == AuthStatus::APILOGINNOACCESS )
		{
		  $this->siggyredirect('/account/noAPIAccess');
		}
        else
        
        {
          //	$this->siggyredirect('/account/noAPIAccess');
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
?>