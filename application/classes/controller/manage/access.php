<?php

use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;

class Controller_Manage_Access extends Controller_Manage
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
		$resp = view('manage.access.denied');
		
		$this->response->body($resp);
	}
   
	public function action_set()
	{
		if( !isset( $_POST['group'] ) )
		{
			HTTP::redirect('manage');
		}
		
		$group = intval($_POST['group']);
		
		if( !Auth::$user->isAdmin()  && !isset( Auth::$user->perms()[ $group ] ) &&
			!( Auth::$user->perms()[ $group ]['canManage'] == 1)
		) 
		{
			HTTP::redirect('manage/access/denied');
		}
		else
		{
			Auth::$user->groupID = intval($_POST['group']);
			Auth::$user->save();
		}
      
		HTTP::redirect('/manage');
	}
	
	public function action_configure()
	{
		$users = DB::select("SELECT u.username,ua.* FROM users u
							JOIN users_group_acl ua ON(u.id = ua.user_id)
							WHERE ua.group_id = :groupID",['groupID' => Auth::$user->groupID]);
		
		$resp = view('manage.access.configure', [
												'users' => $users,
											]);
		
		$this->response->body($resp);
	}
    
    public function action_remove()
    {
        $id = $this->request->param('id');
        
        $count = DB::selectOne("SELECT COUNT(ua.user_id) as total FROM users u
							JOIN users_group_acl ua ON(u.id = ua.user_id)
                            WHERE ua.group_id = :groupID",['groupID' => Auth::$user->groupID]);
                           
                            
        if( $count->total <= 1 )
        {
            Message::add('error', 'You cannot remove the last user with management access. Another user must be added first.');
			HTTP::redirect('manage/access/configure');
        }
        
        $id = $this->request->param('id');
        
        DB::table('users_group_acl')
			->where('user_id', '=', $id)
			->where('group_id','=', Auth::$user->groupID)
			->delete();
        Message::add('success', 'User access removed succesfully');
        HTTP::redirect('manage/access/configure');
    }
    
	public function action_add()
	{
		$errors = [];
		$data = [
					'username' => '',
					'can_view_logs' => 0,
					'can_manage_group_members' => 0,
					'can_manage_settings' => 0,
					'can_view_financial' => 0,
					'can_manage_access' => 0
				];
							
		if ($this->request->method() == "POST") 
		{
			$userID = Auth::usernameExists( $_POST['username'] );
			
			if( !$userID )
			{
				$errors['username'] = "Invalid username";
			}
		
			if( !count($errors) )
			{
				$save = array(
							'can_view_logs' => isset( $_POST['can_view_logs'] ) ? intval( $_POST['can_view_logs'] ) : 0,
							'can_manage_group_members' => isset( $_POST['can_manage_group_members'] ) ? intval( $_POST['can_manage_group_members'] ) : 0,
							'can_manage_settings' => isset( $_POST['can_manage_settings'] ) ? intval( $_POST['can_manage_settings'] ) : 0,
							'can_view_financial' => isset( $_POST['can_view_financial'] ) ? intval( $_POST['can_view_financial'] ) : 0,
							'can_manage_access' => isset( $_POST['can_manage_access'] ) ? intval( $_POST['can_manage_access'] ) : 0,
							'user_id' => $userID,
							'group_id' => Auth::$user->groupID,
							'created_at' => Carbon::now()->toDateTimeString()
						);
				
				
				DB::table('users_group_acl')->insert($save);
				
				Message::add('success', 'User access added succesfully');
				HTTP::redirect('manage/access/configure');
			}
		}
		
		$resp = view('manage.access.accessform', [
												'data' => $data,
												'errors' => $errors,
												'mode' => 'add'
											]);
		
		$this->response->body($resp);
    }

	public function action_edit()
	{
		$id = $this->request->param('id');
		$errors = [];
		$data = DB::selectOne("SELECT u.username,ua.* FROM users u
								JOIN users_group_acl ua ON(u.id = ua.user_id)
								WHERE ua.user_id = :id AND ua.group_id = :groupID",
								[
									'id' => $id,
									'groupID' => Auth::$user->group->id
								]);
							
		if( $data == null )
		{
			Message::add('error', 'Invalid user selected.');
			HTTP::redirect('manage/access/configure');
		}
							
		if ($this->request->method() == "POST") 
		{
			$update = array(
						'can_view_logs' => isset( $_POST['can_view_logs'] ) ? intval( $_POST['can_view_logs'] ) : 0,
						'can_manage_group_members' => isset( $_POST['can_manage_group_members'] ) ? intval( $_POST['can_manage_group_members'] ) : 0,
						'can_manage_settings' => isset( $_POST['can_manage_settings'] ) ? intval( $_POST['can_manage_settings'] ) : 0,
						'can_view_financial' => isset( $_POST['can_view_financial'] ) ? intval( $_POST['can_view_financial'] ) : 0,
						'can_manage_access' => isset( $_POST['can_manage_access'] ) ? intval( $_POST['can_manage_access'] ) : 0,
						'updated_at' => Carbon::now()->toDateTimeString()
					);
			
			DB::table('users_group_acl')->where( 'user_id', '=', $id )->where('group_id','=',Auth::$user->groupID)->update( $update );
			
			
			Message::add('success', 'User access edited succesfully');
			HTTP::redirect('manage/access/configure');
		}
		
		$resp = view('manage.access.accessform', [
												'data' => json_decode(json_encode($data), true),
												'errors' => $errors,
												'id' => $id,
												'mode' => 'edit'
											]);
		
		$this->response->body($resp);
	}
}