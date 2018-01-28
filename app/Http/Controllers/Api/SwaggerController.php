<?php 

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;


class SwaggerController extends BaseController {
	public function index() {
		return response()
				->view('swagger.index');
	}
}