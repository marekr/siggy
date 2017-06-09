<?php 

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use \Group;
use \System;

class SystemsController extends BaseController {

	public function getSystem($id, Request $request) {
		try
		{
			$system = System::get($id, $this->user()->group_id);

			if($system == null)
			{
				return $this->response->errorNotFound();
			}

			return $this->response->array($system);
		}
		catch(Exception $e)
		{
			return $this->response->errorBadRequest();
		}
	}
}
