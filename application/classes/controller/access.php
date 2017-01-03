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
	
	public function action_groups()
	{
		//load header tools
		$this->template->headerTools = '';

		$groupMemberships = array_merge(GroupMember::findByType(GroupMember::TypeChar, Auth::$session->charID), 
										GroupMember::findByType(GroupMember::TypeCorp, Auth::$session->corpID));

		$groups = [];
		foreach($groupMemberships as $gm)
		{
			if($gm->group() != null)	//sanity check...
			{
				$groups[$gm->group()->id] = $gm->group();
			}
		}

		if ($this->request->method() == "POST")
		{
			$selectedGroupId = intval($_POST['group_id']);
			if( $selectedGroupId && isset( $groups[ $selectedGroupId ] ) )
			{
				Auth::$user->data['groupID'] = $selectedGroupId;
				Auth::$user->save();

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
