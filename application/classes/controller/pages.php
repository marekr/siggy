<?php

use Illuminate\Database\Capsule\Manager as DB;

use Siggy\View;

class Controller_Pages extends FrontController {
	protected $noAutoAuthRedirects = true;

	function __construct(Kohana_Request $request, Kohana_Response $response)
	{
		parent::__construct($request, $response);
	}

	public function before()
	{
		parent::before();
	}

	public function action_viewPage()
	{
		$page = $this->request->param('page','');
		if( $page == '' )
		{
			if( Auth::loggedIn() )
			{
				HTTP::redirect('/');
			}
		}
		
		if( $page == 'create-group' || $page == 'createGroup' )
		{
			if(!Auth::loggedIn())
			{
				HTTP::redirect('/account/login');
			}
			
			$resp = view('pages.create_group_intro');
		}
		else if( $page == 'costs' )
		{
			$resp = view('pages.costs');
		}
		else if( $page == 'about' )
		{
			$resp = view('pages.about');
		}
		else if( $page == 'no-group-access')
		{
			$resp = view('pages.no_group_access');
		}
		else
		{
			$resp = view('pages.home');
		}


		$this->response->body($resp);
	}

	public function action_error()
	{
	}

	public function action_createGroup()
	{
		if(!Auth::loggedIn())
		{
			HTTP::redirect('/account/login');
		}

		$id = intval($this->request->param('id'));

		if( $id == 0 || $id == 1 )
		{
			$resp = view('create_group_intro', [
													'title' => 'siggy: create group',
													'selectedTab' => 'createGroup',
													'layoutMode' => 'blank'
												]);
		}
		else if( $id == 2 )
		{
			if ($this->request->method() == "POST")
			{
				$save = [
					'name' => $_POST['name'],
					'ticker' => $_POST['ticker'],
					'password_required' => $_POST['password_required'] ?? 0,
					'password' => $_POST['password'],
					'password_confirmation' => $_POST['password_confirmation']
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
							$insert = ['user_id' => Auth::$user->id, 
										'group_id' => $group->id, 
										'can_manage_access' => 1, 
										'can_view_financial' => 1, 
										'can_manage_settings' => 1, 
										'can_manage_group_members' => 1, 
										'can_view_logs' => 1 
										];
							DB::table('users_group_acl')->insert($insert);

							Auth::$user->groupID = $group->id;
							Auth::$user->save();

							Auth::$session->reloadUserSession();

							HTTP::redirect('pages/createGroup/3');
						}
						else
						{
							$errors[] = 'Unknown error ocurred';
						}
				}
				else
				{
					$validator->errors()->add('name', 'Unknown error has occurred.');
				}
				
				View::share('errors', $validator->errors());
			}
			
			$resp = view('pages.create_group_form', [
													'title' => 'siggy: create group',
													'selectedTab' => 'createGroup',
													'layoutMode' => 'blank'
												]);
		}
		else if ( $id == 3 )
		{
			$resp = view('pages.create_group_complete', [
													'title' => 'siggy: create group',
													'selectedTab' => 'createGroup',
													'layoutMode' => 'blank'
												]);
		}

		$this->response->body($resp);
	}
}
