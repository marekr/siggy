<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use \Auth;
use \stdClass;

class BaseController extends Controller {

	public function isDownForMaintenance()
	{
		return file_exists(storage_path('/framework/down'));
	}

	protected function loadSettings()
	{
		if( Auth::loggedIn() )
		{
			$settings = new stdClass;
			$settings->theme_id = Auth::$user->theme_id;
			$settings->combine_scan_intel = Auth::$user->combine_scan_intel;
			$settings->language = Auth::$user->language;
			$settings->default_activity = Auth::$user->default_activity;

			if( Auth::$user->language != 'en' )
			{
				App::setLocale(Auth::$user->language);
			}

			return $settings;
		}

		$default_settings = new stdClass;
		$default_settings->theme_id = 0;
		$default_settings->combine_scan_intel = 0;
		$default_settings->language = 'en';
		$default_settings->default_activity = '';
		return $default_settings;
	}
}