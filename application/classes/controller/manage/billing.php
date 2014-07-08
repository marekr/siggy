<?php

require_once APPPATH.'classes/groupUtils.php';
require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';

class Controller_Manage_Billing extends Controller_Manage
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
		'overview' => array('can_view_financial')
	);

	/**
	* View: Redirect admins to admin index, users to user profile.
	*/
	public function action_index() 
	{
		if( Auth::$user->isGroupAdmin() ) 
		{
			HTTP::redirect('manage/billing/overview');
		}
		else 
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