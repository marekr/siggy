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

		$groupID = intval(Auth::$session->group->id);

		if( isset($_POST['group_password']) )
		{
			$pass = sha1($_POST['group_password'].Auth::$session->group->password_salt);
			if( !empty(Auth::$session->group->password) )
			{
				if( $pass === Auth::$session->group->password )
				{
					Auth::$user->savePassword( $groupID, $pass );
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
		$view = View::factory('access/blacklisted');

		$view->groupName = Auth::$session->group->name;

		foreach(Auth::$session->group->blacklistCharacters() as $char)
		{
			if($char->character_id == Auth::$session->character_id )
			{
				$view->reason = $char->reason;
				break;
			}
		}
		
		$this->template->content = $view;
	}
	
	public function action_groups()
	{
		$groups = Auth::$session->accessibleGroups();
		if ($this->request->method() == "POST")
		{
			$this->validateCSRF();

			$selectedGroupId = intval($_POST['group_id']);
			if( $selectedGroupId && isset( $groups[ $selectedGroupId ] ) )
			{
				Auth::$user->groupID = $selectedGroupId;
				Auth::$user->save();
				Auth::$session->reloadUserSession();

				HTTP::redirect('/');
			}
		}

		$view = View::factory('access/groups');
		$view->groups = $groups;
		$this->template->content = $view;
	}

	public function before()
	{
		if( $this->request->action() == 'group_password' 
			|| $this->request->action() == 'blacklisted' 
			|| $this->request->action() == "groups"  )
		{
			$this->noAutoAuthRedirects = TRUE;
		}

		parent::before();
	}
}
