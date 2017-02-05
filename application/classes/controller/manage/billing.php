<?php

use Illuminate\Database\Capsule\Manager as DB;

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
			HTTP::redirect('manage/access/denied');
		}
	}

	public function action_overview()
	{
		$numUsers = Auth::$user->group->getCharacterUsageCount();
		
		$payments = array();
		$payments = DB::select("SELECT * FROM billing_payments WHERE groupID=:group ORDER BY paymentID DESC LIMIT 0,10",['group' => Auth::$user->group->id]);
		
		$charges = array();
		$charges = DB::select("SELECT * FROM billing_charges WHERE groupID=:group ORDER BY chargeID DESC LIMIT 0,10",['group' => Auth::$user->group->id]);
	  
		
		$resp = view('manage.billing.overview', [
												'payments' => $payments,
												'charges' => $charges,
												'numUsers' => $numUsers,
											]);
		
		$this->response->body($resp);
	}
}