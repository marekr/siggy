<?php defined('SYSPATH') or die('No direct script access.');

require_once APPPATH.'classes/FrontController.php';

class Controller_Access extends FrontController 
{
	public $groupData = array();
	public $trusted = false;
	
	public $template = 'template/main';
	
	public function action_group_password()
	{
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		if( !$this->trusted )
		{
			return;
		}
		
		$view = View::factory('access/groupPassword');
		$view->groupData = $this->groupData;
		$view->trusted = $this->trusted;
		$view->wrongPass = false;
		$this->template->siggyMode = false;

		//load header tools
		$this->template->headerTools = '';		
		
		$groupID = intval($this->groupData['groupID']);
		
		if( isset($_POST['authPassword']) )
		{
			$pass = sha1($_POST['authPassword'].$this->groupData['authSalt']);
			if( !empty($this->groupData['authPassword']) )
			{
				if( $pass == $this->groupData['authPassword'] )
				{
					Cookie::set('auth-password-' .$groupID, $pass, 365*60*60*24);
					HTTP::redirect('/');
				}
				else
				{
					$view->wrongPass = true;
				}
			}
		}
		
		$this->template->content = $view;
	}
	
	public function action_switch_membership()
	{
		$k = $_GET['k'];
        if( count( $this->groupData['access_groups'] ) > 1 || count( current($this->groupData['access_groups']) > 1) )
        {
            foreach( $this->groupData['access_groups'] as $g )
            {
				if( md5($g['group_id']) == $k )
				{
					Cookie::set('membershipChoice', $g['group_id'], 365*60*60*24);
					break;
				}
            }
        }
		HTTP::redirect('/');
	}
	
	public function before()
	{
		if( $this->request->action() == 'group_password' || $this->request->action() == "switch_membership" )
		{
			$this->noAutoAuthRedirects = TRUE;
		}
	
		parent::before();
	}
}