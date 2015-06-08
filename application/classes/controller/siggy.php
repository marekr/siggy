<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Siggy extends FrontController {

	public $trusted = false;

	public $template = 'template/main';

	public $chainmap = null;

	public function action_index()
	{
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1

		$view = View::factory('siggy/siggy_main');

		$ssname = $this->request->param('ssname', '');

		// set default
		$view->systemData = array('id' => 30000142, 'name' => 'Jita');
		
		// did we have an url requested system?
		$requested = false;
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
		$view->group = Auth::$session->accessData;
		$view->requested = $requested;
		$view->igb = $this->igb;
        $view->settings = $this->template->settings;

		$view->sessionID = '';

		$this->template->content = $view;

		//load chain map html
		$chainMapHTML = View::factory('siggy/chainmap');
		$chainMapHTML->group = Auth::$session->accessData;
		$view->chainMap = $chainMapHTML;

		//load header tools
        $themes = DB::query(Database::SELECT, "SELECT theme_id, theme_name FROM themes
                                                WHERE visibility='all' OR (group_id=:group AND visibility='group')
                                                ORDER BY theme_id ASC")
								->param(':group', Auth::$session->groupID)
								->execute()
								->as_array();

        $view->themes = $themes;
        $view->settings = $this->template->settings;
	}

    public function action_save_character_settings()
    {
        $this->profiler = NULL;
        $this->auto_render = FALSE;

        $charID = Auth::$session->charID;

        if( !empty($charID) )
        {
            $themeID = intval($_POST['theme_id']);
            $combineScanIntel = intval($_POST['combine_scan_intel']);
            $zoom = $_POST['zoom'];
            $language = $_POST['language'];

            $themes = DB::query(Database::SELECT, "SELECT theme_id, theme_name FROM themes
                                                    WHERE theme_id = :themeID AND (visibility='all' OR (group_id=:group AND visibility='group'))")
									->param(':themeID', $themeID)
									->param(':group', Auth::$session->groupID)
									->execute()
									->as_array();

            if( count( $themes ) > 0 )
            {
                DB::query(Database::INSERT, 'REPLACE INTO character_settings (`char_id`, `theme_id`,`combine_scan_intel`,`zoom`,`language`)
				VALUES(:charID, :themeID, :combineScanIntel, :zoom,:language)')
							->param(':charID', $charID )
							->param(':themeID', $themeID)
							->param(':zoom', $zoom)
							->param(':language', $language)
							->param(':combineScanIntel', $combineScanIntel)
							->execute();
            }
        }

        //HTTP::redirect('/');

        exit();
    }

	public function before()
	{
		parent::before();

		if( Auth::$session->accessData['active_chain_map'] )
		{
			$this->chainmap = new Chainmap(Auth::$session->accessData['active_chain_map'],Auth::$session->groupID);
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
									->param(':name', $id)
									->execute()
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
									->param(':group', Auth::$session->groupID)
									->param(':chainmap', Auth::$session->accessData['active_chain_map'])
									->execute()
									->current();

		if( !$systemData['id'] )
		{
			return FALSE;
		}

		$systemData['staticData'] = array();

		$staticData = DB::query(Database::SELECT, "SELECT sm.static_id as id FROM staticmap sm
													WHERE sm.system_id=:id")
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
									->param(':group', Auth::$session->groupID)
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
										->param(':group_id', Auth::$session->groupID)
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
										->param(':group_id', Auth::$session->groupID)
                                        ->param(':system_id', $systemID)
                                        ->execute()
										->as_array();

        return $dscans;
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
		//are we running with a chain map?
		if( $this->chainmap == NULL )
		{
			return;
		}

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

		$connection = DB::query(Database::SELECT, "SELECT `hash` FROM wormholes WHERE hash=:hash AND group_id=:group AND chainmap_id=:chainmap")
						->param(':hash', $whHash)
						->param(':group', Auth::$session->groupID)
						->param(':chainmap', Auth::$session->accessData['active_chain_map'])
						->execute()
						->current();

		if( !isset($connection['hash'] ) )
		{
			$notifierSystems = array();
			if( !$this->chainmap->system_is_mapped($origin) )
			{
				$notifierSystems[] = $origin;
			}

			if( !$this->chainmap->system_is_mapped($dest) )
			{
				$notifierSystems[] = $origin;
			}

			//new wh
			$this->chainmap->add_system_to_map($origin, $dest);

			$this->doSystemMappedNotifications($notifierSystems);


			miscUtils::increment_stat('wormholes', Auth::$session->accessData);
		}
		else
		{
			//existing wh
			DB::update('wormholes')
				->set( array('last_jump' => time()) )
				->where('hash', '=', $whHash)
				->where('group_id', '=', Auth::$session->groupID)
				->where('chainmap_id', '=', Auth::$session->accessData['active_chain_map'])
				->execute();
		}

        if( Auth::$session->accessData['jumpLogEnabled']  && !empty( $_SERVER['HTTP_EVE_SHIPTYPEID'] ) )
        {
            $charID = ( Auth::$session->accessData['jumpLogRecordNames'] ? $_SERVER['HTTP_EVE_CHARID'] : 0 );
            $charName = ( Auth::$session->accessData['jumpLogRecordNames'] ? $_SERVER['HTTP_EVE_CHARNAME'] : '' );
            $jumpTime = ( Auth::$session->accessData['jumpLogRecordTime'] ? time() : 0 );

            DB::query(Database::INSERT, 'INSERT INTO wormholetracker (`wormhole_hash`, `origin`, `destination`, `group_id`, `chainmap_id`, `time`, `shipTypeID`,`charID`, `charName`)
															   VALUES(:hash, :origin, :dest, :groupID, :chainmap, :time,:shipTypeID,:charID,:charName)')
						->param(':hash', $whHash )
						->param(':dest', $dest)
						->param(':origin', $origin )
						->param(':groupID', Auth::$session->groupID )
						->param(':chainmap', Auth::$session->accessData['active_chain_map'] )
						->param(':time', $jumpTime )
						->param(':shipTypeID',  $_SERVER['HTTP_EVE_SHIPTYPEID'] )
						->param(':charID', $charID)
						->param(':charName', $charName)
						->execute();
        }
	}

	private function doSystemMappedNotifications($systems)
	{
		foreach( Auth::$session->accessData['notifiers'] as $notifier )
		{
			if( $notifier['type'] == NotificationTypes::SystemMappedByName )
			{
				$this->wormholeMappedNotificationHandler($notifier, $systems);
			}
			else if( $notifier['type'] == NotificationTypes::SystemMapppedWithResident )
			{
				$this->systemMappedResidentHandler($notifier, $systems);
			}
		}
	}

	private function systemMappedResidentHandler($notifier, $systems)
	{
		$data = json_decode($notifier['data']);
		foreach($systems as $k => $system)
		{
			$posOnlineSQL = '';
			if( !$data->include_offline )
			{
				$posOnlineSQL = ' AND pos.pos_online=1';
			}

			$pos = DB::query(Database::SELECT, "SELECT pos.pos_id,
														pos.pos_system_id as system_id,
														ss.name as system_name
											FROM pos_tracker pos
											INNER JOIN solarsystems ss ON ss.id = pos.pos_system_id
											WHERE pos.group_id=:group_id
											AND pos.pos_system_id=:system_id
											AND pos.pos_owner LIKE :resident" . $posOnlineSQL)
									->param(':group_id', Auth::$session->groupID)
									->param(':system_id', $system)
									->param(':resident', $data->resident_name)
									->execute()
									->current();

			if( isset($pos['pos_id']) )
			{
				$this->createSystemResidentNotification(
														$notifier,
														$pos['system_id'],
														$pos['system_name'],
														$data->resident_name,
														Auth::$session->charName,
														Auth::$session->charID,
														0);
			}
		}
	}

	private function wormholeMappedNotificationHandler($notifier, $systems)
	{
		$pather = new Pathfinder();
		$data = json_decode($notifier['data']);
		if( in_array($data->system_id, $systems) )
		{
			$this->createSystemMappedNotification(
													$notifier,
													$data->system_id,
													$data->system_name,
													Auth::$session->charName,
													Auth::$session->charID,
													0
												);
		}
		else if (isset($data->num_jumps) &&
				(int)$data->num_jumps > 0)
		{
			/* incase its set for a wormhole jsig just return */
			if( miscUtils::isWspaceID($data->system_id) )
			{
				return;
			}
			
			foreach($systems as $k => $system)
			{
				if( miscUtils::isWspaceID($system) )
				{
					continue;
				}
				
				$path = $pather->shortest($data->system_id, $system);

				if( $path['distance'] <= $data->num_jumps )
				{
					$this->createSystemMappedNotification(
															$notifier,
															$data->system_id,
															$data->system_name,
															Auth::$session->charName,
															Auth::$session->charID,
															$path['distance'],
															$system,
															miscUtils::systemNameByID($system)
														);
				}
			}
		}
	}

	public function createSystemMappedNotification($notifier,
													$systemID,
													$systemName,
													 $characterName,
													$characterID,
													$numJumps,
													$nearbySystemID = 0,
													$nearbySystemName = '')
	{
		$eventData = array(
							'system_id' => $systemID,
							'system_name' => $systemName,
							'character_name' => $characterName,
							'character_id' => $characterID,
							'number_jumps' => $numJumps,
							'nearby_system_id' => $nearbySystemID,
							'nearby_system_name' => $nearbySystemName
							);

		$charID = 0;
		if( $notifier['scope'] == 'personal' )
		{
			$charID = $characterID;
		}

		Notification::create(Auth::$session->groupID, $charID, $notifier['type'], $eventData);
	}

	public function createSystemResidentNotification($notifier,
														$systemID,
														$systemName,
														$resident,
													 	$characterName,
														$characterID)
		{
			$eventData = array(
								'system_id' => $systemID,
								'system_name' => $systemName,
								'resident_name' => $resident,
								'discoverer_name' => $characterName,
								'discoverer_id' => $characterID
								);

			$charID = 0;
			if( $notifier['scope'] == 'personal' )
			{
				$charID = $characterID;
			}

			Notification::create(Auth::$session->groupID, $charID, $notifier['type'], $eventData);
		}


	public function action_siggy()
	{
        $this->profiler = NULL;
        $this->auto_render = FALSE;
        header('content-type: application/json');
        header("Cache-Control: no-cache, must-revalidate");

		if( Kohana::$environment == Kohana::PRODUCTION )
		{
			ob_start( 'ob_gzhandler' );
		}

        if(	!$this->siggyAccessGranted() )
        {
            echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
            exit();
        }

        $update = array(
						'systemUpdate' => 0,
						'sigUpdate' => 0,
						'globalNotesUpdate' => 0,
						'mapUpdate' => 0
						);

        if( isset( $_POST['lastUpdate'] ) && isset( $_POST['systemID'] ) && $_POST['systemID'] != 0 )
        {
            $selectedSystemID = intval($_POST['systemID']);
            $forceUpdate = $_POST['forceUpdate'] == 'true' ? 1 : 0;
            $_POST['lastUpdate'] = intval($_POST['lastUpdate']);

            $newSystemData = array();

            if( $forceUpdate )
            {
				$update['systemData'] = $this->getSystemData( $_POST['systemID'] );
				if( count( $update['systemData'] ) > 0 )
				{
					$update['systemUpdate'] = (int) 1;
				}
            }

			$this->_update_process_map($update);

            $activeSystemQuery = DB::query(Database::SELECT, 'SELECT lastUpdate FROM activesystems WHERE systemID=:id AND groupID=:group AND chainmap_id=:chainmap')
												->param(':id', $selectedSystemID)
												->param(':group',Auth::$session->groupID)
												->param(':chainmap', Auth::$session->accessData['active_chain_map'])
												->execute();

            $activeSystem = $activeSystemQuery->current();
            $recordedLastUpdate = ($activeSystem['lastUpdate'] > 0) ? $activeSystem['lastUpdate']: time();

            if( ($_POST['lastUpdate'] < $recordedLastUpdate) || ( $_POST['lastUpdate'] == 0 ) || $forceUpdate || $update['systemUpdate'] )
            {
                $additional = '';
                if( Auth::$session->accessData['showSigSizeCol'] )
                {
					$additional .= ',sigSize';
                }

                $update['sigData'] = DB::query(Database::SELECT, "SELECT sigID,sig, type, siteID, description, created, creator,updated,lastUpdater".$additional." FROM systemsigs
																	WHERE systemID=:id AND groupID=:group")
                                 ->param(':id', $selectedSystemID)
								 ->param(':group', Auth::$session->groupID)
								 ->execute()
								 ->as_array('sigID');

                $update['sigUpdate'] = (int) 1;
            }

			$update['chainmap_id'] = Auth::$session->accessData['active_chain_map'];

            $update['lastUpdate'] = (int)$recordedLastUpdate;
        }
        else
        {
            $update['error'] = 'You suck';
        }

        echo json_encode( $update );

        exit();
	}

	public function action_update()
	{
        $this->profiler = NULL;
        $this->auto_render = FALSE;
        header('content-type: application/json');
        header("Cache-Control: no-cache, must-revalidate");

		if( Kohana::$environment == Kohana::PRODUCTION )
		{
			ob_start( 'ob_gzhandler' );
		}

        if(	!$this->siggyAccessGranted() )
        {
            echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
            exit();
        }

        $update = array( 'location' => array( 'id' => 0,
											'name' => '' )
						);

		if( $this->igb )
        {
			$currentSystemID = (int)$_SERVER['HTTP_EVE_SOLARSYSTEMID'];
            $lastCurrentSystemID = isset($_POST['last_location_id']) ? (int)$_POST['last_location_id'] : 0;

			if( $lastCurrentSystemID != $currentSystemID  )
			{
				if( $lastCurrentSystemID > 0 && $currentSystemID > 0  )
				{
					if( Auth::$session->accessData['recordJumps'] )
					{
						$hourStamp = miscUtils::getHourStamp();


						DB::query(Database::INSERT, 'INSERT INTO jumpstracker (`systemID`, `groupID`, `hourStamp`, `jumps`)
														VALUES(:systemID, :groupID, :hourStamp, 1)
														ON DUPLICATE KEY UPDATE jumps=jumps+1')
											->param(':hourStamp', $hourStamp )
											->param(':systemID', $currentSystemID )
											->param(':groupID', Auth::$session->groupID )
											->execute();

						DB::query(Database::INSERT, 'INSERT INTO jumpstracker (`systemID`, `groupID`, `hourStamp`, `jumps`)
														VALUES(:systemID, :groupID, :hourStamp, 1)
														ON DUPLICATE KEY UPDATE jumps=jumps+1')
											->param(':hourStamp', $hourStamp )
											->param(':systemID', $lastCurrentSystemID )
											->param(':groupID', Auth::$session->groupID )
											->execute();
					}

					$this->__wormholeJump($lastCurrentSystemID, $currentSystemID);
				}

				$update['location']['id'] = $currentSystemID;
				$update['location']['name'] = $_SERVER['HTTP_EVE_SOLARSYSTEMNAME'];
			}

			/* Location tracking */
			if( isset($_SERVER['HTTP_EVE_CHARID']) && isset($_SERVER['HTTP_EVE_CHARNAME']) && $currentSystemID != 0 )
			{
				if( !Auth::$session->accessData['alwaysBroadcast'] )
				{
					$broadcast = isset($_COOKIE['broadcast']) ? intval($_COOKIE['broadcast']) : 1;
				}
				else
				{
					$broadcast = 1;
				}

				DB::query(Database::INSERT, 'INSERT INTO chartracker (`charID`, `charName`, `currentSystemID`,`groupID`,`chainmap_id`,`lastBeep`, `broadcast`,`shipType`, `shipName`)
											VALUES(:charID, :charName, :systemID, :groupID, :chainmap, :lastBeep, :broadcast, :shipType, :shipName)
							ON DUPLICATE KEY UPDATE lastBeep = :lastBeep,
													currentSystemID = :systemID,
													broadcast = :broadcast,
													shipType = :shipType,
													shipName = :shipName')
						->param(':charID', $_SERVER['HTTP_EVE_CHARID'] )
						->param(':charName', $_SERVER['HTTP_EVE_CHARNAME'] )
						->param(':broadcast', $broadcast )
						->param(':systemID', $currentSystemID )
						->param(':groupID', Auth::$session->groupID )
						->param(':shipType', isset($_SERVER['HTTP_EVE_SHIPTYPEID']) ? (int)$_SERVER['HTTP_EVE_SHIPTYPEID'] : 0 )
						->param(':shipName', isset($_SERVER['HTTP_EVE_SHIPNAME']) ? htmlentities($_SERVER['HTTP_EVE_SHIPNAME']) : '' )
						->param(':chainmap', Auth::$session->accessData['active_chain_map'] )
						->param(':lastBeep', time() )
						->execute();
			}
        }

        $group_last_cache_time = isset($_POST['group_cache_time']) ? intval($_POST['group_cache_time']) : 0;
		if( $group_last_cache_time < Auth::$session->accessData['cache_time'] )
		{
			$update['chainmaps_update'] = 1;

			$chainmaps = array();
			foreach( Auth::$session->accessData['accessible_chainmaps'] as $c )
			{
				$chainmaps[ $c['chainmap_id'] ] = array('id' => (int)$c['chainmap_id'],
														'name' => $c['chainmap_name']);
			}

			$update['chainmaps'] = $chainmaps;

			$update['global_notes_update'] = (int) 1;
			$update['globalNotes'] = Auth::$session->accessData['groupNotes'];
		}

		$update['group_cache_time'] = (int) Auth::$session->accessData['cache_time'];


		$latestDisplayed = isset($_POST['newest_notification']) ? (int) $_POST['newest_notification']  : 0;
		$returnLastRead = Notification::lastReadTimestamp( Auth::$session->groupID, Auth::$session->charID );

		$notifications = Notification::latest($latestDisplayed, Auth::$session->groupID, Auth::$session->charID);
		$update['notifications'] = array('last_read' => $returnLastRead, 'items' => $notifications);


        echo json_encode( $update );

        exit();
	}

	private function _update_process_map(&$update)
	{
        $chainMapOpen = ( isset($_POST['mapOpen']) ? filter_var($_POST['mapOpen'], FILTER_VALIDATE_BOOLEAN) : false );

		if( $this->chainmap != null )
		{
			$this->mapData = $this->chainmap->get_map_cache();
			if( $chainMapOpen == true )
			{
				$update['chainMap']['actives'] = array();
				$update['chainMap']['systems'] = array();
				$update['chainMap']['wormholes'] = array();
				$update['chainMap']['stargates'] = array();
				$update['chainMap']['jumpbridges'] = array();
				$update['chainMap']['cynos'] = array();
				if( is_array($this->mapData['systemIDs']) && count($this->mapData['systemIDs'])	 > 0 )
				{
					$activesData = array();
					$activesData = DB::query(Database::SELECT, "SELECT ct.charName, ct.currentSystemID, s.shipName FROM chartracker ct
																LEFT JOIN ships s ON (ct.shipType=s.shipID)
																WHERE ct.groupID = :groupID AND ct.chainmap_id = :chainmap AND ct.broadcast=1 AND
																	ct.currentSystemID IN(".implode(',',$this->mapData['systemIDs']).") AND ct.lastBeep >= :lastBeep")
										->param(':lastBeep', time()-60)
										->param(':groupID', Auth::$session->groupID)
										->param(':chainmap', Auth::$session->accessData['active_chain_map'])
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
					$update['chainMap']['stargates'] = $this->mapData['stargates'];
					$update['chainMap']['jumpbridges'] = $this->mapData['jumpbridges'];
					$update['chainMap']['cynos'] = $this->mapData['cynos'];
					$update['mapUpdate'] = (int) 1;
				}
				$update['chainMap']['lastUpdate'] = $this->mapData['updateTime'];
			}
		}
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

		groupUtils::update_group(Auth::$session->groupID,$update);
		groupUtils::recacheGroup(Auth::$session->groupID);

		echo json_encode(time());
		exit();
	}

	public function action_save_system()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');

		if( isset($_POST['systemID']) )
		{
			$id = intval($_POST['systemID']);
			if( !$id )
			{
				exit();
			}

			$update = array();

			$system_data = $this->getSystemData($id);
			$log_message = sprintf('%s edited system %s; ', Auth::$session->charName, $system_data['name'] );

			if( isset($_POST['label']) )
			{
				$update['displayName'] = trim(strip_tags($_POST['label']));
				$log_message .= " Display Name:" . $update['displayName'] . ";";
			}

			if( isset($_POST['activity']) )
			{
				$update['activity'] = intval($_POST['activity']);
				$log_message .= " Activity Level:" . $update['activity'] . ";";
			}

			if( isset($_POST['rally']) )
			{
				$update['rally'] = intval($_POST['rally']);
				$log_message .= " Rally:" . $update['rally'] . ";";
			}

			if( empty($update) )
			{
				exit();
			}

			$this->chainmap->update_system($_POST['systemID'], $update);
			echo json_encode('1');


			groupUtils::log_action(Auth::$session->groupID,'editsystem', $log_message );

			$this->chainmap->rebuild_map_data_cache();
		}
		exit();
	}
}
