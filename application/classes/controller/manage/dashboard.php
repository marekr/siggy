<?php

require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';

class Controller_Manage_Dashboard extends Controller_Manage
{
	/**
	* @var string Filename of the template file.
	*/
	public $template = 'template/manage';

	/*
	 * Controls access for the whole controller, if not set to FALSE we will only allow user roles specified.
	 */
	public $auth_required = 'gadmin';

   /*
	* Controls access for separate actions
    */
	public $secure_actions = array(
	);

	/**
	* View: Redirect admins to admin index, users to user profile.
	*/
	public function action_index() 
	{
		$this->template->title = __('Manage');
		$view = View::factory('manage/dashboard/index');


		$news = DB::query(Database::SELECT, "SELECT * FROM announcements WHERE visibility = 'manage' OR visibility = 'all' ORDER BY datePublished DESC LIMIT 0,3")
									->execute()->as_array();

		$view->bind('news', $news);
		
		$view->perms = isset(Auth::$user->perms[ Auth::$user->data['groupID'] ]) ? Auth::$user->perms[ Auth::$user->data['groupID'] ] : array();
		

		$members = DB::query(Database::SELECT, "SELECT COUNT(*) as total FROM groupmembers gm 
												WHERE gm.groupID=:group")
						->param(':group', Auth::$user->group()->id)
						->execute()
						->current();
							
		$view->set('member_count', $members['total'] );
		$view->set('group', Auth::$user->group() );

		$this->template->content = $view;
	}

	/**
	* View: Access not allowed.
	*/
	public function action_noaccess() 
	{
		$this->template->title = __('Access not allowed');
		$view = $this->template->content = View::factory('user/noaccess');
	}

}