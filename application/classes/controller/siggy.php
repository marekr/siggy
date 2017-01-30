<?php defined('SYSPATH') or die('No direct script access.');

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;

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
		$sysData = new stdClass();
		$sysData->id = 30000142;
		$sysData->name = 'Jita';
		$view->systemData = $sysData;

		ScribeCommandBus::UnfreezeCharacter(Auth::$session->character_id);

		// did we have an url requested system?
		$requested = false;
		if( !empty($ssname) )
		{
			$sysData = array();

			$ssname = preg_replace("/[^a-zA-Z0-9]/", "", $ssname);

			$system = System::findByName($ssname);
			if( $system != null )
			{
				$sysData = $this->getSystemData($system->id);
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
					$sysData->id = $homeSystems[0];
					$sysData->name = '';
					$view->systemData = $sysData;
				}
			}
			$view->initialSystem = true;
		}

		$view->initialSystem = true;
		$view->group = Auth::$session->group;
		$view->accessData = Auth::$session->accessData;
		$view->requested = $requested;
        $view->settings = $this->template->settings;

		$view->sessionID = '';

		$this->template->content = $view;

		//load chain map html
		$chainMapHTML = View::factory('siggy/chainmap');
		$chainMapHTML->group = Auth::$session->group;
		$chainMapHTML->accessData = Auth::$session->accessData;
		$view->chainMap = $chainMapHTML;

		//load header tools
		$themes = Theme::allByGroup(Auth::$session->group->id);
		
		$view->themes = $themes;
		$view->settings = $this->template->settings;
	}

    public function action_save_character_settings()
    {
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		if(	!$this->siggyAccessGranted() )
		{
			$this->response->body(json_encode(['error' => 1, 'errorMsg' => 'Invalid auth']));
			return;
		}

		$settingsData = json_decode($this->request->body(), true);

		$themeID = intval($settingsData['theme_id']);
		$combineScanIntel = intval($settingsData['combine_scan_intel']);
		$language = $settingsData['language'];
		$activity = !empty($settingsData['default_activity']) ? $settingsData['default_activity'] : null;

		$theme = Theme::findByGroup(Auth::$session->group->id, $themeID);

		if( $theme != null )
		{
			Auth::$user->theme_id = $themeID;
			Auth::$user->language = $language;
			Auth::$user->combine_scan_intel = $combineScanIntel;
			Auth::$user->default_activity = $activity;
			
			Auth::$user->save();
		}

		$this->response->body(json_encode(''));
    }

	public function before()
	{
		parent::before();

		if( Auth::$session->accessData['active_chain_map'] )
		{
			$this->chainmap = Chainmap::find(Auth::$session->accessData['active_chain_map'],Auth::$session->group->id);
		}
	}

	public function after()
	{
		parent::after();
	}

	private function findSystemIDByName( $id )
	{
		$systemData = DB::select("SELECT ss.id,ss.name
									FROM solarsystems ss
									WHERE ss.name=:name",
									[
										'name' => $id
									]);

		if( $systemData == null )
		{
			return 0;
		}
		else
		{
			return $systemData->id;
		}
	}

	private function getSystemData( $id )
	{
		$systemData = DB::selectOne("SELECT ss.*,se.effectTitle, r.regionName, c.constellationName,
													COALESCE(sa.displayName,'') as displayName,
													COALESCE(sa.inUse,0) as inUse,
													COALESCE(sa.activity,0) as activity
													FROM solarsystems ss
													INNER JOIN systemeffects se ON ss.effect = se.id
													INNER JOIN regions r ON ss.region = r.regionID
													INNER JOIN constellations c ON ss.constellation = c.constellationID
													LEFT OUTER JOIN activesystems sa ON (ss.id = sa.systemID  AND sa.groupID = :group AND sa.chainmap_id=:chainmap)
													WHERE ss.id=:id",
													[
														'id' => $id,
														'group' => Auth::$session->group->id,
														'chainmap' => Auth::$session->accessData['active_chain_map'] 
													]);

		if( $systemData == null )
		{
			return FALSE;
		}

		$systemData->staticData = array();

		$staticData = DB::select("SELECT sm.static_id as id FROM staticmap sm
								 WHERE sm.system_id=?",[$systemData->id]);

		if( count( $staticData ) > 0 )
		{
			$systemData->staticData = $staticData;
		}

		$end = miscUtils::getHourStamp();
		$start = miscUtils::getHourStamp(-24);
		$apiData = DB::select("SELECT hourStamp, jumps, kills, npcKills 
												FROM apihourlymapdata 
												WHERE systemID=:system AND hourStamp >= :start AND hourStamp <= :end 
												ORDER BY hourStamp asc LIMIT 0,24",[
													'system' => $systemData->id,
													'start' => $start,
													'end' => $end
												]);

		$trackedJumps = DB::select("SELECT hourStamp, jumps 
													FROM jumpstracker 
													WHERE systemID=:system AND groupID=:group AND hourStamp >= :start 
													AND hourStamp <= :end 
													ORDER BY hourStamp asc LIMIT 0,24",
													[
														'system' => $systemData->id,
														'group' => Auth::$session->group->id,
														'start' => $start,
														'end' => $end
													]);
								//	->execute()->as_array('hourStamp');

		$systemData->stats = [];
		for($i = 23; $i >= 0; $i--)
		{
			$hourStamp = miscUtils::getHourStamp($i*-1);
			$apiJumps = ( isset($apiData[ $hourStamp ]) ? $apiData[ $hourStamp ]['jumps'] : 0);
			$apiKills = ( isset($apiData[ $hourStamp ]) ? $apiData[ $hourStamp ]['kills'] : 0);
			$apiNPC = ( isset($apiData[ $hourStamp ]) ? $apiData[ $hourStamp ]['npcKills'] : 0);
			$siggyJumps = ( isset($trackedJumps[ $hourStamp ]) ? $trackedJumps[ $hourStamp ]['jumps'] : 0);
			$systemData->stats[] = array( $hourStamp*1000, $apiJumps, $apiKills, $apiNPC, $siggyJumps);
		}

		$hubJumps = DB::select("SELECT ss.id as system_id, pr.num_jumps,ss.name as destination_name 
												FROM precomputedroutes pr
												 INNER JOIN solarsystems ss ON ss.id = pr.destination_system
												 WHERE pr.origin_system=? AND pr.destination_system != ?
												 ORDER BY pr.num_jumps ASC", [$systemData->id,$systemData->id]);

		$systemData->hubJumps = $hubJumps;

		$systemData->poses = $this->getPOSes( $systemData->id );

		$systemData->dscans = $this->getDScans( $systemData->id );

		return $systemData;
	}

    private function getPOSes( $systemID )
    {
		$poses = DB::select("SELECT p.id, p.location_planet, p.location_moon, p.online, p.pos_type_id, p.size,
												p.added_date, p.owner, pt.pos_type_name, p.notes
												FROM poses p
												INNER JOIN pos_types pt ON(pt.pos_type_id = p.pos_type_id)
												WHERE p.group_id=:group_id AND p.system_id=:system_id
												ORDER BY p.location_planet ASC, p.location_moon ASC",
												[
													'group_id' => Auth::$session->group->id,
													'system_id' => $systemID
												]);

		return $poses;
	}

	private function getDScans( $systemID )
	{
		$dscans = DB::select("SELECT dscan_id, dscan_title, dscan_date
												FROM dscan
												WHERE group_id=:group_id AND system_id=:system_id",
												[
													'group_id' => Auth::$session->group->id,
													'system_id' => $systemID
												]);

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

	private function __wormholeJump(CharacterLocationHistory $record)
	{
		$origin = $record->current_system_id;
		$dest = $record->previous_system_id;
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

		$kspaceJump = DB::selectOne("SELECT `fromSolarSystemID`, `toSolarSystemID`
													FROM eve_mapsolarsystemjumps
													WHERE (fromSolarSystemID=? AND toSolarSystemID=?) OR
														 (fromSolarSystemID=? AND toSolarSystemID=?)",[$origin, $dest, $dest, $origin]);
		if( $kspaceJump != null )
		{
			return;
		}

		$whHash = Chainmap::whHashByID($origin, $dest);

		$connection = DB::selectOne("SELECT `hash` 
										FROM wormholes 
										WHERE hash=:hash AND group_id=:group AND chainmap_id=:chainmap",
										[
											'hash' => $whHash,
											'group' => Auth::$session->group->id,
											'chainmap' => Auth::$session->accessData['active_chain_map']
										]);

		if( $connection == null )
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


			Auth::$session->group->incrementStat('wormholes', Auth::$session->accessData);
		}
		else
		{
			//existing wh
			DB::table('wormholes')
				->where('hash', '=', $whHash)
				->where('group_id', '=', Auth::$session->group->id)
				->where('chainmap_id', '=', Auth::$session->accessData['active_chain_map'])
				->update(['last_jump' => time()]);
		}

		//TODO fix me......this is a more involved one
		
		if( Auth::$session->group->jump_log_enabled )
		{
			$charID = ( Auth::$session->group->jump_log_record_names ? Auth::$session->character_id : 0 );

			$data = [
				'wormhole_hash' => $whHash,
				'destination_id' => $dest,
				'origin_id' => $origin,
				'group_id' => Auth::$session->group->id,
				'ship_id' => $record->ship_id,
				'character_id' => $charID
			];
			
			$jumpEntry = WormholeJump::create($data);
		}
	}

	private function doSystemMappedNotifications($systems)
	{
		foreach( Notifier::allByGroupCharacter(Auth::$session->group->id, Auth::$session->character_id) as $notifier )
		{
			if( $notifier->type == NotificationTypes::SystemMappedByName )
			{
				$this->wormholeMappedNotificationHandler($notifier, $systems);
			}
			else if( $notifier->type == NotificationTypes::SystemMapppedWithResident )
			{
				$this->systemMappedResidentHandler($notifier, $systems);
			}
		}
	}

	private function systemMappedResidentHandler($notifier, $systems)
	{
		$data = $notifier->data;
		foreach($systems as $k => $system)
		{
			$q = POS::with('system')->where('group_id',Auth::$session->group->id)
				->where('system_id', $system)
				->where('owner','LIKE',$data->resident_name);

			if( !$data->include_offline )
			{
				$q = $q->where('online',1);
			}

			$pos = $q->first();

			if( $pos != null )
			{
				$this->createSystemResidentNotification(
														$notifier,
														$pos->system->id,
														$pos->system->name,
														$data->resident_name,
														Auth::$session->character_name,
														Auth::$session->character_id,
														0);
			}
		}
	}

	private function wormholeMappedNotificationHandler($notifier, $systems)
	{
		$pather = new Pathfinder();
		$data = $notifier->data;
		if( in_array($data->system_id, $systems) )
		{
			$this->createSystemMappedNotification(
													$notifier,
													$data->system_id,
													$data->system_name,
													Auth::$session->character_name,
													Auth::$session->character_id,
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
															Auth::$session->character_name,
															Auth::$session->character_id,
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
		if( $notifier->scope == 'personal' )
		{
			$charID = $characterID;
		}

		Notification::createFancy(Auth::$session->group->id, $charID, $notifier->type, $eventData);
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
			if( $notifier->scope == 'personal' )
			{
				$charID = $characterID;
			}

			Notification::createFancy(Auth::$session->group->id, $charID, $notifier->type, $eventData);
		}


	public function action_siggy()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		if( Kohana::$environment == Kohana::PRODUCTION )
		{
			ob_start( 'ob_gzhandler' );
		}

		if(	!$this->siggyAccessGranted() )
		{
			$this->response->body(json_encode(['error' => 1, 'errorMsg' => 'Invalid auth']));
			return;
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

            $activeSystem = DB::selectOne('SELECT lastUpdate FROM activesystems WHERE systemID=:id AND groupID=:group AND chainmap_id=:chainmap',
												[
													'id' => $selectedSystemID,
													'group' => Auth::$session->group->id,
													'chainmap' => Auth::$session->accessData['active_chain_map']
												]);
								
			$recordedLastUpdate = ($activeSystem != null) ? $activeSystem->lastUpdate: time();

			if( ($_POST['lastUpdate'] < $recordedLastUpdate) || ( $_POST['lastUpdate'] == 0 ) || $forceUpdate || $update['systemUpdate'] )
			{
				$additional = '';

				$update['sigData'] = Signature::findByGroupSystem(Auth::$session->group->id,$selectedSystemID);

				 foreach($update['sigData'] as &$sig)
				 {
					$sig->id = (int)$sig->id;
					$sig->siteID = (int)$sig->siteID;

					 if($sig->type != 'wh')
					 	continue;

					$whSigData = WormholeSignature::findAllBySig($sig->id);
					foreach($whSigData as $wh)
					{
						$sig->chainmap_wormholes[ $wh->chainmap_id ] = $wh->wormhole_hash;
					}
				 }
				$update['sigUpdate'] = (int) 1;
			}

			$update['chainmap_id'] = Auth::$session->accessData['active_chain_map'];

			$update['lastUpdate'] = (int)$recordedLastUpdate;
		}
		else
		{
			$update['error'] = 'You suck';
		}

		$this->response->body(json_encode($update));
	}

	public function action_update()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		if( Kohana::$environment == Kohana::PRODUCTION )
		{
			ob_start( 'ob_gzhandler' );
		}

		if(	!$this->siggyAccessGranted() )
		{
			$this->response->body(json_encode(['error' => 1, 'errorMsg' => 'Invalid auth']));
			return;
		}

		$update = ['location' => [ 'id' => 0] ];

		$ssoCharacters = Auth::$user->ssoCharacters;
		foreach($ssoCharacters as $character)
		{
			if( $character->character_id != Auth::$session->character_id 
				&& !$character->always_track_location )
			{
				continue;
			}

			$charData = $character->character;

			if($charData == null)
			{
				//TODO FIXME/HANDLE ME BETTER
				continue;
			}

			$currentLocation = CharacterLocation::findWithinCutoff($character->character_id);


			if($charData->canAccessMap(Auth::$session->group->id,Auth::$session->accessData['active_chain_map']))
			{
				$locationThreshold = $charData->location_processed_at;
				$locationThresholdCutoff = Carbon::now()->subMinutes(1);
				if($locationThreshold == null)
				{
					$locationThreshold = $locationThresholdCutoff;
				}
				else
				{
					$locationThreshold = Carbon::parse($locationThreshold);

					//make sure our "saved" point isnt that far back in time or else we may start mapping things unexpectedly
					if($locationThreshold->lt($locationThresholdCutoff))
					{
						$locationThreshold = $locationThresholdCutoff;
					}
				}

				$history = CharacterLocationHistory::findNewerThan($character->character_id, $locationThreshold);

				$lastHistoryDatetime = null;
				foreach($history as $record)
				{
					if($record->current_system_id != $record->previous_system_id)
					{
						if( Auth::$session->group->record_jumps )
						{
							$hourStamp = miscUtils::getHourStamp();


							DB::insert('INSERT INTO jumpstracker (`systemID`, `groupID`, `hourStamp`, `jumps`)
															VALUES(:systemID, :groupID, :hourStamp, 1)
															ON DUPLICATE KEY UPDATE jumps=jumps+1',
															[
																'hourStamp' => $hourStamp,
																'systemID' => $record->current_system_id,
																'groupID' => Auth::$session->group->id
															]);

							DB::insert('INSERT INTO jumpstracker (`systemID`, `groupID`, `hourStamp`, `jumps`)
															VALUES(:systemID, :groupID, :hourStamp, 1)
															ON DUPLICATE KEY UPDATE jumps=jumps+1',
															[
																'hourStamp' => $hourStamp,
																'systemID' => $record->previous_system_id,
																'groupID' => Auth::$session->group->id
															]);
						}

						try
						{
							$this->__wormholeJump($record);
						}
						catch(Exception $e)
						{
						}
					}

					$lastHistoryDatetime = $record->changed_at;
				}

				if($lastHistoryDatetime != null)
				{
					$charData->location_processed_at = $lastHistoryDatetime;
					$charData->save();
				}
			
				if( $currentLocation != null )
				{
					if( $character->character_id == Auth::$session->character_id )
					{
						$update['location']['id'] = (int)$currentLocation->system_id;
					}

					if( !Auth::$session->group->always_broadcast )
					{
						$broadcast = isset($_COOKIE['broadcast']) ? intval($_COOKIE['broadcast']) : 1;
					}
					else
					{
						$broadcast = 1;
					}

					DB::insert('INSERT INTO chartracker (`charID`, `currentSystemID`,`groupID`,`chainmap_id`,`lastBeep`, `broadcast`,`shipType`, `shipName`)
												VALUES(:charID, :systemID, :groupID, :chainmap, :lastBeep, :broadcast, :shipType, :shipName)
												ON DUPLICATE KEY UPDATE lastBeep = :lastBeep2,
														currentSystemID = :systemID2,
														broadcast = :broadcast2,
														shipType = :shipType2,
														shipName = :shipName2',
										[
											'charID' => $character->character_id,
											'broadcast' => $broadcast,
											'systemID' => (int)$currentLocation->system_id,
											'groupID' => Auth::$session->group->id,
											'shipType' => 0,
											'shipName' => '',
											'chainmap' => Auth::$session->accessData['active_chain_map'],
											'lastBeep' => time(),
											'lastBeep2' => time(),
											'systemID2' => (int)$currentLocation->system_id,
											'broadcast2' => $broadcast,
											'shipType2' => 0,
											'shipName2' => ''
										]);
				}
			}
		}

		$group_last_cache_time = isset($_POST['group_cache_time']) ? intval($_POST['group_cache_time']) : 0;
		if( $group_last_cache_time < Auth::$session->group->cache_time )
		{
			$update['chainmaps_update'] = 1;

			$chainmaps = array();
			foreach( Auth::$session->accessibleChainMaps() as $c )
			{
				$chainmaps[ $c->chainmap_id ] = ['id' => (int)$c->chainmap_id,
														'name' => $c->chainmap_name];
			}

			$update['chainmaps'] = $chainmaps;

			$update['global_notes_update'] = (int) 1;
			$update['globalNotes'] = Auth::$session->group->notes;
		}

		$update['group_cache_time'] = (int) Auth::$session->group->last_update;


		$latestDisplayed = isset($_POST['newest_notification']) ? (int) $_POST['newest_notification']  : 0;
		$returnLastRead = Notification::lastReadTimestamp( Auth::$session->group->id, Auth::$session->character_id );

		$notifications = Notification::latest($latestDisplayed, Auth::$session->group->id, Auth::$session->character_id);
		$update['notifications'] = array('last_read' => $returnLastRead, 'items' => $notifications);


		$this->response->body(json_encode($update));
	}

	private function _update_process_map(&$update)
	{
		$chainMapOpen = ( isset($_POST['mapOpen']) ? filter_var($_POST['mapOpen'], FILTER_VALIDATE_BOOLEAN) : false );

		if( $this->chainmap != null )
		{
			$this->mapData = $this->chainmap->get_map_cache();
			if( $chainMapOpen == true )
			{
				$update['chainMap']['actives'] = [];
				$update['chainMap']['systems'] = [];
				$update['chainMap']['wormholes'] = [];
				$update['chainMap']['stargates'] = [];
				$update['chainMap']['jumpbridges'] = [];
				$update['chainMap']['cynos'] = [];
				if( is_array($this->mapData['systemIDs']) && count($this->mapData['systemIDs'])	 > 0 )
				{
					$activesData = array();
					$activesData = DB::select("SELECT c.name as charName, ct.currentSystemID, s.shipName FROM chartracker ct
																LEFT JOIN ships s ON (ct.shipType=s.shipID)
																LEFT JOIN characters c ON(c.id=ct.charID)
																WHERE ct.groupID = :groupID AND ct.chainmap_id = :chainmap AND ct.broadcast=1 AND
																	ct.currentSystemID IN(".implode(',',$this->mapData['systemIDs']).") AND ct.lastBeep >= :lastBeep",
																[
																	'lastBeep' => time()-60,
																	'groupID' => Auth::$session->group->id,
																	'chainmap' => Auth::$session->accessData['active_chain_map']
																]);

					if( is_array($activesData) && count($activesData) > 0 )
					{
						$actives = [];
						foreach( $activesData as $act )
						{
							if( strlen( $act->charName) > 15 )
							{
								$act->charName = substr($act->charName, 0,12).'...';
							}

							if( $act->shipName == NULL )
							{
								$act->shipName = "";
							}
							$actives[ $act->currentSystemID ][] = array('name' => $act->charName, 'ship' => $act->shipName);
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
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		if(	 !$this->siggyAccessGranted() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}

		$notes = htmlspecialchars($_POST['notes']);

		Auth::$session->group->notes = $notes;
		Auth::$session->group->save();

		$this->response->body(json_encode(time()));
	}

	public function action_save_system()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		if( isset($_POST['systemID']) )
		{
			$id = intval($_POST['systemID']);
			if( !$id )
			{
				exit();
			}

			$update = array();

			$system_data = $this->getSystemData($id);
			$log_message = sprintf('%s edited system %s; ', Auth::$session->character_name, $system_data->name );

			if( isset($_POST['label']) )
			{
				$update['displayName'] = trim(htmlspecialchars($_POST['label']));
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

			if( isset($_POST['hazard']) )
			{
				$update['hazard'] = intval($_POST['hazard']);
				$log_message .= " Hazard:" . $update['hazard'] . ";";
			}

			if( empty($update) )
			{
				exit();
			}

			$this->chainmap->update_system($_POST['systemID'], $update);
			echo json_encode('1');


			Auth::$session->group->logAction('editsystem', $log_message );

			$this->chainmap->rebuild_map_data_cache();
		}

		$this->response->body(json_encode([]));
	}
}
