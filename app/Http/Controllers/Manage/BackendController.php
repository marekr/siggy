<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Support\Facades\DB;

use App\Facades\Auth;

class BackendController extends BaseController
{
	public function getESI()
	{
		if(empty(config('backend.esi.user_id')) ||
			Auth::user()->id != config('backend.esi.user_id'))
		{
			abort(401, 'Unauthorized.');
		}

		return view('manage.backend.esi');
	}
}