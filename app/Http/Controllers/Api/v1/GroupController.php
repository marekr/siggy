<?php 

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use \Group;
use Siggy\System;

class GroupController extends BaseController {

	public function getGroup(Request $request) {
		try
		{
			$group = $this->user()->group;
			
			$object = [
				'id' => $group->id,
				'name' => $group->name,
				'ticker' => $group->ticker,
				'isk_balance' => $group->isk_balance,
				'payment_code' => 'siggy-'.$group->payment_code
			];

			return $this->response->array($object);
		}
		catch(Exception $e)
		{
			return $this->response->errorBadRequest();
		}
	}
}
