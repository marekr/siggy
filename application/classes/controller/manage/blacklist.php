<?php

class Controller_Manage_Blacklist extends Controller_Manage
{
	public $template = 'template/manage';

	public $auth_required = 'gadmin';

	public $secure_actions = array(
        // user actions
        'members' => array('can_manage_access'),
        'edit' => array('can_manage_access'),
        'remove' => array('can_manage_access')
	);
	
	/**
	* View: Redirect admins to admin index, users to user profile.
	*/
	public function action_index() 
	{
		if( Auth::$user->isAdmin() ) 
		{
			HTTP::redirect('manage/admin/groups');
		} 
		else 
		{
			HTTP::redirect('manage/access/denied');
		}
	}

	/**
	* View: Access not allowed.
	*/
	public function action_denied() 
	{
		$this->template->title = __('Access not allowed');
		$view = $this->template->content = View::factory('manage/access/denied');
	}
	
	public function action_list()
	{
		$this->template->title = __('Manage Blacklist');
		
		$view = $this->template->content = View::factory('manage/blacklist/list');

		$view->chars = Auth::$user->group->blacklistCharacters();
	}

	public function action_remove()
	{
		$id = $this->request->param('id');
		$entry = GroupBlackListCharacter::findByGroupAndChar(Auth::$user->groupID, $id);

		if($entry != null)
		{
			$entry->delete();
			Message::add('success', 'Blacklisted character removed succesfully');
		}

		HTTP::redirect('manage/blacklist/list');
	}

	public function action_add()
	{
		$this->template->title = __('Add Character To Blacklist');
		
		$errors = array();

		if ($this->request->method() == "POST") 
		{
			$charSearchResults = array();
			if( empty($_POST['character_name']) )
			{
				$errors['character_name'] = "EVE character is required.";
			}
			else
			{
			
				$charSearchResults = miscUtils::searchEVEEntityByName( $_POST['character_name'], 'char' );
				if( $charSearchResults == null )
				{
					$errors['character_name'] = "EVE character not found";
				}
				else
				{
					$charSearchResults = current($charSearchResults);
					
					$char = GroupBlacklistCharacter::findByGroupAndChar(Auth::$user->groupID, $charSearchResults->id);
						
					if( $char != null )
					{
						$errors['character_name'] = "The character is already blacklisted";
					}
				}
			}
				
        
            if( count($errors) == 0 )
			{
                $save = [
                            'reason' => $_POST['reason'],
                            'character_id' => $charSearchResults->id,
                            'group_id' => Auth::$user->groupID
				];

				GroupBlacklistCharacter::create($save);
                
                Message::add('success', 'Character added to blacklist succesfully');
                HTTP::redirect('manage/blacklist/list');
            }
        }

		$view = $this->template->content = View::factory('manage/blacklist/form');
		$view->mode = 'add';
		$view->bind('data', $data);
		$view->bind('errors', $errors);
    }
}