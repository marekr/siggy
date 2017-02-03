<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Access extends FrontController {
	public $groupData = array();
	public $trusted = false;

	public $template = 'template/main';

	public function action_group_password()
	{
		if(Auth::$session->group == null)
		{
			//kick them off where hopefully the frontpagecontroller pushes them to the right spot
			HTTP::redirect('/');
		}

		$wrongPass = false;

		$groupID = Auth::$session->group->id;

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
					$wrongPass = true;
				}
			}
		}
		
		$resp = view('access.group_password', [
												'group' => Auth::$session->group,
												'themes' => Theme::allByGroup(Auth::$session->group->id),
												'settings' => $this->loadSettings(),
												'wrongPass' => $wrongPass
											]);
		$this->response->body($resp)
						->noCache();
	}

	public function action_blacklisted()
	{
		if(Auth::$session->group == null)
		{
			//kick them off where hopefully the frontpagecontroller pushes them to the right spot
			HTTP::redirect('/');
		}

		$reason = '';
		foreach(Auth::$session->group->blacklistCharacters() as $char)
		{
			if($char->character_id == Auth::$session->character_id )
			{
				$reason = $char->reason;
				break;
			}
		}

		
		$resp = view('access.blacklisted', [
												'group' => Auth::$session->group,
												'themes' => Theme::allByGroup(Auth::$session->group->id),
												'settings' => $this->loadSettings(),
												'reason' => $reason,
												'groupName' => Auth::$session->group->name
											]);
		$this->response->body($resp)
						->noCache();
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
		
		$resp = view('access.groups', [
												'group' => Auth::$session->group,
												'settings' => $this->loadSettings(),
												'groups' => $groups
											]);
		$this->response->body($resp)
						->noCache();
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
