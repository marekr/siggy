<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;

use \Auth;
use \Group;

class AccessController extends BaseController
{
	public $actionAcl = [
        'getConfigure' => ['can_manage_access'],
        'getRemove' => ['can_manage_access'],
        'getAdd' => ['can_manage_access'],
	];

	public function postSet()
	{
		if( !isset( $_POST['group'] ) )
		{
			return redirect('manage');
		}
		
		$group = intval($_POST['group']);
		
		if( !Auth::$user->isAdmin()  && !isset( Auth::$user->perms()[ $group ] ) &&
			!( Auth::$user->perms()[ $group ]['canManage'] == 1)
		) 
		{
			return redirect('manage/access/denied');
		}
		else
		{
			Auth::$user->groupID = intval($_POST['group']);
			Auth::$user->save();
		}

		return redirect('/manage');
	}
	
	public function getConfigure()
	{
		$users = DB::select("SELECT u.username,ua.* FROM users u
							JOIN users_group_acl ua ON(u.id = ua.user_id)
							WHERE ua.group_id = :groupID",['groupID' => Auth::$user->groupID]);
		
		return view('manage.access.configure', [
												'users' => $users,
											]);
	}
    
    public function getRemove($id)
    {
		$count = DB::selectOne("SELECT COUNT(ua.user_id) as total FROM users u
							JOIN users_group_acl ua ON(u.id = ua.user_id)
							WHERE ua.group_id = :groupID",['groupID' => Auth::$user->groupID]);
							
							
		if( $count->total <= 1 )
		{
			flash('You cannot remove the last user with management access. Another user must be added first.')->error();
			return redirect('manage/access/configure');
		}
		
		DB::table('users_group_acl')
			->where('user_id', '=', $id)
			->where('group_id','=', Auth::$user->groupID)
			->delete();

		flash('User access removed succesfully')->success();
		return redirect('manage/access/configure');
	}

	public function getAdd()
	{
		$data = [
					'username' => '',
					'can_view_logs' => 0,
					'can_manage_group_members' => 0,
					'can_manage_settings' => 0,
					'can_view_financial' => 0,
					'can_manage_access' => 0
				];
	
		return view('manage.access.accessform', [
												'data' => $data,
												'mode' => 'add'
											]);
	}



	public function postAdd(Request $request)
	{
		$validator = Validator::make($_POST, [
			'username' => 'required',
		]);

		if($validator->passes())
		{
			$userID = Auth::usernameExists( $_POST['username'] );
			if( !$userID )
			{
				$validator->errors()->add('username', 'User not found');
			}
		
			if( count($validator->errors()) == 0 )
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
				
				flash('User access added succesfully')->success();
				return redirect('manage/access/configure');
			}
		}

		return redirect('manage/access/add')
					->withErrors($validator)
					->withInput();
    }

	public function getEdit($id)
	{
		$data = DB::selectOne("SELECT u.username,ua.* FROM users u
								JOIN users_group_acl ua ON(u.id = ua.user_id)
								WHERE ua.user_id = :id AND ua.group_id = :groupID",
								[
									'id' => $id,
									'groupID' => Auth::$user->group->id
								]);
							
		if( $data == null )
		{
			flash('Invalid user selected.')->error();
			return redirect('manage/access/configure');
		}

		return view('manage.access.accessform', [
												'data' => json_decode(json_encode($data), true),
												'id' => $id,
												'mode' => 'edit'
											]);
	}
	
	public function postEdit($id)
	{
		$data = DB::selectOne("SELECT u.username,ua.* FROM users u
								JOIN users_group_acl ua ON(u.id = ua.user_id)
								WHERE ua.user_id = :id AND ua.group_id = :groupID",
								[
									'id' => $id,
									'groupID' => Auth::$user->group->id
								]);

		if( $data == null )
		{
			flash('Invalid user selected.')->error();
			return redirect('manage/access/configure');
		}

		$update = array(
					'can_view_logs' => intval( $_POST['can_view_logs'] ?? 0 ),
					'can_manage_group_members' => intval( $_POST['can_manage_group_members'] ?? 0 ),
					'can_manage_settings' => intval( $_POST['can_manage_settings'] ?? 0),
					'can_view_financial' => intval( $_POST['can_view_financial'] ?? 0),
					'can_manage_access' => intval( $_POST['can_manage_access'] ?? 0),
					'updated_at' => Carbon::now()->toDateTimeString()
				);

		DB::table('users_group_acl')
			->where( 'user_id', '=', $id )
			->where('group_id','=',Auth::$user->group->id)
			->update( $update );

		flash('User access edited succesfully')->success();
		return redirect('manage/access/configure');
	}
}