<?php 

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Facades\Auth;
use App\Facades\SiggySession;
use \Group;

class GroupController extends Controller {


	public function getCreateForm()
	{
		return view('group.create_form', [
												'title' => 'create group',
												'selectedTab' => 'createGroup',
												'layoutMode' => 'blank'
											]);
	}

	public function postCreateForm(Request $request)
	{
		$save = [
			'name' => $request->input('name'),
			'ticker' => $request->input('ticker'),
			'password_required' => $request->input('password_required',0),
			'password' => $request->input('password'),
			'password_confirmation' => $request->input('password_confirmation')
		];

		$validator = Validator::make($save, [
				'name' => 'required|alpha_dash|min:3',
				'ticker' => 'required|min:3',
				'password_required' => 'required|boolean',
				'password' => 'nullable|confirmed',
				'password_confirmation' => 'required_with:password'
		]);

		if(!$validator->fails())
		{
			$group = Group::createFancy($save);
			if( $group != null )
			{
				$insert = ['user_id' => Auth::user()->id, 
							'group_id' => $group->id, 
							'can_manage_access' => 1, 
							'can_view_financial' => 1, 
							'can_manage_settings' => 1, 
							'can_manage_group_members' => 1, 
							'can_view_logs' => 1 
							];
				DB::table('users_group_acl')->insert($insert);

				Auth::user()->groupID = $group->id;
				Auth::user()->save();

				SiggySession::reloadUserSession();

				return redirect('group/create/completed');
			}
			else
			{
				$validator->errors()->add('name', 'Unknown error has occurred.');
			}
		}
		
		return redirect('group/create/form')
					->withErrors($validator)
					->withInput();
	}
	
	public function getCreateCompleted()
	{
		return view('group.create_completed', [
												'title' => 'create group',
												'selectedTab' => 'createGroup',
												'layoutMode' => 'blank'
											]);
	}

	public function getCreateIntro()
	{
		return view('group.create_intro', [
													'title' => 'create group',
													'selectedTab' => 'createGroup',
													'layoutMode' => 'blank'
												]);
	}

	public function welcome()
	{
		return view('pages.home');
	}

	public function about()
	{
		return view('pages.about');
	}
	
	public function costs()
	{
		return view('pages.costs');
	}
}
