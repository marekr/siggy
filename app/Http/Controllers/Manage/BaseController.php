<?php

namespace App\Http\Controllers\Manage;


use App\Http\Controllers\Controller;
use \Auth;

class BaseController extends Controller {

	public $authRequired = 'gadmin';
	public $actionAcl = [];

}