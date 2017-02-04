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
	public function action_apiUpdateCorpData()
	{
		set_time_limit(0);

		PhealHelper::configure();
		$pheal = new Pheal(null,null,'corp');

		$select = date('G');
		if( $select > 9 )
		{
			$select -= 9;
			if( $select > 9 )
			{
				$select -= 9;
			}
		}

		$corpsToUpdate = array();
		$corpsToUpdate = DB::select(Database::SELECT, "SELECT * FROM groupmembers WHERE memberType='corp' AND SUBSTR(id,LENGTH(id),1) = ?",[$select]);

		foreach($corpsToUpdate as $gm)
		{
			$corp = Corporation::find((int)$gm->eveID);

			//incase this fails...
			if($corp != null)
			{
				$corp->syncWithApi();
			}
		}
	}

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

	public function action_hourlyAPIStats()
	{
	}
}
