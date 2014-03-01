<?php defined('SYSPATH') or die('No direct script access.');

require_once APPPATH.'classes/FrontController.php';

class Controller_Siggy extends FrontController 
{
	public $trusted = false;
	
	public $template = 'template/main';

	public function action_index()
	{
    
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		$ssname = $this->request->param('ssname', '');
	
		$mapOpen = ( isset($_COOKIE['mapOpen'] ) ? intval($_COOKIE['mapOpen']) : 0 );
        $statsOpen = ( isset($_COOKIE['system_stats_open'] ) ? intval($_COOKIE['system_stats_open']) : 0 );
        
		if( !empty($ssname) )
		{
				$ssname = preg_replace("/[^a-zA-Z0-9]/", "", $ssname);
				$requested = true;
		}
		else
		{
				$ssname = ($this->igb ? $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'] : 'Jita');
				$requested = false;
		}

		$view = View::factory('siggy/siggyMain');
		
		$view->initialSystem = false;
		$view->group = $this->groupData;
		$view->requested = $requested;
        $view->statsOpen = $statsOpen;
		if( $ssname )
		{
				$sysData = $this->getSystemData($ssname);
				if( $sysData )
				{
						$view->systemData = $sysData;
						$view->initialSystem = true;
				}
				else
				{
						$requested = false;
						$view->systemData = array('id' => 30000142, 'name' => 'Jita');
						$view->initialSystem = true;
				}
		}
		
		//$sessionID = $this->__generateSession();
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
                            ->param(':group', $this->groupData['groupID'])->execute()->as_array();
        
        
        
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
                                ->param(':group', $this->groupData['groupID'])->execute()->as_array();
                                
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
		if( $this->request->action() == 'GroupAuth' || $this->request->action() == "switchMembership" )
		{
			$this->noAutoAuthRedirects = TRUE;
		}
	
		parent::before();
	}
	
	public function after()
	{
		if( is_object($this->template)  )
		{
			if( $this->request->action() == 'GroupAuth' )
			{
				$this->template->siggyMode = false;
			}
			else
			{
				$this->template->siggyMode = true;
			}
		}
	
		parent::after();
	}
	
	public function action_systemData($name='')
	{
			if ($this->request->is_ajax()) {
					$this->profiler = NULL;
					$this->auto_render = FALSE;
					header('content-type: application/json');
			}
			
			if( !empty($name ) )
			{
				$systemData = $this->getSystemData($name);
				echo json_encode($systemData);
				die();

			}
		
	}
	
	private function getSystemList()
	{
			//removed	 ORDER BY sa.inUse DESC, sa.lastActive DESC because the client sorts it anyway
			$time = time()-60*60*24;
			
			$extra = '';
			if( !$this->shouldSysListShowReds() )
			{
				$extra = 'AND sa.inUse = 1 ';
			}
			
			$systems = DB::query(Database::SELECT, "SELECT sa.systemID,ss.name,ss.sysClass,sa.displayName,sa.inUse,sa.lastActive,sa.activity FROM activesystems sa 
			 INNER JOIN solarsystems ss ON ss.id = sa.systemID
			WHERE sa.groupID=:group AND sa.subGroupID=:subgroup AND sa.lastActive >=:time ".$extra."AND sa.lastActive != 0")
										->param(':group', $this->groupData['groupID'])->param(':subgroup', $this->groupData['subGroupID'])->param(':time', $time)->execute()->as_array('systemID');	 
										
			return $systems;
	}

	private function getSystemData( $name )
	{
			$systemQuery = DB::query(Database::SELECT, "SELECT ss.*,se.effectTitle, r.regionName, c.constellationName, 
														COALESCE(sa.displayName,'') as displayName,
														COALESCE(sa.inUse,0) as inUse,
														COALESCE(sa.activity,0) as activity
														FROM solarsystems ss 
														INNER JOIN systemeffects se ON ss.effect = se.id
														INNER JOIN regions r ON ss.region = r.regionID
														INNER JOIN constellations c ON ss.constellation = c.constellationID
														LEFT OUTER JOIN activesystems sa ON (ss.id = sa.systemID  AND sa.groupID = :group AND sa.subGroupID=:subgroup)
														WHERE ss.name=:name")
										->param(':name', $name)
										->param(':group', $this->groupData['groupID'])
										->param(':subgroup', $this->groupData['subGroupID'])
										->execute();
			
			//system exists
			
			$systemData = $systemQuery->current();
			if( !$systemData['id'] )
			{
				return FALSE;
			}
			
			$systemData['staticData'] = array();
										
			$staticData = DB::query(Database::SELECT, "SELECT st.* FROM staticmap sm 
			INNER JOIN statics st ON sm.staticID = st.staticID
			WHERE sm.systemID=:id")
										->param(':id', $systemData['id'])->execute()->as_array();	 
			
			if( count( $staticData ) > 0 )
			{
				$systemData['staticData'] = $staticData;
			}
			
			$end = miscUtils::getHourStamp();
			$start = miscUtils::getHourStamp(-24);
			$apiData = DB::query(Database::SELECT, "SELECT hourStamp, jumps, kills, npcKills FROM apihourlymapdata WHERE systemID=:system AND hourStamp >= :start AND hourStamp <= :end ORDER BY hourStamp asc LIMIT 0,24")
											->param(':system', $systemData['id'])->param(':start', $start)->param(':end', $end)->execute()->as_array('hourStamp');	 
			
			$trackedJumps = DB::query(Database::SELECT, "SELECT hourStamp, jumps FROM jumpstracker WHERE systemID=:system AND groupID=:group AND hourStamp >= :start AND hourStamp <= :end ORDER BY hourStamp asc LIMIT 0,24")
											->param(':system', $systemData['id'])->param(':group', $this->groupData['groupID'])->param(':start', $start)->param(':end', $end)->execute()->as_array('hourStamp');	 
			
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
			
			$hubJumps = DB::query(Database::SELECT, " SELECT ss.id as system_id, pr.num_jumps,ss.name as destination_name FROM precomputedroutes pr
														INNER JOIN solarsystems ss ON ss.id = pr.destination_system
														 WHERE pr.origin_system=:system AND pr.destination_system != :system
														 ORDER BY pr.num_jumps ASC")
											->param(':system', $systemData['id'])->execute()->as_array();
			
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
                                                WHERE p.group_id=:group_id AND p.pos_system_id=:system_id")
										->param(':group_id', $this->groupData['groupID'])
                                        ->param(':system_id', $systemID)
                                        ->execute()->as_array('pos_id');	

        return $poses;
    }
	
	
    private function getDScans( $systemID )
    {
        $dscans = DB::query(Database::SELECT, "SELECT dscan_id, dscan_title, dscan_date
												FROM dscan
                                                WHERE group_id=:group_id AND system_id=:system_id")
										->param(':group_id', $this->groupData['groupID'])
                                        ->param(':system_id', $systemID)
                                        ->execute()->as_array();	

        return $dscans;
    }
	
	
	private function rebuildMapCache()
	{
		return groupUtils::rebuildMapCache($this->groupData['groupID'], $this->groupData['subGroupID']);
	}
	
	private function getHomeSystems()
	{			
		$homeSystems = array();
		if( $this->groupData['subGroupID'] != 0 )
		{
			if( $this->groupData['sgHomeSystemIDs'] != '' )
			{
				$homeSystems = explode(',', $this->groupData['sgHomeSystemIDs']);
			}
		}
		else
		{
			if( $this->groupData['homeSystemIDs'] != '' )
			{
				$homeSystems = explode(',', $this->groupData['homeSystemIDs']);
			}
		}
		
		return $homeSystems;
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
											->param(':charID',  $this->groupData['charID'])->execute()->as_array('profileID');	
											
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
	
	private function whHashByID($to, $from)
	{
		if( $to < $from )
		{
			return md5( intval($to) . intval($from) );
		}
		else
		{
			return md5( intval($from) . intval($to) );
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
		
		$kspaceJump = DB::query(Database::SELECT, "SELECT `fromSolarSystemID`, `toSolarSystemID` FROM mapsolarsystemjumps WHERE (fromSolarSystemID=:sys1 AND toSolarSystemID=:sys2) OR
																																(fromSolarSystemID=:sys2 AND toSolarSystemID=:sys1)")
						->param(':sys1', $origin)
						->param(':sys2', $dest)
						->execute()->current();
						
		if( isset($kspaceJump['fromSolarSystemID']) )
		{
			return;
		}
		
		
    
        $whHash = $this->whHashByID($origin, $dest);
		
		$connection = DB::query(Database::SELECT, "SELECT `hash` FROM wormholes WHERE hash=:hash AND groupID=:group AND subGroupID=:subGroupID")
						->param(':hash', $whHash)
						->param(':group', $this->groupData['groupID'])
						->param(':subGroupID', $this->groupData['subGroupID'])
						->execute()->current();

		if( !isset($connection['hash'] ) )
		{
			//new wh
			$this->_addSystemToMap($whHash, $origin, $dest);
		}
		else
		{
			//existing wh
			DB::update('wormholes')
				->set( array('lastJump' => time()) )
				->where('hash', '=', $whHash)
				->where('groupID', '=', $this->groupData['groupID'])
				->where('subGroupID', '=', $this->groupData['subGroupID'])
				->execute();
		}
		
                        

        if( $this->groupData['jumpLogEnabled']  && !empty( $_SERVER['HTTP_EVE_SHIPTYPEID'] ) )
        {
            $charID = ( $this->groupData['jumpLogRecordNames'] ? $_SERVER['HTTP_EVE_CHARID'] : 0 );
            $charName = ( $this->groupData['jumpLogRecordNames'] ? $_SERVER['HTTP_EVE_CHARNAME'] : '' );
            $jumpTime = ( $this->groupData['jumpLogRecordTime'] ? time() : 0 );
            DB::query(Database::INSERT, 'INSERT INTO wormholetracker (`whHash`, `origin`, `destination`, `groupID`, `subGroupID`, `time`, `shipTypeID`,`charID`, `charName`) VALUES(:hash, :origin, :dest, :groupID, :subGroupID, :time,:shipTypeID,:charID,:charName)')
                ->param(':hash', $whHash )
                ->param(':dest', $dest)
                ->param(':origin', $origin )
                ->param(':groupID', $this->groupData['groupID'] )
                ->param(':subGroupID', $this->groupData['subGroupID'] )
                ->param(':time', $jumpTime )
                ->param(':shipTypeID',  $_SERVER['HTTP_EVE_SHIPTYPEID'] )
                ->param(':charID', $charID)
                ->param(':charName', $charName)
                ->execute();
        }

	}
	
	private function _addSystemToMap($whHash, $sys1,$sys2, $eol=0, $mass=0)
	{
		$sys1Connections = $this->__getConnectedSystems($sys1);	
		$sys2Connections = $this->__getConnectedSystems($sys2);	
		
		$sys1Count = count($sys1Connections);
		$sys2Count = count($sys2Connections);
		
		if( $sys1Count == 0 )
		{
			$this->__placeSystem($sys2,$sys2Connections, $sys1);
		}
		else if( $sys2Count == 0 )
		{
			//sys2 is "new"
			 $this->__placeSystem($sys1,$sys1Connections, $sys2);
		}
		else if( $sys1Count == 0 && $sys2Count == 0 )
		{
			//both are new
			//we just map one
			//this will probably change at some point soon
			 $this->__placeSystem($sys1,$sys1Connections, $sys2);
		}
		
		//default case is both systems already mapped, so jsut connect them
							
						 
		DB::query(Database::INSERT, 'INSERT INTO wormholes (`hash`, `to`, `from`, `groupID`, `subGroupID`, `lastJump`, `eol`, `mass`) VALUES(:hash, :to, :from, :groupID, :subGroupID, :lastJump, :eol, :mass)')
						->param(':hash', $whHash )
						->param(':to', $sys1 )
						->param(':from', $sys2)
						->param(':eol', $eol )
						->param(':mass', $mass )
						->param(':groupID', $this->groupData['groupID'] )
						->param(':subGroupID', $this->groupData['subGroupID'] )
						->param(':lastJump', time() )->execute();
		$this->rebuildMapCache();
	}
	
	private function __getConnectedSystems($system)
	{
		return DB::query(Database::SELECT, "SELECT x,y FROM activesystems 
														WHERE groupID=:group AND
														subGroupID=:subGroupID AND
														systemID IN (SELECT
																		CASE WHEN w.`to`=:sys
																			THEN w.`from`
																			ELSE w.`to`
																		END AS `connected_system` 
																		FROM wormholes w
																		WHERE w.`to`=:sys OR w.`from`=:sys AND w.groupID=:group AND w.subGroupID=:subGroupID)")
						->param(':sys', $system)
						->param(':group', $this->groupData['groupID'])
						->param(':subGroupID', $this->groupData['subGroupID'])
						->execute()
						->as_array();	
	}
	
	private function __placeSystem($originSys, $originSystems, $systemToBePlaced)
	{
		$sysPos = NULL;
		$sysData = DB::query(Database::SELECT, "SELECT * FROM activesystems 
														WHERE groupID=:group AND
														subGroupID=:subGroupID AND
														systemID=:sys")
						->param(':sys', $originSys)
						->param(':group', $this->groupData['groupID'])
						->param(':subGroupID', $this->groupData['subGroupID'])
						->execute()
						->current();
										
		$spots = $this->__generatePossibleSystemLocations($sysData['x'], $sysData['y']);

		foreach($spots as $spot)
		{
			$intersect = false;
			foreach($originSystems as $sys)
			{
				if( $this->__doBoxesIntersect($this->__coordsToBB($spot['x'],$spot['y']), $this->__coordsToBB($sys['x'],$sys['y'])) )
				{
					$intersect = true;
				}
			}
			
			if( !$intersect )
			{
				//winnar!
				$sysPos = $spot;
				break;
			}
		}
		
		//if we didnt find a spot, just use the first one and call it a day
		if( $sysPos == NULL )
		{
			$sysPOS = $spots[0];
		}
		
			
		$this->__setActiveSystem($systemToBePlaced, array( 'x' => intval($sysPos['x']),
															'y' => intval($sysPos['y']),
															'lastUpdate' => time() ) );
	}
	
	private function __setActiveSystem($systemID, $data)
	{
		if( !(count($data) > 0) )
		{
			return;
		}
		
		$extraIns = '';
		$extraInsVal = '';
		$extraUp = array();
		
		foreach($data as $k => $v)
		{
			$extraIns .= ',`'.$k.'`';
			$extraInsVal .= ',:'.$k;
			$extraUp[] = $k.'=:'.$k;
		}
		
		$extraUp = implode(',', $extraUp);
		
		$q = DB::query(Database::INSERT, 'INSERT INTO activesystems (`systemID`, `groupID`, `subGroupID`'.$extraIns.')
									 VALUES(:systemID, :groupID, :subGroupID'.$extraInsVal.')
									 ON DUPLICATE KEY UPDATE '.$extraUp)
							->param(':systemID', $systemID )
							->param(':groupID', $this->groupData['groupID'] )
							->param(':subGroupID', $this->groupData['subGroupID'] );
			
		foreach($data as $k => $v)
		{
			$q->param(':'.$k, $v);
		}
		$q->execute();
	}
	
	
	private function __generatePossibleSystemLocations($x, $y)
	{
		$originBB = $this->__coordsToBB($x,$y);
		
		$cX = $originBB['left'];
		$cY = $originBB['top'];
		
		$ret = array();
		
		$positions = 8;
		$rotation = 2 * M_PI / $positions;
		for($position = 0; $position < $positions; ++$position)
		{
			$spot_rotation = $position * $rotation;
			$newx = $cX + 125*cos($spot_rotation);
			$newy = $cY + 85*sin($spot_rotation);
			
			
			//limited horizontal span
			if( $newy < 380 && $newy > 0 && $newx > 0 )
			{
				$ret[] = array('x' => $newx, 'y' => $newy);
			}
			
		}
		
		return $ret;
	}
	
	private function __doBoxesIntersect($a, $b)
	{
		
		$x1 = $a['left'];
		$x2 = $a['left'] + $a['width'];
		$y1 = $a['bottom'];
		$y2 = $a['bottom'] + $a['height'];
		
		$a1 = $b['left'];
		
		$a2 = $b['left'] + $b['width'];
		$b1 =  $b['bottom'];
		$b2 =  $b['bottom'] +  $b['height'];

			return  ( ($x1 <= $a1 && $a1 <= $x2) && ($y1 <= $b1 && $b1 <= $y2) ) ||
			        ( ($x1 <= $a2 && $a2 <= $x2) && ($y1 <= $b1 && $b1 <= $y2) ) ||
			        ( ($x1 <= $a1 && $a1 <= $x2) && ($y1 <= $b2 && $b2 <= $y2) ) ||
			        ( ($x1 <= $a2 && $a1 <= $x2) && ($y1 <= $b2 && $b2 <= $y2) ) ||	
			        ( ($a1 <= $x1 && $x1 <= $a2) && ($b1 <= $y1 && $y1 <= $b2) ) ||
			        ( ($a1 <= $x2 && $x2 <= $a2) && ($b1 <= $y1 && $y1 <= $b2) ) ||
			        ( ($a1 <= $x1 && $x1 <= $a2) && ($b1 <= $y2 && $y2 <= $b2) ) ||
			        ( ($a1 <= $x2 && $x1 <= $a2) && ($b1 <= $y2 && $y2 <= $b2) );
	}
	
	private function __coordsToBB($x,$y)
	{
		return array( 'left' => $x,
					  'top' => $y,
					  'width' => 78,
					  'height' => 38,
					   'right' => $x+78,
					   'bottom' => $y+38 );
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
			->param(':groupID', $this->groupData['groupID'])->param(':hash', $hash)->execute()->as_array();
			
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
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
      //  ob_start( 'ob_gzhandler' );
        

        if(	!$this->siggyAccessGranted() )
        {
            echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
            exit();
        }			 
        
        $chainMapOpen = ( isset($_GET['mapOpen']) ? intval($_GET['mapOpen']) : 0 );
        
        $update = array('systemUpdate' => 0, 'sigUpdate' => 0, 'globalNotesUpdate' => 0, 'mapUpdate' => 0, 'acsid' => 0, 'acsname' =>'');
        
        
        if( isset( $_GET['lastUpdate'] ) && isset( $_GET['systemID'] ) && $_GET['systemID'] != 0 )
        {
            $currentSystemID = intval($_GET['systemID']);
            $forceUpdate = $_GET['forceUpdate'] == 'true' ? 1 : 0;
            $_GET['lastUpdate'] = intval($_GET['lastUpdate']);
            $freeze = intval( $_GET['freezeSystem'] );
        //	$detectedSystemID = intval($_SERVER['HTTP_EVE_SOLARSYSTEMID']);
        
            $newSystemData = array();
            $update['acsid'] = $lastSystemID = $actualCurrentSystemID = intval($_GET['acsid']);
            $update['acsname'] = $lastSystemName = $actualCurrentSystemName = $_GET['acsname'];

            if( $this->igb )
            {
                if( ($actualCurrentSystemID != $_SERVER['HTTP_EVE_SOLARSYSTEMID'] ) )
                {
                    //$newSystemData = $this->getSystemData( $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'] );
                    //fix me once CCP stops being dumb
                    
                        $update['acsid'] = $actualCurrentSystemID = $_SERVER['HTTP_EVE_SOLARSYSTEMID'];
                        $update['acsname'] = $actualCurrentSystemName = $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'];
                    
                    //
                    
                    
                    if( $this->groupData['recordJumps'] && $actualCurrentSystemID != 0 && $lastSystemID != 0 )
                    {
                            $hourStamp = miscUtils::getHourStamp();
                            DB::query(Database::INSERT, 'INSERT INTO jumpsTracker (`systemID`, `groupID`, `hourStamp`, `jumps`) VALUES(:systemID, :groupID, :hourStamp, 1) ON DUPLICATE KEY UPDATE jumps=jumps+1')
                                                ->param(':hourStamp', $hourStamp )->param(':systemID', $lastSystemID )->param(':groupID', $this->groupData['groupID'] )->execute();						

                            DB::query(Database::INSERT, 'INSERT INTO jumpsTracker (`systemID`, `groupID`, `hourStamp`, `jumps`) VALUES(:systemID, :groupID, :hourStamp, 1) ON DUPLICATE KEY UPDATE jumps=jumps+1')
                                                ->param(':hourStamp', $hourStamp )->param(':systemID', $actualCurrentSystemID )->param(':groupID', $this->groupData['groupID'] )->execute();									
                    }
                
                    if( ($lastSystemID != $actualCurrentSystemID) && $actualCurrentSystemID != 0 && !empty($lastSystemID) )
                    {
                        $this->__wormholeJump($lastSystemID, $actualCurrentSystemID);
                    }
                    
                }					 
            }
					
            if( $forceUpdate || ( $this->igb && $_GET['systemName'] != $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'] ) )
            {
                //$newSystemData = $this->getSystemData( $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'] );
                //if specific system isn't picked then load new one
                if( !$freeze && $this->igb )
                {
                    $update['systemData'] = $this->getSystemData( $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'] );
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
                    $update['systemData'] = $this->getSystemData( $_GET['systemName'] );
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
      
                      DB::query(Database::INSERT, 'INSERT INTO chartracker (`charID`, `charName`, `currentSystemID`,`groupID`,`subGroupID`,`lastBeep`, `broadcast`,`shipType`, `shipName`) VALUES(:charID, :charName, :systemID, :groupID, :subGroupID, :lastBeep, :broadcast, :shipType, :shipName)'
                                    . 'ON DUPLICATE KEY UPDATE lastBeep = :lastBeep, currentSystemID = :systemID, broadcast = :broadcast, shipType = :shipType, shipName = :shipName')
                    ->param(':charID', $_SERVER['HTTP_EVE_CHARID'] )->param(':charName', $_SERVER['HTTP_EVE_CHARNAME'] )
                    ->param(':broadcast', $broadcast )
                    ->param(':systemID', $actualCurrentSystemID )
                    ->param(':groupID', $this->groupData['groupID'] )
                    ->param(':shipType', isset($_SERVER['HTTP_EVE_SHIPTYPEID']) ? $_SERVER['HTTP_EVE_SHIPTYPEID'] : 0 )
                    ->param(':shipName', isset($_SERVER['HTTP_EVE_SHIPNAME']) ? htmlentities($_SERVER['HTTP_EVE_SHIPNAME']) : '' )
                    ->param(':subGroupID', $this->groupData['subGroupID'] )
                    ->param(':lastBeep', time() )->execute();			
 
            }
					
			$this->mapData = groupUtils::getMapCache( $this->groupData['groupID'], $this->groupData['subGroupID'] );
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
                                                                        WHERE ct.groupID = :groupID AND ct.subGroupID = :subGroupID AND ct.broadcast=1 AND
                                                                            ct.currentSystemID IN(".implode(',',$this->mapData['systemIDs']).") AND ct.lastBeep >= :lastBeep 
                                                                            ORDER BY ct.charName ASC")
                                                ->param(':lastBeep', time()-60)
                                                ->param(':groupID', $this->groupData['groupID'])
                                                ->param(':subGroupID', $this->groupData['subGroupID'])
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
                    
                    if( $_GET['mapLastUpdate'] != $this->mapData['updateTime'] )
                    {
                        $update['chainMap']['systems'] = $this->mapData['systems'];
                        $update['chainMap']['wormholes'] = $this->mapData['wormholes'];
                        $update['mapUpdate'] = (int) 1;
                    }
                    $update['chainMap']['lastUpdate'] = $this->mapData['updateTime'];
            }
					
            $activeSystemQuery = DB::query(Database::SELECT, 'SELECT lastUpdate FROM activesystems WHERE systemID=:id AND groupID=:group AND subGroupID=:subgroup')
												->param(':id', $currentSystemID)
												->param(':group',$this->groupData['groupID'])
												->param(':subgroup', $this->groupData['subGroupID'])
												->execute();

            $activeSystem = $activeSystemQuery->current();
            $recordedLastUpdate = ($activeSystem['lastUpdate'] > 0) ? $activeSystem['lastUpdate']: time();

            if( ($_GET['lastUpdate'] < $recordedLastUpdate) || ( $_GET['lastUpdate'] == 0 ) || $forceUpdate || $update['systemUpdate'] )
            {
                $additional = '';
                if( $this->groupData['showSigSizeCol'] )
                {
                        $additional .= ',sigSize';
                }
                $update['sigData'] = DB::query(Database::SELECT, "SELECT sigID,sig, type, siteID, description, created, creator,updated,lastUpdater".$additional." FROM systemsigs WHERE systemID=:id AND groupID=:group")
                                 ->param(':id', $currentSystemID)->param(':group', $this->groupData['groupID'])->execute()->as_array('sigID');	

                $update['sigUpdate'] = (int) 1;
            }
					
            if( $this->groupData['subGroupID'] != 0 )
            {
                if( ( $_GET['lastGlobalNotesUpdate'] ) < $this->groupData['sgNotesTime'] )
                {
                    $update['globalNotesUpdate'] = (int) 1;
                    $update['lastGlobalNotesUpdate'] = (int) $this->groupData['sgNotesTime'];
                    $update['globalNotes'] = $this->groupData['sgNotes'];
                }
            }
            else
            {
                if( ( $_GET['lastGlobalNotesUpdate'] ) < $this->groupData['lastNotesUpdate'] )
                {
                    $update['globalNotesUpdate'] = (int) 1;
                    $update['lastGlobalNotesUpdate'] = (int) $this->groupData['lastNotesUpdate'];
                    $update['globalNotes'] = $this->groupData['groupNotes'];
                }
            }
            
            
            $update['lastUpdate'] = $recordedLastUpdate;
        }
        else
        {
            $update['error'] = 'You suck';
        }
        echo json_encode( $update );
			
		// echo View::factory('profiler/stats'); 
        exit();
	}
	
	public function action_globalNotesSave()
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
		if( $this->groupData['subGroupID'] != 0 )
		{
			$update['sgNotes'] = $notes;
			$update['sgNotesTime'] = time();
			DB::update('subgroups')->set( $update )->where('subGroupID', '=', $this->groupData['subGroupID'])->execute();
			groupUtils::recacheSubGroup($this->groupData['groupID']);
			
			echo json_encode($update['sgNotesTime']);
		}
		else
		{
			$update['groupNotes'] = $notes;
			$update['lastNotesUpdate'] = time();
			DB::update('groups')->set( $update )->where('groupID', '=', $this->groupData['groupID'])->execute();
			groupUtils::recacheGroup($this->groupData['groupID']);
			
			echo json_encode($update['lastNotesUpdate']);
		}
		exit();
	}
	
	public function action_sigData($systemID)
	{
			if ($this->request->is_ajax()) 
			{
					$this->profiler = NULL;
					$this->auto_render = FALSE;
					header('content-type: application/json');	 
					header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			}

			$sigData = DB::query(Database::SELECT, "SELECT sigID,sig, type, siteID, description, created FROM systemsigs WHERE systemID=:id AND groupID=:group")
										->param(':id', $systemID)->param(':group',$this->groupData['groupID'])->execute()->as_array('sigID');	 
			echo json_encode($sigData);
			exit();
	}
	
	
	
	public function action_sigAdd()
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
			
			//DB::update('activesystems')->set( array('lastUpdate' => time(),'lastActive' => time() ) )->where('systemID', '=', $insert['systemID'])->where('groupID', '=', $this->groupData['groupID'])->where('subGroupID', '=', $this->groupData['subGroupID'])->execute();
			$this->__setActiveSystem($insert['systemID'], array('lastUpdate' => time(),
																'lastActive' => time() )
																);
			
			if( $this->groupData['statsEnabled'] )
			{
				DB::query(Database::INSERT, 'INSERT INTO stats (`charID`,`charName`,`groupID`,`subGroupID`,`dayStamp`,`adds`) VALUES(:charID, :charName, :groupID, :subGroupID, :dayStamp, 1) ON DUPLICATE KEY UPDATE adds=adds+1')
									->param(':charID',  $this->groupData['charID'])->param(':charName', $this->groupData['charName'] )
									->param(':groupID', $this->groupData['groupID'] )->param(':subGroupID', $this->groupData['subGroupID'] )->param(':dayStamp', miscUtils::getDayStamp() )->execute();
	
			}
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
												->param(':id', $systemID)->param(':group',$this->groupData['groupID'])->param(':sig', $sig['sig'] )->execute()->current();	 
												
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
									
						if( $this->groupData['statsEnabled'] && $insert['type'] != 'none' )
						{
							DB::query(Database::INSERT, 'INSERT INTO stats (`charID`,`charName`,`groupID`,`subGroupID`,`dayStamp`,`adds`) VALUES(:charID, :charName, :groupID, :subGroupID, :dayStamp, 1) ON DUPLICATE KEY UPDATE adds=adds+1')
												->param(':charID',  $this->groupData['charID'])->param(':charName', $this->groupData['charName'] )
												->param(':groupID', $this->groupData['groupID'] )->param(':subGroupID', $this->groupData['subGroupID'] )->param(':dayStamp', miscUtils::getDayStamp() )->execute();
				
						}						
						
					}
				}
				
				if( $doingUpdate )
				{
					$this->__setActiveSystem($systemID, array('lastUpdate' => time(),'lastActive' => time() ));
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
			$this->__setActiveSystem($_POST['systemID'], array('lastUpdate' => time(),'lastActive' => time() ));
			
			if( $this->groupData['statsEnabled'] )
			{
				DB::query(Database::INSERT, 'INSERT INTO stats (`charID`,`charName`,`groupID`,`subGroupID`,`dayStamp`,`updates`) VALUES(:charID, :charName, :groupID, :subGroupID, :dayStamp, 1) ON DUPLICATE KEY UPDATE updates=updates+1')
									->param(':charID',  $this->groupData['charID'] )->param(':charName', $this->groupData['charName'] )
									->param(':groupID', $this->groupData['groupID'] )->param(':subGroupID', $this->groupData['subGroupID'] )->param(':dayStamp', miscUtils::getDayStamp() )->execute();			
			}
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
			WHERE s.sigID=:sigID AND s.groupID=:groupID')->param(':groupID', $this->groupData['groupID'])->param(':sigID', $id)->execute()->current();			
			
			DB::delete('systemsigs')->where('sigID', '=', $id)->execute();
			
			$this->__setActiveSystem($_POST['systemID'], array('lastUpdate' => time() ));
			
			$message = $this->groupData['charName'].' deleted sig "'.$sigData['sig'].'" from system '.$sigData['systemName'];;
			if( $sigData['type'] != 'none' )
			{
				$message .= '" which was of type '.strtoupper($sigData['type']);
			}
			$this->__logAction('delsig', $message );
			echo json_encode('1');
		}
		die();
	}
	
	public function action_chainMapSave()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');	 
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		
		$systemData = json_decode($_POST['systemData'], TRUE);
		if( count( $systemData ) > 0 )
		{
			foreach( $systemData as $system )
			{
				if( !isset($system['y']) || $system['y'] < 0 || $system['y'] > 400 )
				{
					$system['y'] = 0;
				}
				
				if( !isset($system['x']) || $system['x'] < 0 )
				{
					$system['x'] = 0;
				}
				
				$this->__setActiveSystem($system['id'], array('x' => $system['x'], 'y' => $system['y']));
			}
			
			$this->__logAction('editmap', $this->groupData['charName']. " edited the map");
		
			$this->rebuildMapCache();
		}
		
		exit();
	}
	
	private function __logAction( $type, $message )
	{
		$insert = array( 'groupID' => $this->groupData['groupID'],
										'type' => $type,
										'message' => $message,
										'entryTime' => time()
						);
		DB::insert('logs', array_keys($insert) )->values(array_values($insert))->execute();
	}
	
	public function action_chainMapWHMassDelete()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');	 
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		
		$hashes = json_decode($_POST['hashes']);
		if( is_array($hashes) && count($hashes) > 0 )
		{
			foreach( $hashes as $k =>	 $v )
			{
				$hashes[$k] = "'".($v)."'";
			}
			$hashes = implode(',', $hashes);
			
			$wormholes = DB::query(Database::SELECT, 'SELECT * FROM	 wormholes WHERE hash IN('.$hashes.') AND groupID=:groupID AND subGroupID=:subGroupID')->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->execute();
			$systemIDs = array();
			foreach( $wormholes as $wh )
			{
				$systemIDs[] = $wh['to'];
				$systemIDs[] = $wh['from'];
			}
			$systemIDs = array_unique( $systemIDs );
			
			DB::query(Database::DELETE, 'DELETE FROM wormholes WHERE hash IN('.$hashes.') AND groupID=:groupID AND subGroupID=:subGroupID')->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->execute();
			
			
			DB::query(Database::DELETE, 'DELETE FROM wormholetracker WHERE whHash IN('.$hashes.') AND groupID=:groupID AND subGroupID=:subGroupID')->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->execute();
			
			$message = $this->groupData['charName'].' deleted wormholes with IDs: '.implode(',', $systemIDs);
			$this->__logAction('delwhs', $message );			
			
			$this->sysResetByMap( $systemIDs );
			
			$this->rebuildMapCache();
		}
		exit();
	}
	
	private function sysResetByMap($systemIDs)
	{
		if( !is_array($systemIDs) || !count($systemIDs)	 )
		{
			return;
		}
	
	
		$homeSystems = $this->getHomeSystems();
		
		//only enable this "Feature" if we have a home system, a.k.a. RAGE INSURANCE
		if( !count($homeSystems)	)
		{
			return;
		}
		
		foreach( $systemIDs as $systemID )
		{
			if( !in_array($systemID, $homeSystems) )
			{
				$check = DB::query(Database::SELECT, 'SELECT * FROM	 wormholes WHERE groupID=:groupID AND subGroupID=:subGroupID AND (`to`=:id OR `from`=:id)')->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->param(':id', $systemID)->execute()->current();
				if( !$check['hash'] )
				{ 
					$this->__setActiveSystem($systemID, array('displayName' => '','inUse' => 0 , 'activity' => 0 ) );
				}
			}
		}
	}
	
	public function action_chainMapWHDisconnect()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
	 // header('content-type: application/json');	 
	//	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		
		$hash = ($_POST['hash']);
	 
		$wormhole = DB::query(Database::SELECT, 'SELECT * FROM	wormholes WHERE hash=:hash AND groupID=:groupID AND subGroupID=:subGroupID')->param(':hash',$hash)->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->execute()->current();
				
		if( !$wormhole['hash'] )
		{
			return;
		}		 
				
		DB::query(Database::DELETE, 'DELETE FROM wormholes WHERE hash=:hash AND groupID=:groupID AND subGroupID=:subGroupID')->param(':hash',$hash)->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->execute();
		
		DB::query(Database::DELETE, 'DELETE FROM wormholetracker WHERE whHash=:hash AND groupID=:groupID AND subGroupID=:subGroupID')->param(':hash',$hash)->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->execute();
			
		$message = $this->groupData['charName'].' deleted wormhole between system IDs: '.implode(',', array($wormhole['to'], $wormhole['from']) );
		$this->__logAction('delwh', $message );			
			
		$this->sysResetByMap( array($wormhole['to'], $wormhole['from']) );
		
		$this->rebuildMapCache();
		
	}
	
	
	public function action_chainMapWHSave()
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
		$mode = trim($_POST['mode']);
		if( $mode == 'edit' )
		{
			$update = array();
			$hash = ($_POST['hash']);
			
			$wormhole = DB::query(Database::SELECT, 'SELECT * FROM	wormholes WHERE hash=:hash AND groupID=:groupID AND subGroupID=:subGroupID')->param(':hash',$hash)->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->execute()->current();
					
			if( !$wormhole['hash'] )
			{
				echo json_encode(array('error' => 1, 'errorMsg' => 'Wormhole does not exist.'));
				exit();
			}		 
			
			$update['eol'] = intval($_POST['eol']);
			
			
			$update['mass'] = intval($_POST['mass']);
			
			if( !$wormhole['eol'] && $update['eol'] )
			{
				$update['eolToggled'] = time();
			}
			elseif( $wormhole['eol'] && !$update['eol'] )
			{
				$update['eolToggled'] = 0;
			}
		
			DB::update('wormholes')->set( $update )->where('hash', '=', $hash)->where('groupID', '=', $this->groupData['groupID'])->where('subGroupID', '=', $this->groupData['subGroupID'])->execute();
			$this->rebuildMapCache();
		}
		else
		{
			$fromSys = trim($_POST['fromSys']);
			$fromSysCurrent = intval($_POST['fromSysCurrent']);
			$toSys	= trim($_POST['toSys']);
			$toSysCurrent = intval($_POST['toSysCurrent']);
			
			$errors = array();
			if( !$fromSysCurrent && empty($fromSys) )
			{
				$errors[] = "No 'from' system selected!";
			}
			
			if( !$toSysCurrent && empty($toSys) )
			{
				$errors[] = "No 'to' system selected!";
			}
			
			if( $toSys == $fromSys || ($toSysCurrent && $fromSysCurrent ) )
			{
				$errors[] = "You cannot link a system to itself!";
			}
			
			$fromSysID = 0;
			if( $fromSysCurrent )
			{
				$fromSysID = $_SERVER['HTTP_EVE_SOLARSYSTEMID'];
			}
			elseif( !empty($fromSys) )
			{
				$fromSysID = $this->__findSystemByName($fromSys);
				if( !$fromSysID )
				{
					$errors[] = "The 'from' system could not be looked up by name.";
				}
			}
			
			$toSysID = 0;
			if( $toSysCurrent )
			{
				$toSysID = $_SERVER['HTTP_EVE_SOLARSYSTEMID'];
			}
			elseif( !empty($toSys) )
			{
				$toSysID = $this->__findSystemByName($toSys);
				if( !$toSysID )
				{
					$errors[] = "The 'to' system could not be looked up by name.";
				}
			}
            
            if( $fromSysID == $toSysID )
            {
				$errors[] = "You cannot link a system to itself!";
            }
			
			if( count($errors) > 0 )
			{
				echo json_encode(array('success' => 0, 'dataErrorMsgs' => $errors ) );
				exit();
			}
		
			$eol = intval($_POST['eol']);
			$mass = intval($_POST['mass']);	
			
			$whHash = $this->whHashByID($fromSysID , $toSysID);
			$this->_addSystemToMap($whHash, $fromSysID, $toSysID, $eol, $mass);
			/*
			DB::query(Database::INSERT, 'INSERT INTO wormholes (`hash`, `to`, `from`, `eol`, `mass`, `groupID`, `subGroupID`, `lastJump`) VALUES(:hash, :to, :from, :eol, :mass, :groupID, :subGroupID, :lastJump) ON DUPLICATE KEY UPDATE eol=:eol, mass=:mass')
								->param(':hash', $whHash )->param(':to', $toSysID )->param(':from', $fromSysID	)->param(':eol', $eol	 )->param(':mass', $mass	)->param(':groupID', $this->groupData['groupID'] )->param(':subGroupID', $this->groupData['subGroupID'] )->param(':lastJump', time() )->execute();*/
			
            
			$message = $this->groupData['charName'].' added wormhole manually between system IDs' . $fromSysID . ' and ' . $toSysID;
			$this->__logAction('addwh', $message );	
		}
		echo json_encode( array('success' => 1) );
		
		exit();
	}
	
	//allows finding by display name or real name
	private function __findSystemByName($name)
	{
		$name = strtolower($name);
		$systemID = DB::query(Database::SELECT, 'SELECT systemID,displayName FROM activesystems WHERE LOWER(displayName) = :name AND groupID=:groupID AND subGroupID=:subGroupID')
													->param(':name', $name )->param(':groupID', $this->groupData['groupID'])->param(':subGroupID', $this->groupData['subGroupID'])->execute()->get('systemID', 0);
													
		if( $systemID == 0 )
		{
			$systemID = DB::query(Database::SELECT, 'SELECT id,name FROM solarsystems WHERE LOWER(name) = :name')
																->param(':name', $name )->execute()->get('id', 0);
																
		}
		
		return $systemID;
	}
	
	public function action_saveSystemOptions()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');	 
		
		if( isset($_POST['systemID']) )
		{
			$id = intval($_POST['systemID']);
			
			$this->__setActiveSystem($_POST['systemID'], array('displayName' => trim($_POST['label']), 'activity' => intval($_POST['activity']) ) );
			echo json_encode('1');
			
			$this->rebuildMapCache();
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
		
		$customsystems = DB::select(	array('solarsystems.name', 'name'), array('activesystems.displayName', 'displayName') )
								->from('activesystems')
								->join('solarsystems', 'LEFT')->on('activesystems.systemID', '=', 'solarsystems.id')
								->where('displayName','like',$q.'%')->where('groupID', '=', $this->groupData['groupID'])
								->where('subGroupID', '=', $this->groupData['subGroupID'])->execute()->as_array();
		foreach($customsystems as $system)
		{
				print $system['displayName']."|".$system['name']."\n";
		}		

		$systems = DB::select(array('solarsystems.name', 'name'),array('regions.regionName', 'regionName'), array('solarsystems.sysClass', 'class'))->from('solarsystems')->join('regions', 'LEFT')->on('solarsystems.region', '=', 'regions.regionID')->where('name','like',$q.'%')->execute()->as_array();
		
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
	
	
	
	private function shouldSysListShowReds()
	{
		if( $this->groupData['subGroupID'] )
		{
			return $this->groupData['sgSysListShowReds'];
		}
		else
		{
			return $this->groupData['sysListShowReds'];
		}
	}

} // End Welcome

