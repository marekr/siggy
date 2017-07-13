<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Support\Facades\DB;

use App\Facades\Auth;

class DashboardController extends BaseController
{
	public function index() 
	{
		$news = DB::select("SELECT * FROM announcements WHERE visibility = 'manage' OR visibility = 'all' ORDER BY datePublished DESC LIMIT 0,3");
		
		$members = DB::selectOne("SELECT COUNT(*) as total FROM groupmembers gm 
												WHERE gm.groupID=?",[Auth::user()->group->id]);

		return view('manage.dashboard.index', [
													'news' => $news,
													'member_count' => $members->total,
													'group' => Auth::user()->group
												]);
	}

}