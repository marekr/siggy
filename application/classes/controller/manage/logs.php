<?php

use Illuminate\Database\Capsule\Manager as DB;

require_once APPPATH.'classes/ZebraPagination2.php';
/**
 * User controller: user administration, also user accounts/profiles.
 *
 * @author Mikito Takada
 * @package default
 * @version 1.0
 */

class Controller_Manage_Logs extends Controller_Manage
{

	/**
	* @var string Filename of the template file.
	*/
	public $template = 'template/manage';

	/**
	* Controls access for the whole controller, if not set to FALSE we will only allow user roles specified.
	*/
	public $auth_required = 'gadmin';

	/*
	* Controls access for separate actions
	*/
	public $secure_actions = array(
		'sessions' => array('can_view_logs'),
		'activity' => array('can_view_logs')
	);

	/**
	* View: Redirect admins to admin index, users to user profile.
	*/
	public function action_index() 
	{
		if( Auth::$user->isGroupAdmin() || Auth::$user->admin ) 
		{
			HTTP::redirect('manage/logs/activity');
		} 
		else 
		{
			HTTP::redirect('manage/access/denied');
		}
	}
	
	public function action_sessions()
	{
		$sessions = [];
		$sessions = DB::select("SELECT ss.*,cm.chainmap_name,c.name as character_name FROM sessions ss
												LEFT JOIN chainmaps cm ON(cm.chainmap_id = ss.chainmap_id)
												LEFT JOIN characters c ON(c.id=ss.character_id)
												WHERE ss.group_id=? ORDER BY ss.updated_at DESC",[Auth::$user->group->id]);
		
		//lump the data by chars
		$sessData = [];
		foreach( $sessions as $sess )
		{
			$charID = $sess->character_id;
			if( !isset($sessData[ $charID ] ) )
			{
				$sessData[ $charID ]['charID'] = $charID;
				$sessData[ $charID ]['charName'] = $sess->character_name;
				$sessData[ $charID ]['lastBeep'] = $sess->updated_at;
			}
			
			$sessData[ $charID ]['data'][] = $sess;
		}

		$resp = view('manage.logs.sessions', [
												'sessions' => $sessData,
											]);
		
		$this->response->body($resp);
	}
     
	public function action_activity()
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

		$resp = view('manage.logs.activity', [
												'logs' => $logs,
												'filterType' => $filterType,
												'pagination' => $paginationHTML,
											]);
		
		$this->response->body($resp);
	}
}