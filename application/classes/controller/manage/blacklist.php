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
   
	public function action_set()
	{
		if( !isset( $_POST['group'] ) )
		{
			HTTP::redirect('manage');
		}
		
		$group = intval($_POST['group']);
		
		if( !Auth::$user->isAdmin()  && !isset( Auth::$user->perms[ $group ] ) &&
			!( Auth::$user->perms[ $group ]['canManage'] == 1)
		) 
		{
			HTTP::redirect('manage/access/denied');
		}
		else
		{
			Auth::$user->data['groupID'] = intval($_POST['group']);
			Auth::$user->save();
		}
      
		HTTP::redirect('/manage');
	}
	
	public function action_list()
	{
		$this->template->title = __('Manage Blacklist');
		
		$view = $this->template->content = View::factory('manage/blacklist/list');
		
		
		$chars = DB::query(Database::SELECT, 
							"SELECT * FROM group_character_blacklist
                            WHERE group_id = :groupID"
							)
							->param(":groupID", Auth::$user->data['groupID'])
							->execute()
							->as_array();
		
		$view->chars = $chars;
	}
    
    public function action_remove()
    {
        $id = $this->request->param('id');
        
        DB::delete('group_character_blacklist')->where('id', '=', $id)->where('group_id','=', Auth::$user->data['groupID'])->execute();
        Message::add('success', 'Blacklisted character removed succesfully');
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
				
				if( !count($charSearchResults) )
				{
					$errors['character_name'] = "EVE character not found";
				}
				
				$chars = DB::query(Database::SELECT, 
					"SELECT * FROM group_character_blacklist 
					WHERE character_id =:charID AND group_id = :groupID"
					)
					->param(":groupID", Auth::$user->data['groupID'])
					->param(":charID", $charSearchResults[0]['characterID'])
					->execute()
					->current();
					
				if( isset($chars['character_id']) )
				{
					$errors['character_name'] = "The character is already blacklisted";
				}
			}
				
        
            if( !count($errors) )
			{
                $save = array(
                            'reason' => $_POST['reason'],
                            'character_id' => $charSearchResults[0]['characterID'],
                            'character_name' => $charSearchResults[0]['characterName'],
                            'group_id' => Auth::$user->data['groupID'],
							'created' => time()
                        );
                
                
                DB::insert('group_character_blacklist', array_keys($save) )->values(array_values($save))->execute();
                
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