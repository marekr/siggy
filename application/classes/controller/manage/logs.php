<?php

require_once APPPATH.'classes/groupUtils.php';
require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';
require_once APPPATH.'classes/Zebra_Pagination.php';
/**
 * User controller: user administration, also user accounts/profiles.
 *
 * @author Mikito Takada
 * @package default
 * @version 1.0
 */

class Controller_Manage_Logs extends Controller_App {

   /**
    * @var string Filename of the template file.
    */
   public $template = 'template/manage';

   /**
    * Controls access for the whole controller, if not set to FALSE we will only allow user roles specified.
    *
    * See Controller_App for how this implemented.
    *
    * Can be set to a string or an array, for example array('login', 'admin') or 'login'
    */
   public $auth_required = 'gadmin';

   /** Controls access for separate actions
    *
    *  See Controller_App for how this implemented.
    *
    *  Examples:
    * 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
    * 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
    */
   public $secure_actions = array(
      // user actions
      'members' => array('login','gadmin')
      // the others are public (forgot, login, register, reset, noaccess)
      // logout is also public to avoid confusion (e.g. easier to specify and test post-logout page)
      );

   // USER SELF-MANAGEMENT

	/**
	* View: Redirect admins to admin index, users to user profile.
	*/
	public function action_index() 
	{
		if( Auth::$user->isGroupAdmin() || Auth::$user->data['admin'] ) 
		{
			$this->request->redirect('manage/logs/activity');
		} 
		else 
		{
			$this->request->redirect('account/overview');
		}
	}
	
	     
	public function action_sessions()
	{
		$sessions = array();
		$sessions = DB::query(Database::SELECT, "SELECT ss.*,sg.sgName FROM siggysessions ss
		LEFT JOIN subGroups sg ON(sg.subGroupID = ss.subGroupID)
		WHERE ss.groupID=:group ORDER BY ss.lastBeep DESC")
					  ->param(':group', Auth::$user->data['groupID'])->execute()->as_array();

		$view = View::factory('manage/logs/sessions');
		
		//lump the data by chars
		$sessData = array();
		foreach( $sessions as $sess )
		{
			$charID = $sess['charID'];
			if( !isset($sessData[ $charID ] ) )
			{
				$sessData[ $charID ]['charID'] = $charID;
				$sessData[ $charID ]['charName'] = $sess['charName'];
				$sessData[ $charID ]['lastBeep'] = $sess['lastBeep'];
			}
			
			if( empty($sess['sgName']) )
			{
				$sess['sgName'] = 'Default';
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

		$pagination = new Zebra_Pagination();
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