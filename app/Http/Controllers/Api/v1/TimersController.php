<?php 

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use \Group;
use Siggy\Timer;

class TimersController extends BaseController {

	public function getList(Request $request) {
		try
		{
			$timers = Timer::findAllByGroupOrdered($id, $this->user()->group_id);

			return $this->response->array($timers);
		}
		catch(Exception $e)
		{
			return $this->response->errorBadRequest();
		}
	}
}
