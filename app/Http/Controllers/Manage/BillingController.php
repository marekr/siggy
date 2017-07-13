<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Support\Facades\DB;

use App\Facades\Auth;
use \Group;

class BillingController extends BaseController
{
	public $actionAcl = [
		'getOverview' => ['can_view_financial']
	];

	public function getOverview()
	{
		$numUsers = Auth::user()->group->getCharacterUsageCount();
		
		$payments = array();
		$payments = DB::select("SELECT * FROM billing_payments WHERE groupID=:group ORDER BY paymentID DESC LIMIT 0,10",['group' => Auth::user()->group->id]);
		
		$charges = array();
		$charges = DB::select("SELECT * FROM billing_charges WHERE groupID=:group ORDER BY chargeID DESC LIMIT 0,10",['group' => Auth::user()->group->id]);


		return view('manage.billing.overview', [
												'payments' => $payments,
												'charges' => $charges,
												'numUsers' => $numUsers,
											]);
	}
}