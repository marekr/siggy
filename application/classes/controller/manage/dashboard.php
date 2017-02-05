<?php

use Illuminate\Database\Capsule\Manager as DB;

class Controller_Manage_Dashboard extends Controller_Manage
{
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
		$news = DB::select("SELECT * FROM announcements WHERE visibility = 'manage' OR visibility = 'all' ORDER BY datePublished DESC LIMIT 0,3");
		
		$members = DB::selectOne("SELECT COUNT(*) as total FROM groupmembers gm 
												WHERE gm.groupID=?",[Auth::$user->group->id]);

		$resp = view('manage.dashboard.index', [
													'news' => $news,
													'member_count' => $members->total,
													'group' => Auth::$user->group
												]);
		
		$this->response->body($resp);
	}

}