<?php defined('SYSPATH') or die('No direct script access.');
//error_reporting(E_ALL);
//error_reporting(E_ALL);
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');


use Pheal\Pheal;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;

class Controller_Cron extends Controller
{
	public function action_resetStuff()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		//two days?
		$cutoff = time()-60*60*24;

		//DB::update('activesystems')->set(array('displayName' => '', 'activity' => 0, 'lastActive' => 0, 'inUse' => 0))->where('lastActive', '<=', $cutoff)->where('lastActive', '!=', 0)->execute();
		DB::table('wormholes')->where('last_jump', '<=', $cutoff)->delete();

		print 'done!';
	}
}
