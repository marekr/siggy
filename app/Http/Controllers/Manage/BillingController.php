<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Support\Facades\DB;

use App\Facades\Auth;
use \Group;
use Siggy\BillingPayment;
use Siggy\BillingCharge;

class BillingController extends BaseController
{
	public $actionAcl = [
		'getOverview' => ['can_view_financial']
	];

	public function getOverview()
	{
		$numUsers = Auth::user()->group->getCharacterUsageCount();
		
		$payments = [];
		$payments = BillingPayment::findAllByGroupOrdered(Auth::user()->group->id);
		
		$charges = [];
		$charges = BillingCharge::findAllByGroupOrdered(Auth::user()->group->id);


		return view('manage.billing.overview', [
												'payments' => $payments,
												'charges' => $charges,
												'numUsers' => $numUsers,
											]);
	}
}