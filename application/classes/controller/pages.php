<?php

use Illuminate\Database\Capsule\Manager as DB;

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
		
		if( $page == 'create-group' )
		{
			if(!Auth::loggedIn())
			{
				HTTP::redirect('/account/login');
			}
			
			$resp = view('pages.createGroupIntro', [
													'title' => 'siggy: getting siggy',
													'selectedTab' => 'createGroup',
													'layoutMode' => 'blank'
												]);
		}
		else if( $page == 'costs' )
		{
			$resp = view('pages.costs', [
													'title' => 'siggy: costs',
													'selectedTab' => 'costs',
													'layoutMode' => 'blank'
												]);
		}
		else if( $page == 'about' )
		{
			$resp = view('pages.about', [
													'title' => 'siggy: about',
													'selectedTab' => 'about',
													'layoutMode' => 'blank'
												]);
		}
		else if( $page == 'no-group-access')
		{
			$this->template->title = "No group access";
			$this->template->selectedTab = 'home';

			$this->template->content = $view = View::factory('pages/no_group_access');
		}
		else
		{
			$resp = view('pages.home', [
													'title' => 'siggy: home',
													'selectedTab' => 'home',
													'layoutMode' => 'blank'
												]);
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
			$resp = view('create_group_intro.blade', [
													'title' => 'siggy: create group',
													'selectedTab' => 'createGroup',
													'layoutMode' => 'blank'
												]);
		}
		else if( $id == 2 )
		{
			$errors = array();
			if ($this->request->method() == "POST")
			{
				$validator = Validation::factory($_POST)
							->rule('groupName', 'not_empty')
							->rule('groupTicker', 'not_empty')
							->rule('confirm_group_password',  'matches', array(':validation', 'group_password', 'confirm_group_password'));

				if( intval($_POST['group_password_required']) == 1 )
				{
					$validator->rule('group_password', 'not_empty');
				}

				if ( $validator->check() )
				{
						$group = Group::createFancy( [ 'name' => $_POST['groupName'],
															'ticker' => $_POST['groupTicker'],
															'password' => $_POST['group_password'],
															'password_required' => intval($_POST['group_password_required'])
														]
													);

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

							HTTP::redirect('pages/create-group/3');
						}
						else
						{
							$errors[] = 'Unknown error ocurred';
						}
				}
				else
				{
						$errors = $validator->errors('pages/createGroup');
				}
			}
			
			$resp = view('create_group_form.blade', [
													'title' => 'siggy: create group',
													'selectedTab' => 'createGroup',
													'layoutMode' => 'blank',
													'errors' => $errors
												]);
		}
		else if ( $id == 3 )
		{
			$resp = view('create_group_complete.blade', [
													'title' => 'siggy: create group',
													'selectedTab' => 'createGroup',
													'layoutMode' => 'blank'
												]);
		}

		$this->response->body($resp);
	}
}
