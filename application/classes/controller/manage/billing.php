<?php

require_once APPPATH.'classes/groupUtils.php';
require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';

class Controller_Manage_Billing extends Controller_App {

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
      if( Auth::$user->isGroupAdmin() ) 
      {
         HTTP::redirect('manage/billing/overview');
      } else 
      {
         HTTP::redirect('account/overview');
      }
   }
   
   public function action_overview()
   {
		$user = Auth::$user->data;
		
										
		$numUsers = groupUtils::getCharacterUsageCount(Auth::$user->data['groupID']);
		
		
		$payments = array();
		$payments = DB::query(Database::SELECT, "SELECT * FROM billing_payments WHERE groupID=:group ORDER BY paymentID DESC LIMIT 0,10")
										->param(':group', Auth::$user->data['groupID'])->execute()->as_array();
		
		$charges = array();
		$charges = DB::query(Database::SELECT, "SELECT * FROM billing_charges WHERE groupID=:group ORDER BY chargeID DESC LIMIT 0,10")
										->param(':group', Auth::$user->data['groupID'])->execute()->as_array();
      
		$view = View::factory('manage/billing/overview');
		$view->bind('payments', $payments);
		$view->bind('charges', $charges);
		
		$group = ORM::factory('group', Auth::$user->data['groupID']);
		$view->set('group', $group );
		
		$view->set('numUsers', $numUsers);
		
		$this->template->content = $view;
		$this->template->title = "Billing Overview";
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