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

	public function action_billingCharges()
	{
		$groups = Group::where('billable',1)->get();
		foreach($groups as $group)
		{
			$numUsers = $group->getCharacterUsageCount();
			if( $numUsers == 0 )
			{
				continue;
			}

			$cost = miscUtils::computeCostPerDays($numUsers, 1);

			$message = 'Daily usage cost - ' . $numUsers . ' characters';

			$insert = array(
								'amount' => $cost,
								'date' => time(),
								'groupID' => $group->id,
								'memberCount' => $numUsers,
								'message' => $message
							);
			$result = DB::table('billing_charges')->insert($insert);

			$group->applyISKCharge( $cost );
		}
	}

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

	public function action_clearOldSigs()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		//two days?
		$cutoff = Carbon::now()->subDays(26)->toDateTimeString();
		$whCutoff = Carbon::now()->subDays(2)->toDateTimeString();

		$groups = DB::select("SELECT id,skip_purge_home_sigs FROM groups");
		foreach( $groups as $group )
		{
			$ignoreSys = '';
			$chains = DB::select("SELECT chainmap_homesystems_ids FROM chainmaps
													WHERE group_id = ? AND
													chainmap_skip_purge_home_sigs=1",[$group->id]);

			if( $chains != null )
			{
				$ignoreSys = array();
				foreach( $chains as $c )
				{
					if( !empty($c->chainmap_homesystems_ids) )
					{
						$ignoreSys[] = $c->chainmap_homesystems_ids;
					}
				}

				$ignoreSys = implode(',', $ignoreSys);
			}

			$ignoreSysExtra = '';
			if( !empty($ignoreSys) )
			{
				$ignoreSysExtra = "systemID NOT IN(".$ignoreSys.") AND ";
			}

			
			$query = DB::delete("DELETE FROM systemsigs 
								WHERE sig != 'POS' AND
									groupID=:groupID AND
									{$ignoreSysExtra}
									( created_at <= :cutoff OR (type = 'wh' AND created_at <= :whcutoff))",[
										'cutoff' => $cutoff,
										'groupID' => $group->id,
										'whcutoff' => $whCutoff
									]);
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
		$cutoff = time()-(3600*24*2);

		DB::table('apihourlymapdata')
			->where('hourStamp', '<=', $cutoff)
			->delete();
		DB::table('jumpstracker')
			->where('hourStamp', '<=', $cutoff)
			->delete();

		$systems = DB::table('solarsystems')->orderBy('id')->get()->all();
		foreach($systems as &$system)
		{
			$system->jumps = 0;
			$system->kills = 0;
			$system->npcKills = 0;
			$system->podKills = 0;
		}

		PhealHelper::configure();
		$pheal = new Pheal('','');
		$pheal->scope = 'map';

		$jumpsData = $pheal->Jumps();
		foreach($jumpsData->solarSystems as $ss )
		{
			if( isset( $systems[ $ss->solarSystemID ] ) )
			{
				$systems[ $ss->solarSystemID ]->jumps = $ss->shipJumps;
			}
		}

		$killsData = $pheal->Kills();
		foreach($killsData->solarSystems as $ss )
		{
			if( isset( $systems[ $ss->solarSystemID ] ) )
			{
				$systems[ $ss->solarSystemID ]->kills = $ss->shipKills;
				$systems[ $ss->solarSystemID ]->npcKills = $ss->factionKills;
				$systems[ $ss->solarSystemID ]->podKills = $ss->podKills;
			}
		}


		date_default_timezone_set('UTC');
		$requestDateInfo = getdate( time() - 3600 );
		$time = gmmktime($requestDateInfo['hours'],0,0,$requestDateInfo['mon'],$requestDateInfo['mday'],$requestDateInfo['year']);

		foreach($systems as $system)
		{
			DB::insert('INSERT INTO apihourlymapdata (`systemID`,`hourStamp`, `jumps`, `kills`, `npcKills`, `podKills`) 
				VALUES(:systemID, :hourStamp, :jumps, :kills, :npcKills, :podKills) ON DUPLICATE KEY UPDATE systemID=systemID',[
					'systemID' => $system->id,
					'hourStamp' => $time,
					'jumps' => $system->jumps,
					'kills' => $system->kills,
					'npcKills' => $system->npcKills,
					'podKills' => $system->podKills
				]);

		}

		print "done!";
	}
}
