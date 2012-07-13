<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Cron extends Controller 
{
  

	public function action_clearOldSigs()
	{
			$this->profiler = NULL;
			$this->auto_render = FALSE;
			//two days?
			$cutoff = time()-60*60*24*2;
      //DB::delete('systemsigs')->where('created', '<=', $cutoff)->where('sig', '!=', 'POS')->execute();
      
			$groups = DB::query(Database::SELECT, "SELECT groupID,skipPurgeHomeSigs,homeSystemIDs FROM groups")->execute()->as_array();	 
			foreach( $groups as $group )
			{
				$ignoreSys = '';
				
				if( $group['skipPurgeHomeSigs'] && !empty($group['homeSystemIDs']) )
				{
					$ignoreSys = $group['homeSystemIDs'];
				}
			
				$subGroupsQuery = DB::query(Database::SELECT, "SELECT subGroupID, sgSkipPurgeHomeSigs,sgHomeSystemIDs FROM subgroups WHERE groupID = :groupID")->param(':groupID', $group['groupID'])->execute();	 
				$subGroups = $subGroupsQuery->as_array();
				if(	$subGroupsQuery->count() > 0 )
				{
					foreach( $subGroups as $subGroup )
					{
						if( $subGroup['sgSkipPurgeHomeSigs'] && !empty($subGroup['sgHomeSystemIDs']) )
						{
							$ignoreSys .= ( empty($ignoreSys) ? $subGroup['sgHomeSystemIDs'] : ','.$subGroup['sgHomeSystemIDs'] );
						}
					}
				}
				
				if( !empty($ignoreSys) )
				{
					DB::query(Database::DELETE, "DELETE FROM systemsigs WHERE sig != 'POS' AND groupID=:groupID AND systemID NOT IN(".$ignoreSys.") AND created <= :cutoff")->param(':cutoff',$cutoff)->param(':groupID', $group['groupID'])->execute();
				}
				else
				{
					DB::query(Database::DELETE, "DELETE FROM systemsigs WHERE sig != 'POS' AND groupID=:groupID AND created <= :cutoff")->param(':cutoff',$cutoff)->param(':groupID', $group['groupID'])->execute();
				}
			}
      print 'done!';
	}

	public function action_resetStuff()
	{
			$this->profiler = NULL;
			$this->auto_render = FALSE;
			//two days?
			$cutoff = time()-60*60*24;
      
      DB::update('activesystems')->set(array('displayName' => '', 'activity' => 0, 'lastActive' => 0, 'inUse' => 0))->where('lastActive', '<=', $cutoff)->where('lastActive', '!=', 0)->execute();
      DB::delete('wormholes')->where('lastJump', '<=', $cutoff)->execute();
      
      print 'done!';
	}
	
	public function action_hourlyAPIStats()
	{
		$cutoff = time()-(3600*24*2);
		
    DB::delete('apiHourlyMapData')->where('hourStamp', '<=', $cutoff)->execute();
    DB::delete('jumpsTracker')->where('hourStamp', '<=', $cutoff)->execute();
      
		$systems = DB::select('id')->from('solarsystems')->order_by('id', 'ASC')->execute()->as_array('id');
		foreach($systems as &$system)
		{		
			$system['jumps'] = 0;
			$system['kills'] = 0;
			$system['npcKills'] = 0;
			$system['podKills'] = 0;
		}
	
	
		require_once(Kohana::find_file('vendor', 'pheal/Pheal'));
		spl_autoload_register("Pheal::classload");
		$pheal = new Pheal('','');
		$pheal->scope = 'map';
		$jumpsData = $pheal->Jumps();
		foreach($jumpsData->solarSystems as $ss )
		{
			if( isset( $systems[ $ss->solarSystemID ] ) )
			{
				$systems[ $ss->solarSystemID ]['jumps'] = $ss->shipJumps;
			}
		}
		
		$killsData = $pheal->Kills();
		foreach($killsData->solarSystems as $ss )
		{
			if( isset( $systems[ $ss->solarSystemID ] ) )
			{
				$systems[ $ss->solarSystemID ]['kills'] = $ss->shipKills;
				$systems[ $ss->solarSystemID ]['npcKills'] = $ss->factionKills;
				$systems[ $ss->solarSystemID ]['podKills'] = $ss->podKills;
			}
		}
		

		date_default_timezone_set('UTC');
		$requestDateInfo = getdate( time() - 3600 );
		$time = gmmktime($requestDateInfo['hours'],0,0,$requestDateInfo['mon'],$requestDateInfo['mday'],$requestDateInfo['year']);		
	
		foreach($systems as $system)
		{
			DB::query(Database::INSERT, 'INSERT INTO apiHourlyMapData (`systemID`,`hourStamp`, `jumps`, `kills`, `npcKills`, `podKills`) VALUES(:systemID, :hourStamp, :jumps, :kills, :npcKills, :podKills) ON DUPLICATE KEY UPDATE systemID=systemID')
														->param(':systemID', $system['id'] )->param(':hourStamp', $time )->param(':jumps', $system['jumps'] )->param(':kills', $system['kills'] )->param(':npcKills', $system['npcKills'] )->param(':podKills', $system['podKills'] )->execute();
	
		}
		
		print "done!";
	}
}