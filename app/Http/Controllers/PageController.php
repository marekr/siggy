<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller {

	public function welcome()
	{
		return view('pages.home');
	}

	public function about()
	{
		return view('pages.about');
	}
	
	public function costs()
	{
		return view('pages.costs');
	}
}
