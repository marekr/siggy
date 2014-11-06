<?php

require_once APPPATH.'classes/groupUtils.php';
require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';
require_once APPPATH.'classes/Zebra_Pagination2.php';
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
		if( Auth::$user->isGroupAdmin() || Auth::$user->data['admin'] ) 
		{
			HTTP::redirect('manage/logs/activity');
		} 
		else 
		{
			HTTP::redirect('account/overview');
		}
	}
	
	public function action_sessions()
	{
		$sessions = array();
		$sessions = DB::query(Database::SELECT, "SELECT ss.*,cm.chainmap_name FROM siggysessions ss
												LEFT JOIN chainmaps cm ON(cm.chainmap_id = ss.chainmap_id)
												WHERE ss.groupID=:group ORDER BY ss.lastBeep DESC")
							  ->param(':group', Auth::$user->data['groupID'])
							  ->execute()
							  ->as_array();

		$view = View::factory('manage/logs/sessions');
		
		//lump the data by chars
		$sessData = array();
		foreach( $sessions as $sess )
		{
			$charID = $sess['char_id'];
			if( !isset($sessData[ $charID ] ) )
			{
				$sessData[ $charID ]['charID'] = $charID;
				$sessData[ $charID ]['charName'] = $sess['char_name'];
				$sessData[ $charID ]['lastBeep'] = $sess['lastBeep'];
			}
			
			$sessData[ $charID ]['data'][] = $sess;
		}


		$view->bind('sessions', $sessData);

		$group = ORM::factory('group', Auth::$user->data['groupID']);

		$this->template->content = $view;
		$this->template->title = "Active Sessions";
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
			$extraSQL .= " AND message LIKE ". Database::instance()->quote("%".$_GET['search']."%");
		}


		$logsTotal = DB::query(Database::SELECT, "SELECT COUNT(*) as total FROM logs WHERE groupID=:group " . $extraSQL . " ORDER BY logID DESC")
					  ->param(':group', Auth::$user->data['groupID'])->execute()->current();

		$logsTotal = $logsTotal['total'];

		$pagination = new Zebra_Pagination2();
		$pagination->records($logsTotal);
		$pagination->records_per_page(20);


		$paginationHTML = $pagination->render(true);
			
		$offset = $pagination->next_page_offset();		
		  
		$logs = array();
		$logs = DB::query(Database::SELECT, "SELECT * FROM logs WHERE groupID=:group " . $extraSQL . " ORDER BY logID DESC LIMIT ".$offset.",20")
					  ->param(':group', Auth::$user->data['groupID'])->execute()->as_array();

		$view = View::factory('manage/logs/activity');
		$view->bind('logs', $logs);
		$view->bind('pagination', $paginationHTML);
		$view->set('filterType', $filterType);

		$group = ORM::factory('group', Auth::$user->data['groupID']);

		$this->template->content = $view;
		$this->template->title = "Activity Logs";
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