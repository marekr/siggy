<?php

/**
 * User controller: user administration, also user accounts/profiles.
 *
 * @author Mikito Takada
 * @package default
 * @version 1.0
 */

class Controller_Manage_Admin extends Controller_App {

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
      if( Auth::$user->isAdmin() ) 
      {
         $this->request->redirect('manage/admin/groups');
      } 
      else 
      {
         $this->request->redirect('manage/admin/noaccess');
      }
   }

   /**
    * View: Access not allowed.
    */
   public function action_noaccess() 
   {
      $this->template->title = __('Access not allowed');
      $view = $this->template->content = View::factory('user/noaccess');
   }
   
	public function action_changeGroup()
	{
		if( !Auth::$user->isAdmin() ) 
		{
			$this->request->redirect('manage/admin/noaccess');
		}
      
		if( isset( $_POST['group'] ) )
		{
			Auth::$user->data['groupID'] = intval($_POST['group']);
			Auth::$user->save();
		}
      
	  $this->request->redirect('/manage');
      
	}

   public function action_groups() 
   {
      if( !Auth::$user->isAdmin() ) 
      {
         $this->request->redirect('manage/admin/noaccess');
      }
      $this->template->title = __('Group management');
      
      $view = $this->template->content = View::factory('manage/admin/groups');
      
      $groups = ORM::factory('group')->find_all();
      $view->set('groups', $groups );
   }
   
   public function action_groupBilling( $id )
   {
		if( !Auth::$user->isAdmin() ) 
		{
			$this->request->redirect('manage/admin/noaccess');
		}

		$this->template->title = __('Group billing');

		$view = $this->template->content = View::factory('manage/admin/groupBilling');

		$group = ORM::factory('group', intval($id) );
		$view->set('group', $group );
      
      
   }
   
   public function action_groupBill( $id ) 
   {
		if( !Auth::$user->isAdmin() ) 
		{
			$this->request->redirect('manage/admin/noaccess');
		}
		$this->template->title = __('Group management');

		$view = $this->template->content = View::factory('manage/admin/groupBill');

		$group = ORM::factory('group', intval($id) );
		$view->set('group', $group );
      
      

		require_once( Kohana::find_file('vendor', 'pheal/Pheal') );
		spl_autoload_register( "Pheal::classload" );
		PhealConfig::getInstance()->cache = new PhealFileCache(APPPATH.'cache/api/');
		PhealConfig::getInstance()->http_ssl_verifypeer = false;
		$pheal = new Pheal(null,null,'corp');      

      
		$totalMembers = 0;
		$groupMembers =  $group->groupmembers->find_all();
			
     
		$displayMembers = array();
		foreach($groupMembers as $gm)
		{
				if( $gm->memberType == 'corp' )
				{
						try
						{
								$result = $pheal->CorporationSheet( array( 'corporationID' => (int)$gm->eveID ) );
								$count = $result->memberCount;
								$totalMembers += $count;
								$displayMembers[] = array('memberType' => $gm->memberType, 'accessName' => $gm->accessName, 'userCount' => $count);
						}
						catch( Exception $e )
						{
								$displayMembers[] = array('memberType' => $gm->memberType, 'accessName' => $gm->accessName, 'userCount' => 'ERROR');
						}
				}
				else
				{
						$totalMembers += 1;
						$displayMembers[] = array('memberType' => $gm->memberType, 'accessName' => $gm->accessName, 'userCount' => 1);
				}
		}
      
      $view->set('members', $displayMembers);
      $view->set('totalMembers', $totalMembers);
      
      $cost = 0;
      if( $totalMembers > 100 )
      {
			$totalMembers -= 100;
			$cost = 100 + 0.75*$totalMembers;
      }
      else
      {
			$cost = $totalMembers;
      }
      
      $view->set('cost', $cost);
      
   }
}