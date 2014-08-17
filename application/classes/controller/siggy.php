<?php defined('SYSPATH') or die('No direct script access.');

require_once APPPATH.'classes/FrontController.php';

class Controller_Siggy extends FrontController 
{
	public $trusted = false;
	
	public $template = 'template/main';
	
	public $chainmap = null;

	public function action_index()
	{
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		
		$view = View::factory('siggy/siggyMain');
		
		$ssname = $this->request->param('ssname', '');
	
		$mapOpen = ( isset($_COOKIE['mapOpen'] ) ? intval($_COOKIE['mapOpen']) : 0 );
        $statsOpen = ( isset($_COOKIE['system_stats_open'] ) ? intval($_COOKIE['system_stats_open']) : 0 );
		
		// set default
		$view->systemData = array('id' => 30000142, 'name' => 'Jita');
		
		// did we have an url requested system?
		if( !empty($ssname) )
		{
			$sysData = array();
			
			$ssname = preg_replace("/[^a-zA-Z0-9]/", "", $ssname);
			
			$ssid = $this->findSystemIDByName($ssname);
			if( $ssid )
			{
				$sysData = $this->getSystemData($ssid);
			}
			
			if( !empty($sysData) )
			{
				$requested = true;
				$view->systemData = $sysData;
			}
		}
		else
		{
			$requested = false;
			
			if( $this->chainmap != null )
			{
				$homeSystems = $this->chainmap->get_home_systems();
				
				if( count($homeSystems) > 0 )
				{
					$view->systemData = array('id' => $homeSystems[0], 'name' => '');
				}
			}
			$view->initialSystem = true;
		}
		
		$view->initialSystem = true;
		$view->group = $this->groupData;
		$view->requested = $requested;
        $view->statsOpen = $statsOpen;
		$view->igb = $this->igb;
		
		$view->sessionID = '';
	
		$this->template->content = $view;
		
		//load chain map html
		$chainMapHTML = View::factory('templatebits/chainMap');
		$chainMapHTML->mapOpen = $mapOpen;
		$chainMapHTML->group = $this->groupData;
		$view->chainMap = $chainMapHTML;
		
		$routePlannerHTML = View::factory('templatebits/routeplanner');
		$this->template->routePlanner = $routePlannerHTML;
		
		//load header tools
        $themes = DB::query(Database::SELECT, "SELECT theme_id, theme_name FROM themes
                                                WHERE visibility='all' OR (group_id=:group AND visibility='group')
                                                ORDER BY theme_id ASC")
								->param(':group', $this->groupData['groupID'])
								->execute()
								->as_array();

		$headerToolsHTML = View::factory('templatebits/headerTools');
		$headerToolsHTML->group = $this->groupData;
        $headerToolsHTML->themes = $themes;
        $headerToolsHTML->settings = $this->template->settings;
		$this->template->headerTools = $headerToolsHTML;
	}
	
    public function action_savesettings()
    {
        $this->profiler = NULL;
        $this->auto_render = FALSE;
        
        $charID = $this->groupData['charID'];
        
        
        if( !empty($charID) )
        {									
            $themeID = intval($_POST['theme_id']);
            
            $themes = DB::query(Database::SELECT, "SELECT theme_id, theme_name FROM themes
                                                    WHERE theme_id = :themeID AND (visibility='all' OR (group_id=:group AND visibility='group'))")
									->param(':themeID', $themeID)
									->param(':group', $this->groupData['groupID'])
									->execute()
									->as_array();

            if( count( $themes ) > 0 )
            {
                DB::query(Database::INSERT, 'REPLACE INTO character_settings (`char_id`, `theme_id`) VALUES(:charID, :themeID)')
							->param(':charID', $charID )
							->param(':themeID', $themeID)
							->execute();
            }
        }
        
        HTTP::redirect('/');
        
        exit();
    }
	
	public function before()
	{
		parent::before();
		
		if( $this->groupData['active_chain_map'] )
		{
			$this->chainmap = new Chainmap($this->groupData['active_chain_map'],$this->groupData['groupID']);
		}
	}
	
	public function after()
	{
		parent::after();
	}
	
	private function findSystemIDByName( $id )
	{
		$systemData = DB::query(Database::SELECT, "SELECT ss.id,ss.name
													FROM solarsystems ss 
													WHERE ss.name=:name")
									->param(':id', $id)
									->execute
									->current();
									
		if( !$systemData['id'] )
		{
			return 0;
		}
		else
		{
			return $systemData['id'];
		}
	}
	
	private function getSystemData( $id )
	{
		$systemData = DB::query(Database::SELECT, "SELECT ss.*,se.effectTitle, r.regionName, c.constellationName, 
													COALESCE(sa.displayName,'') as displayName,
													COALESCE(sa.inUse,0) as inUse,
													COALESCE(sa.activity,0) as activity
													FROM solarsystems ss 
													INNER JOIN systemeffects se ON ss.effect = se.id
													INNER JOIN regions r ON ss.region = r.regionID
													INNER JOIN constellations c ON ss.constellation = c.constellationID
													LEFT OUTER JOIN activesystems sa ON (ss.id = sa.systemID  AND sa.groupID = :group AND sa.chainmap_id=:chainmap)
													WHERE ss.id=:id")
									->param(':id', $id)
									->param(':group', $this->groupData['groupID'])
									->param(':chainmap', $this->groupData['active_chain_map'])
									->execute()
									->current();
		
		if( !$systemData['id'] )
		{
			return FALSE;
		}
		
		$systemData['staticData'] = array();
									
		$staticData = DB::query(Database::SELECT, "SELECT st.* FROM staticmap sm 
													INNER JOIN statics st ON sm.staticID = st.staticID
													WHERE sm.systemID=:id")
									->param(':id', $systemData['id'])
									->execute()
									->as_array();	 
		
		if( count( $staticData ) > 0 )
		{
			$systemData['staticData'] = $staticData;
		}
		
		$end = miscUtils::getHourStamp();
		$start = miscUtils::getHourStamp(-24);
		$apiData = DB::query(Database::SELECT, "SELECT hourStamp, jumps, kills, npcKills FROM apihourlymapdata WHERE systemID=:system AND hourStamp >= :start AND hourStamp <= :end ORDER BY hourStamp asc LIMIT 0,24")
									->param(':system', $systemData['id'])
									->param(':start', $start)
									->param(':end', $end)
									->execute()
									->as_array('hourStamp');	 
	
		$trackedJumps = DB::query(Database::SELECT, "SELECT hourStamp, jumps FROM jumpstracker WHERE systemID=:system AND groupID=:group AND hourStamp >= :start AND hourStamp <= :end ORDER BY hourStamp asc LIMIT 0,24")
									->param(':system', $systemData['id'])
									->param(':group', $this->groupData['groupID'])
									->param(':start', $start)
									->param(':end', $end)
									->execute()->as_array('hourStamp');	 
		
		$systemData['stats'] = array();
		for($i = 23; $i >= 0; $i--)
		{
			$hourStamp = miscUtils::getHourStamp($i*-1);
			$apiJumps = ( isset($apiData[ $hourStamp ]) ? $apiData[ $hourStamp ]['jumps'] : 0);
			$apiKills = ( isset($apiData[ $hourStamp ]) ? $apiData[ $hourStamp ]['kills'] : 0);
			$apiNPC = ( isset($apiData[ $hourStamp ]) ? $apiData[ $hourStamp ]['npcKills'] : 0);
			$siggyJumps = ( isset($trackedJumps[ $hourStamp ]) ? $trackedJumps[ $hourStamp ]['jumps'] : 0);
			$systemData['stats'][] = array( $hourStamp*1000, $apiJumps, $apiKills, $apiNPC, $siggyJumps);
		}
		
		$hubJumps = DB::query(Database::SELECT, "SELECT ss.id as system_id, pr.num_jumps,ss.name as destination_name FROM precomputedroutes pr
												 INNER JOIN solarsystems ss ON ss.id = pr.destination_system
												 WHERE pr.origin_system=:system AND pr.destination_system != :system
												 ORDER BY pr.num_jumps ASC")
									->param(':system', $systemData['id'])
									->execute()
									->as_array();
	
		$systemData['hubJumps'] = $hubJumps;
		
		$systemData['poses'] = $this->getPOSes( $systemData['id'] );
		
		$systemData['dscans'] = $this->getDScans( $systemData['id'] );
		
		return $systemData;
	}
    
    private function getPOSes( $systemID )
    {
        $poses = DB::query(Database::SELECT, "SELECT p.pos_id, p.pos_location_planet, p.pos_location_moon, p.pos_online, p.pos_type, p.pos_size,
                                                p.pos_added_date, p.pos_owner, pt.pos_type_name, p.pos_notes
												FROM pos_tracker p
                                                INNER JOIN pos_types pt ON(pt.pos_type_id = p.pos_type)
                                                WHERE p.group_id=:group_id AND p.pos_system_id=:system_id
												ORDER BY p.pos_location_planet ASC, p.pos_location_moon ASC")
										->param(':group_id', $this->groupData['groupID'])
                                        ->param(':system_id', $systemID)
                                        ->execute()
										->as_array();	

        return $poses;
    }
	
    private function getDScans( $systemID )
    {
        $dscans = DB::query(Database::SELECT, "SELECT dscan_id, dscan_title, dscan_date
												FROM dscan
                                                WHERE group_id=:group_id AND system_id=:system_id")
										->param(':group_id', $this->groupData['groupID'])
                                        ->param(':system_id', $systemID)
                                        ->execute()
										->as_array();	

        return $dscans;
    }
	
	public function action_loadScanProfiles()
	{
		if(	 !$this->siggyAccessGranted() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}			 
			
		$profiles = array();
		if( isset( $this->groupData['charID'] ) )
		{
			$profiles = DB::query(Database::SELECT, "SELECT * FROM scanprofiles WHERE charID=:charID")
											->param(':charID',  $this->groupData['charID'])
											->execute()
											->as_array('profileID');	
											
		}
		else
		{
			$profiles['error'] = 'No char ID';
		}
		
		echo json_encode($profiles);
		die();
	}
	
	public function action_tweakScanProfile()
	{
		if(	 !$this->siggyAccessGranted() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}			 
			
		$mode = $_POST['mode'];
		if( !empty( $this->groupData['charID'] ) )
		{
			$update['profileName'] = strip_tags($_POST['profileName']);
			$update['covertOps'] = intval($_POST['covertOps']);
			$update['rangeFinding'] = intval($_POST['rangeFinding']);
			$update['rigs'] = intval($_POST['rigs']);
			$update['prospector'] = intval($_POST['prospector']);
			$update['sistersLauncher'] = intval($_POST['sistersLauncher']);
			$update['sistersProbes'] = intval($_POST['sistersProbes']);
			$update['preferred'] = intval($_POST['preferred']);
			$update['charID'] = $this->groupData['charID'];
			
			if( $update['preferred'] )
			{
				DB::update('scanprofiles')->set( array('preferred' => 0) )->where('charID', '=',  $this->groupData['charID'])->execute();
			}
			
			if( $mode == 'edit' )
			{
				$id = intval($_POST['profileID']);
				
				DB::update('scanprofiles')->set( $update )->where('profileID', '=', $id)->execute();
				$update['profileID'] = $id;
			}
			else
			{
				$ins = DB::insert('scanprofiles', array_keys($update) )->values(array_values($update))->execute();
				$update['profileID'] = $ins[0];
			}
			unset($update['charID']);
			echo json_encode( $update );
			die();
		}
	}
	
	public function action_deleteScanProfile()
	{
		if( !empty( $this->groupData['charID']) )
		{
			$id = intval($_POST['profileID']);
			DB::delete('scanprofiles')->where('profileID', '=', $id)->where('charID','=',  $this->groupData['charID'] )->execute();
		}
	}
	
	private function isWormholeSystemByName($name)
	{
		if( preg_match('/\bJ\d{6}\b/', $name) )
		{
			return TRUE;
		}
		return FALSE;
	}
	
	private function __wormholeJump($origin, $dest)
	{
        if( $origin == $dest )
        {
            //failure condition that happens sometimes, bad for the JS engine
            return;
        }
		
		$shipTypeID = isset($_SERVER['HTTP_EVE_SHIPTYPEID']) ? $_SERVER['HTTP_EVE_SHIPTYPEID']  : 0;
		
		if( $shipTypeID == 0 || $shipTypeID == 670 || $shipTypeID == 33328 )
		{
			//pods
			//return because we could be podded :)
			return;
		}
		
		$kspaceJump = DB::query(Database::SELECT, "SELECT `fromSolarSystemID`, `toSolarSystemID` 
													FROM mapsolarsystemjumps 
													WHERE (fromSolarSystemID=:sys1 AND toSolarSystemID=:sys2) OR
														 (fromSolarSystemID=:sys2 AND toSolarSystemID=:sys1)")
						->param(':sys1', $origin)
						->param(':sys2', $dest)
						->execute()
						->current();
						
		if( isset($kspaceJump['fromSolarSystemID']) )
		{
			return;
		}
    
        $whHash = mapUtils::whHashByID($origin, $dest);
		
		$connection = DB::query(Database::SELECT, "SELECT `hash` FROM wormholes WHERE hash=:hash AND groupID=:group AND chainmap_id=:chainmap")
						->param(':hash', $whHash)
						->param(':group', $this->groupData['groupID'])
						->param(':chainmap', $this->groupData['active_chain_map'])
						->execute()
						->current();

		if( !isset($connection['hash'] ) )
		{
			//new wh
			$this->chainmap->add_system_to_map($whHash, $origin, $dest);
			
			miscUtils::increment_stat('wormholes', $this->groupData);
		}
		else
		{
			//existing wh
			DB::update('wormholes')
				->set( array('lastJump' => time()) )
				->where('hash', '=', $whHash)
				->where('groupID', '=', $this->groupData['groupID'])
				->where('chainmap_id', '=', $this->groupData['active_chain_map'])
				->execute();
		}
		
        if( $this->groupData['jumpLogEnabled']  && !empty( $_SERVER['HTTP_EVE_SHIPTYPEID'] ) )
        {
            $charID = ( $this->groupData['jumpLogRecordNames'] ? $_SERVER['HTTP_EVE_CHARID'] : 0 );
            $charName = ( $this->groupData['jumpLogRecordNames'] ? $_SERVER['HTTP_EVE_CHARNAME'] : '' );
            $jumpTime = ( $this->groupData['jumpLogRecordTime'] ? time() : 0 );
			
            DB::query(Database::INSERT, 'INSERT INTO wormholetracker (`whHash`, `origin`, `destination`, `groupID`, `chainmap_id`, `time`, `shipTypeID`,`charID`, `charName`)
															   VALUES(:hash, :origin, :dest, :groupID, :chainmap, :time,:shipTypeID,:charID,:charName)')
						->param(':hash', $whHash )
						->param(':dest', $dest)
						->param(':origin', $origin )
						->param(':groupID', $this->groupData['groupID'] )
						->param(':chainmap', $this->groupData['active_chain_map'] )
						->param(':time', $jumpTime )
						->param(':shipTypeID',  $_SERVER['HTTP_EVE_SHIPTYPEID'] )
						->param(':charID', $charID)
						->param(':charName', $charName)
						->execute();
        }

	}
	
	public function action_getJumpLog()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1\

		if(	!$this->siggyAccessGranted() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}			 
		
		if( !isset($_GET['whHash']) || empty( $_GET['whHash'] ) )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Missing whHash parameter.'));
			exit();
		}
		
		$hash = ($_GET['whHash']);
		
		$jumpData = array();
		$jumpData  = DB::query(Database::SELECT, "SELECT wt.shipTypeID, wt.charName, wt.charID, wt.origin, wt.destination, wt.time, s.shipName, s.mass, s.shipClass FROM wormholetracker wt 
													LEFT JOIN ships as s ON s.shipID = wt.shipTypeID 
													WHERE wt.groupID = :groupID AND wt.whHash = :hash 
													ORDER BY wt.time DESC")
										->param(':groupID', $this->groupData['groupID'])
										->param(':hash', $hash)
										->execute()
										->as_array();
										
		$totalMass = 0;
		foreach( $jumpData as $jump )
		{
			$totalMass += $jump['mass'];
		}
		
		$output['totalMass'] = $totalMass;
		$output['jumpItems'] = $jumpData;

		echo json_encode($output);
		exit();
	}
	
	public function action_update()
	{
        $this->profiler = NULL;
        $this->auto_render = FALSE;
        header('content-type: application/json');
        header("Cache-Control: no-cache, must-revalidate");
      //  ob_start( 'ob_gzhandler' );
        
        if(	!$this->siggyAccessGranted() )
        {
            echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
            exit();
        }			 
        
        $chainMapOpen = ( isset($_POST['mapOpen']) ? intval($_POST['mapOpen']) : 0 );
        
        $update = array('systemUpdate' => 0, 'sigUpdate' => 0, 'globalNotesUpdate' => 0, 'mapUpdate' => 0, 'acsid' => 0, 'acsname' =>'');
        
        $group_last_cache_time = isset($_POST['group_cache_time']) ? intval($_POST['group_cache_time']) : 0;
        if( isset( $_POST['lastUpdate'] ) && isset( $_POST['systemID'] ) && $_POST['systemID'] != 0 )
        {
            $currentSystemID = intval($_POST['systemID']);
            $forceUpdate = $_POST['forceUpdate'] == 'true' ? 1 : 0;
            $_POST['lastUpdate'] = intval($_POST['lastUpdate']);
            $freeze = intval( $_POST['freezeSystem'] );
        
            $newSystemData = array();
            $update['acsid'] = $lastSystemID = $actualCurrentSystemID = intval($_POST['acsid']);
            $update['acsname'] = $lastSystemName = $actualCurrentSystemName = $_POST['acsname'];

            if( $this->igb )
            {
                if( ($actualCurrentSystemID != $_SERVER['HTTP_EVE_SOLARSYSTEMID'] ) )
                {
                    //$newSystemData = $this->getSystemData( $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'] );
                    //fix me once CCP stops being dumb
                    
					$update['acsid'] = $actualCurrentSystemID = $_SERVER['HTTP_EVE_SOLARSYSTEMID'];
					$update['acsname'] = $actualCurrentSystemName = $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'];
                    
                    
                    if( $this->groupData['recordJumps'] && $actualCurrentSystemID != 0 && $lastSystemID != 0 )
                    {
						$hourStamp = miscUtils::getHourStamp();
						DB::query(Database::INSERT, 'INSERT INTO jumpstracker (`systemID`, `groupID`, `hourStamp`, `jumps`) VALUES(:systemID, :groupID, :hourStamp, 1) ON DUPLICATE KEY UPDATE jumps=jumps+1')
											->param(':hourStamp', $hourStamp )
											->param(':systemID', $lastSystemID )
											->param(':groupID', $this->groupData['groupID'] )
											->execute();						

						DB::query(Database::INSERT, 'INSERT INTO jumpstracker (`systemID`, `groupID`, `hourStamp`, `jumps`) VALUES(:systemID, :groupID, :hourStamp, 1) ON DUPLICATE KEY UPDATE jumps=jumps+1')
											->param(':hourStamp', $hourStamp )
											->param(':systemID', $actualCurrentSystemID )
											->param(':groupID', $this->groupData['groupID'] )
											->execute();									
                    }
                
                    if( ($lastSystemID != $actualCurrentSystemID) && $actualCurrentSystemID != 0 && !empty($lastSystemID) )
                    {
                        $this->__wormholeJump($lastSystemID, $actualCurrentSystemID);
                    }
                }
            }
			
            if( $forceUpdate || ( $this->igb && $_POST['systemID'] != $_SERVER['HTTP_EVE_SOLARSYSTEMID'] ) )
            {
                //$newSystemData = $this->getSystemData( $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'] );
                //if specific system isn't picked then load new one
                if( !$freeze && $this->igb )
                {
                    $update['systemData'] = $this->getSystemData( $_SERVER['HTTP_EVE_SOLARSYSTEMID'] );
                    //$newSystemData = $this->getSystemData( $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'] );
                    //$update['systemData'] = $newSystemData;
                    if( count( $update['systemData'] ) > 0 )
                    {
                            $update['systemUpdate'] = (int) 1;
                            $currentSystemID = $update['systemData']['id'];
                    }
                }
                //if specific system is picked, we have a forced update
                elseif( $freeze  || $forceUpdate )
                {
                    $update['systemData'] = $this->getSystemData( $_POST['systemID'] );
                    if( count( $update['systemData'] ) > 0 )
                    {
                            $update['systemUpdate'] = (int) 1;
                            $currentSystemID = $update['systemData']['id'];
                    }
                }
            }
					
            //location tracking!
            if( $this->igb && isset($_SERVER['HTTP_EVE_CHARID']) && isset($_SERVER['HTTP_EVE_CHARNAME']) && $actualCurrentSystemID != 0 )
            {
                if( ! $this->groupData['alwaysBroadcast'] )
                {
                    $broadcast = (isset($_COOKIE['broadcast']) ? intval($_COOKIE['broadcast']) : 1);
                }
                else
                {
                    $broadcast = 1;
                }
      
				DB::query(Database::INSERT, 'INSERT INTO chartracker (`charID`, `charName`, `currentSystemID`,`groupID`,`chainmap_id`,`lastBeep`, `broadcast`,`shipType`, `shipName`) VALUES(:charID, :charName, :systemID, :groupID, :chainmap, :lastBeep, :broadcast, :shipType, :shipName)'
							. 'ON DUPLICATE KEY UPDATE lastBeep = :lastBeep, currentSystemID = :systemID, broadcast = :broadcast, shipType = :shipType, shipName = :shipName')
						->param(':charID', $_SERVER['HTTP_EVE_CHARID'] )
						->param(':charName', $_SERVER['HTTP_EVE_CHARNAME'] )
						->param(':broadcast', $broadcast )
						->param(':systemID', $actualCurrentSystemID )
						->param(':groupID', $this->groupData['groupID'] )
						->param(':shipType', isset($_SERVER['HTTP_EVE_SHIPTYPEID']) ? $_SERVER['HTTP_EVE_SHIPTYPEID'] : 0 )
						->param(':shipName', isset($_SERVER['HTTP_EVE_SHIPNAME']) ? htmlentities($_SERVER['HTTP_EVE_SHIPNAME']) : '' )
						->param(':chainmap', $this->groupData['active_chain_map'] )
						->param(':lastBeep', time() )->execute();			
            }
			
			if( $this->chainmap != null )
			{
				$this->mapData = $this->chainmap->get_map_cache();
				if( $chainMapOpen == 1 )
				{
					$update['chainMap']['actives'] = array();
					$update['chainMap']['systems'] = array();
					$update['chainMap']['wormholes'] = array();
					if( is_array($this->mapData['systemIDs']) && count($this->mapData['systemIDs'])	 > 0 )
					{
						$activesData = array();
						$activesData = DB::query(Database::SELECT, "SELECT ct.charName, ct.currentSystemID, s.shipName FROM chartracker ct 
																	LEFT JOIN ships s ON (ct.shipType=s.shipID)
																	WHERE ct.groupID = :groupID AND ct.chainmap_id = :chainmap AND ct.broadcast=1 AND
																		ct.currentSystemID IN(".implode(',',$this->mapData['systemIDs']).") AND ct.lastBeep >= :lastBeep 
																		ORDER BY ct.charName ASC")
											->param(':lastBeep', time()-60)
											->param(':groupID', $this->groupData['groupID'])
											->param(':chainmap', $this->groupData['active_chain_map'])
											->execute()
											->as_array();
							
						if( is_array($activesData) && count($activesData) > 0 )
						{
							$actives = array();
							foreach( $activesData as $act )
							{
								if( strlen( $act['charName']) > 15 )
								{
									$act['charName'] = substr($act['charName'], 0,12).'...';
								}
								
								if( $act['shipName'] == NULL )
								{
									$act['shipName'] = "";
								}
								$actives[ $act['currentSystemID'] ][] = array('name' => $act['charName'], 'ship' => $act['shipName']);
							}

							$update['chainMap']['actives'] = $actives;
						}
					}
					
					if( $_POST['mapLastUpdate'] != $this->mapData['updateTime'] )
					{
						$update['chainMap']['systems'] = $this->mapData['systems'];
						$update['chainMap']['wormholes'] = $this->mapData['wormholes'];
						$update['mapUpdate'] = (int) 1;
					}
					$update['chainMap']['lastUpdate'] = $this->mapData['updateTime'];
				}
			}
			
            $activeSystemQuery = DB::query(Database::SELECT, 'SELECT lastUpdate FROM activesystems WHERE systemID=:id AND groupID=:group AND chainmap_id=:chainmap')
												->param(':id', $currentSystemID)
												->param(':group',$this->groupData['groupID'])
												->param(':chainmap', $this->groupData['active_chain_map'])
												->execute();

            $activeSystem = $activeSystemQuery->current();
            $recordedLastUpdate = ($activeSystem['lastUpdate'] > 0) ? $activeSystem['lastUpdate']: time();
			//print $recordedLastUpdate;
            if( ($_POST['lastUpdate'] < $recordedLastUpdate) || ( $_POST['lastUpdate'] == 0 ) || $forceUpdate || $update['systemUpdate'] )
            {
                $additional = '';
                if( $this->groupData['showSigSizeCol'] )
                {
                        $additional .= ',sigSize';
                }
				
                $update['sigData'] = DB::query(Database::SELECT, "SELECT sigID,sig, type, siteID, description, created, creator,updated,lastUpdater".$additional." FROM systemsigs
																	WHERE systemID=:id AND groupID=:group")
                                 ->param(':id', $currentSystemID)
								 ->param(':group', $this->groupData['groupID'])
								 ->execute()
								 ->as_array('sigID');	

                $update['sigUpdate'] = (int) 1;
            }
            
			if( $group_last_cache_time < $this->groupData['cache_time'] )
			{
				$update['chainmaps_update'] = 1;
				$update['chainmaps'] = $this->groupData['accessible_chainmaps'];
				
				$update['globalNotesUpdate'] = (int) 1;
				$update['group_cache_time'] = (int) $this->groupData['cache_time'];
				$update['globalNotes'] = $this->groupData['groupNotes'];
			}
			
			$update['chainmap_id'] = $this->groupData['active_chain_map'];
			
            $update['lastUpdate'] = (int)$recordedLastUpdate;
        }
        else
        {
            $update['error'] = 'You suck';
        }
		
        echo json_encode( $update );
			
        exit();
	}
	
	public function action_notes_save()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');	 
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		
		if(	 !$this->siggyAccessGranted() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}			 		
		
		$notes = strip_tags($_POST['notes']);
		
		$update = array('groupNotes' => $notes);
		
		groupUtils::update_group($this->groupData['groupID'],$update);
		groupUtils::recacheGroup($this->groupData['groupID']);
		
		echo json_encode(time());
		exit();
	}
	
	public function action_sigData($systemID)
	{
		if ($this->request->is_ajax()) 
		{
			$this->profiler = NULL;
			$this->auto_render = FALSE;
			header('content-type: application/json');	 
			header("Cache-Control: no-cache, must-revalidate");
		}

		$sigData = DB::query(Database::SELECT, "SELECT sigID,sig, type, siteID, description, created FROM systemsigs WHERE systemID=:id AND groupID=:group")
									->param(':id', $systemID)
									->param(':group',$this->groupData['groupID'])
									->execute()
									->as_array('sigID');	 
		echo json_encode($sigData);
		exit();
	}
	
	public function action_sigAdd()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate");
		
		if(	 !$this->siggyAccessGranted() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}			 
		
		if( isset($_POST['systemID']) )
		{
			$insert['systemID'] = intval($_POST['systemID']);
			$insert['sig'] = strtoupper($_POST['sig']);
			$insert['description'] = $_POST['desc'];
			$insert['created'] = time();
			$insert['siteID'] = intval($_POST['siteID']);
			$insert['type'] = $_POST['type'];
			$insert['groupID'] = $this->groupData['groupID'];
			
			if( $this->groupData['showSigSizeCol'] )
			{
				$insert['sigSize'] = ( is_numeric( $_POST['sigSize'] ) ? $_POST['sigSize'] : '' );
			}
			
			if( !empty( $this->groupData['charName'] ) )
			{
				$insert['creator'] = $this->groupData['charName'];
			}
			
			$sigID = DB::insert('systemsigs', array_keys($insert) )->values(array_values($insert))->execute();
			
			$this->chainmap->update_system($insert['systemID'], array('lastUpdate' => time(),
																'lastActive' => time() )
										);
			
			miscUtils::increment_stat('adds', $this->groupData);
			
			$insert['sigID'] = $sigID[0];
			echo json_encode(array($sigID[0] => $insert ));
		}
		exit();
	}
	
	public function action_massSigs()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1

		if(	 !$this->siggyAccessGranted() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}

		if( isset($_POST['systemID']) && isset($_POST['blob']) && !empty($_POST['blob']) )
		{
			$sigs = miscUtils::parseIngameSigExport( $_POST['blob'] );
			
			$systemID = intval($_POST['systemID']);
			
			$addedSigs = array();
			
			if( count($sigs) > 0 && count($sigs) < 200 )	//200 is safety limit to prevent attacks, no system should have this many sigs
			{
				$doingUpdate = FALSE;
				foreach( $sigs as $sig )
				{
					$sigData = DB::query(Database::SELECT, "SELECT sigID,sig, type, siteID, description, created FROM systemsigs WHERE systemID=:id AND groupID=:group AND sig=:sig")
												->param(':id', $systemID)
												->param(':group',$this->groupData['groupID'])
												->param(':sig', $sig['sig'] )
												->execute()
												->current();	 
												
					if( isset($sigData['sigID']) )
					{
						if(  $sig['type'] != 'none' || $sig['siteID'] != 0 )
						{
							$doingUpdate = TRUE;
							$update = array(
											'updated' => time(),
											'siteID' => ( $sig['siteID'] != 0 ) ? $sig['siteID'] : $sigData['siteID'],
											'type' => $sig['type']
											);
											
							if( !empty( $this->groupData['charName']) )
							{
								$update['lastUpdater'] = $this->groupData['charName'];
							}
							DB::update('systemsigs')->set( $update )->where('sigID', '=', $sigData['sigID'])->execute();
						}
					}
					else
					{
						$insert = array();
						$insert['systemID'] = intval($systemID);
						$insert['sig'] = strtoupper($sig['sig']);
						$insert['description'] = "";
						$insert['created'] = time();
						$insert['siteID'] = intval($sig['siteID']);
						$insert['type'] = $sig['type'];
						$insert['groupID'] = $this->groupData['groupID'];
						$insert['sigSize'] = "";	//need to return this value for JS to fail gracefully

						if( !empty( $this->groupData['charName'] ) )
						{
							$insert['creator'] = $this->groupData['charName'];
						}
						$sigID = DB::insert('systemsigs', array_keys($insert) )->values(array_values($insert))->execute();
			
						$insert['sigID'] = $sigID[0];
						
						$addedSigs[ $sigID[0] ] = $insert;
									
						if( $insert['type'] != 'none' )
						{
							miscUtils::increment_stat('adds', $this->groupData);
						}
					}
				}
				
				if( $doingUpdate )
				{
					$this->chainmap->update_system($systemID, array('lastUpdate' => time(),'lastActive' => time() ) );
				}
				
				echo json_encode($addedSigs);
			}
		}		
		exit();
	}
	
	public function action_sigEdit()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		
		if( isset($_POST['sigID']) )
		{
			$update['sig'] = strtoupper($_POST['sig']);
			$update['description'] = $_POST['desc'];
			$update['updated'] = time();
			$update['siteID'] = isset($_POST['siteID']) ? intval($_POST['siteID']) : 0;
			$update['type'] = $_POST['type'];
			
			if( $this->groupData['showSigSizeCol'] )
			{
					$update['sigSize'] = ( is_numeric( $_POST['sigSize'] ) ? $_POST['sigSize'] : ''  );
			}
			
			if( !empty( $this->groupData['charName']) )
			{
				$update['lastUpdater'] = $this->groupData['charName'];
			}
			
			$id = intval($_POST['sigID']);
			
			DB::update('systemsigs')->set( $update )->where('sigID', '=', $id)->execute();
			$this->update_system($_POST['systemID'], array('lastUpdate' => time(), 'lastActive' => time() ) );
			
			miscUtils::increment_stat('updates', $this->groupData);
			
			echo json_encode('1');
		}
		die();
	}
	
	public function action_sigRemove()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		
		if( isset($_POST['sigID']) )
		{
			$id = intval($_POST['sigID']);
			$sigData = DB::query(Database::SELECT, 'SELECT *,ss.name as systemName FROM	 systemsigs s 
													INNER JOIN solarsystems ss ON ss.id = s.systemID
													WHERE s.sigID=:sigID AND s.groupID=:groupID')
									->param(':groupID', $this->groupData['groupID'])
									->param(':sigID', $id)
									->execute()
									->current();			
			
			DB::delete('systemsigs')->where('sigID', '=', $id)->execute();
			
			$this->chainmap->update_system($_POST['systemID'], array('lastUpdate' => time() ));
			
			$message = $this->groupData['charName'].' deleted sig "'.$sigData['sig'].'" from system '.$sigData['systemName'];;
			if( $sigData['type'] != 'none' )
			{
				$message .= '" which was of type '.strtoupper($sigData['type']);
			}
			
			groupUtils::log_action($this->groupData['groupID'], 'delsig', $message);
			echo json_encode('1');
		}
		die();
	}
	
	public function action_saveSystemOptions()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');	 
		
		if( isset($_POST['systemID']) )
		{
			$id = intval($_POST['systemID']);
			
			$this->chainmap->update_system($_POST['systemID'], array('displayName' => trim($_POST['label']), 'activity' => intval($_POST['activity']) ) );
			echo json_encode('1');
		
			$system_data = $this->getSystemData($id);
			
			$log_message = sprintf('%s edited system %s; Display Name: %s, Activity Level %d', $this->groupData['charName'], $system_data['name'],  trim($_POST['label']),intval($_POST['activity']) );
			groupUtils::log_action($this->groupData['groupID'],'editsystem', $log_message );
			
			$this->chainmap->rebuild_map_data_cache();
		}
		exit();
	}
	
	public function action_autocompleteWH()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		
		$q = '';
		if ( isset($_GET['q']) ) 
		{
			$q = trim(strtolower($_GET['q']));
		}
		
		if ( empty($q) ) 
		{
			return;
		}
		
		$customsystems = DB::select( array('solarsystems.name', 'name'), array('activesystems.displayName', 'displayName') )
										->from('activesystems')
										->join('solarsystems', 'LEFT')
										->on('activesystems.systemID', '=', 'solarsystems.id')
										->where('displayName','like',$q.'%')
										->where('groupID', '=', $this->groupData['groupID'])
										->where('chainmap_id', '=', $this->groupData['active_chain_map'])
										->execute()
										->as_array();
									
		foreach($customsystems as $system)
		{
			print $system['displayName']."|".$system['name']."\n";
		}

		$systems = DB::select(array('solarsystems.name', 'name'),array('regions.regionName', 'regionName'), array('solarsystems.sysClass', 'class'))
								->from('solarsystems')
								->join('regions', 'LEFT')
								->on('solarsystems.region', '=', 'regions.regionID')
								->where('name','like',$q.'%')
								->execute()
								->as_array();
		
		foreach($systems as $system)
		{
			if( $system['class'] >= 7 )
			{
				print $system['name']."|".$system['regionName']."\n";
			}
			else
			{
				print $system['name']."|\n";
			}
		}
		die();
	}
}