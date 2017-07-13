<?php

namespace App\Http\Controllers\Manage;


use App\Http\Controllers\Controller;
use App\Facades\Auth;

class BaseController extends Controller {

	public $authRequired = 'gadmin';
	public $actionAcl = [];

}