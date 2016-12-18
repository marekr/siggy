<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Access extends FrontController {
	public $groupData = array();
	public $trusted = false;

	public $template = 'template/main';

	public function action_group_password()
	{
		$view = View::factory('access/groupPassword');
		$view->groupData = Auth::$session->accessData;
		$view->trusted = $this->trusted;
		$view->wrongPass = false;
		$this->template->siggyMode = false;

		//load header tools
		$this->template->headerTools = '';

		$groupID = intval(Auth::$session->groupID);

		if( isset($_POST['group_password']) )
		{
			$pass = sha1($_POST['group_password'].Auth::$session->group->password_salt);
			if( !empty(Auth::$session->group->password) )
			{
				if( $pass == Auth::$session->group->password )
				{
					if( Auth::loggedIn() )
					{
						Auth::$user->savePassword( $groupID, $pass );
					}
					else
					{
						Cookie::set('auth-password-' .$groupID, $pass, 365*60*60*24);
					}
					HTTP::redirect('/');
				}
				else
				{
					$view->wrongPass = true;
				}
			}
		}

		$this->template->content = '';
		$this->template->alt_content = $view;
	}

	public function action_blacklisted()
	{
		//load header tools
		$this->template->headerTools = '';

		$view = View::factory('access/blacklisted');

		$view->groupName = Auth::$session->group->name;

		foreach(Auth::$session->group->blacklistCharacters() as $char)
		{
			if($char->character_id == Auth::$session->charID )
			{
				$view->reason = $char->reason;
				break;
			}
		}
		
		$this->template->content = $view;
	}

	public function action_switch_membership()
	{
		$k = $_GET['k'];
		if( count( Auth::$session->accessData['access_groups'] ) > 1 || count( current(Auth::$session->accessData['access_groups']) > 1) )
		{
			foreach( Auth::$session->accessData['access_groups'] as $g )
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
		if( $this->request->action() == 'group_password' || $this->request->action() == 'blacklisted'  || $this->request->action() == "switch_membership" )
		{
			$this->noAutoAuthRedirects = TRUE;
		}

		parent::before();
	}
}
