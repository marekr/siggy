<?php

use Siggy\View;

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

	public function action_list()
	{
		$chars = Auth::$user->group->blacklistCharacters();
		
		$resp = view('manage.blacklist.list', [
												'chars' => $chars
											]);
		
		$this->response->body($resp);
	}

	public function action_remove()
	{
		$id = $this->request->param('id');
		$entry = GroupBlackListCharacter::findByGroup(Auth::$user->groupID, $id);

		if($entry != null)
		{
			$entry->delete();
			Message::add('success', 'Blacklisted character removed succesfully');
		}

		HTTP::redirect('manage/blacklist/list');
	}

	public function action_add()
	{
		$errors = [];

		if ($this->request->method() == "POST") 
		{
			$data = [
					'reason' => $_POST['reason'] ?? '', 
					'character_name' => $_POST['character_name']
					];

			$validator = Validator::make($data, [
				'character_name' => 'required',
			]);

			$charSearchResults = array();
			if(!$validator->fails())
			{
				$charSearchResults = Character::searchEVEAPI( $_POST['character_name'], true );
				if( $charSearchResults == null )
				{
					$validator->errors()->add('character_name', 'EVE character not found');
				}
				else
				{
					$charSearchResults = current($charSearchResults);
					
					$char = GroupBlacklistCharacter::findByGroupAndChar(Auth::$user->groupID, $charSearchResults->id);
						
					if( $char != null )
					{
						$validator->errors()->add('character_name', 'The character is already blacklisted');
					}
				}
			}
				
		
			if( count($validator->errors()) == 0 )
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
			else
			{
				View::share('errors', $validator->errors());
			}
		}
		
		$resp = view('manage.blacklist.form', [
												'mode' => 'add'
											]);
		
		$this->response->body($resp);
	}
}