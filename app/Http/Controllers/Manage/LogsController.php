<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use \ZebraPagination2;

use \Auth;
use \Group;
use Carbon\Carbon;

class LogsController extends BaseController
{
	public $actionAcl = [
		'getSessions' => ['can_view_logs'],
		'getActivity' => ['can_view_logs']
	];
	
	public function getCharacters()
	{
		$characters = DB::select("SELECT c.id,c.name,cg.last_group_access_at FROM character_group cg
												LEFT JOIN characters c ON(c.id=cg.character_id)
												WHERE cg.group_id=? AND cg.last_group_access_at >= ?
												 ORDER BY cg.last_group_access_at DESC",[Auth::$user->group->id, Carbon::now()->subDay()]);

		return view('manage.logs.characters', [
												'characters' => $characters,
											]);
	}
     
	public function getActivity(Request $request)
	{
		$extraSQL = "";
		$filterType = 'all';
		if( isset($_GET['filter_type']) )
		{
			switch( $_GET['filter_type'] )
			{
				case 'delwhs':
				  $extraSQL .= "AND (type = 'delwhs' OR type = 'delwh')";
				  $filterType = 'delwhs';
				  break;
				case 'delsig':
				  $extraSQL .= "AND type = 'delsig'";
				  $filterType = 'delsig';
				  break;
				case 'editsig':
				  $extraSQL .= "AND type = 'editsig'";
				  $filterType = 'editsig';
				  break;
				case 'editmap':
				  $extraSQL .= "AND type = 'editmap'";
				  $filterType = 'editmap';
				  break;
				default:
				  break;
			} 
		}

		if( isset($_GET['search']) )
		{
			$extraSQL .= " AND message LIKE ". DB::connection()->getPdo()->quote("%".$_GET['search']."%");
		}


		$logsTotal = DB::selectOne("SELECT COUNT(*) as total FROM logs WHERE groupID=? " . $extraSQL . " ORDER BY logID DESC",[Auth::$user->group->id]);

		$logsTotal = $logsTotal->total;

		$pagination = new ZebraPagination2();
		$pagination->records($logsTotal);
		$pagination->records_per_page(20);


		$paginationHTML = $pagination->render(true);
			
		$offset = $pagination->next_page_offset();		
		  
		$logs = [];
		$logs = DB::select("SELECT * FROM logs WHERE groupID=? " . $extraSQL . " ORDER BY logID DESC LIMIT ".$offset.",20",[Auth::$user->group->id]);

		return view('manage.logs.activity', [
												'logs' => $logs,
												'filterType' => $filterType,
												'pagination' => $paginationHTML,
											]);
	}
}