<?php 

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cookie;

use Carbon\Carbon;
use Siggy\Structure;
use Siggy\POS;
use Siggy\Notification;
use Siggy\ScribeCommandBus;
use Siggy\Theme;
use \stdClass;
use App\Facades\Auth;
use Siggy\CharacterLocation;
use \CharacterLocationHistory;
use \miscUtils;
use \Signature;
use \Chainmap;
use \WormholeSignature;
use \System;
use \WormholeJump;
use \Notifier;
use \NotificationTypes;
use \Pathfinder;
use App\Facades\SiggySession;

class SiggyController extends BaseController {

	public $chainmap = null;
	
	public function index(Request $request)
	{
		// regenerate the session so we have a csrf token that wont expire immediately
		$request->session()->regenerate();

		// set default
		$sysData = new stdClass();
		$sysData->id = 30000142;
		$sysData->name = 'Jita';

		$activeChar = Auth::user()->getActiveSSOCharacter();

		Redis::pipeline(function($pipe) {
			$pipe->sadd('siggy:actives:user#'.Auth::user()->id, "active", SiggySession::getCharacterId());
			$pipe->expire('siggy:actives:user#'.Auth::user()->id, 60);
		});

		ScribeCommandBus::UnfreezeCharacter($activeChar->character_owner_hash);


		// did we have an url requested system?
		$requested = false;

		if( $this->getChainmap() != null )
		{
			$homeSystems = $this->getChainmap()->get_home_systems();

			if( count($homeSystems) > 0 )
			{
				$sysData->id = $homeSystems[0];
				$sysData->name = '';
			}
		}

		$reconnectRequired = [];
		$ssoCharacters = Auth::user()->ssoCharacters;
		foreach($ssoCharacters as $character)
		{
			if(!$character->scope_esi_location_read_online ||
				!$character->scope_esi_location_read_location)
			{
				$reconnectRequired[] = $character;
			}
		}

		//load header tools
		$themes = Theme::allByGroup(SiggySession::getGroup()->id);

		//old cookie cleanup
		if( Cookie::get('sessionID') !== null )
		{
			Cookie::queue(Cookie::forget('sessionID'));
		}

		if( Cookie::get('userID') !== null )
		{
			Cookie::queue(Cookie::forget('userID'));
		}
		
		if( Cookie::get('passHash') !== null )
		{
			Cookie::queue(Cookie::forget('passHash'));
		}

		return view('siggy.siggy_main', [
												'group' => SiggySession::getGroup(),
												'themes' => Theme::allByGroup(SiggySession::getGroup()->id),
												'settings' => $this->loadSettings(),
												'systemData' => $sysData,
												'requested' => $requested,
												'reconnectRequired' => $reconnectRequired
											]);
	}
	
	public function update()
	{
		$update = ['location' => [ 'id' => 0] ];

		Redis::pipeline(function($pipe) {
			$pipe->sadd('siggy:actives:user#'.Auth::user()->id, "active", SiggySession::getCharacterId());
			$pipe->expire('siggy:actives:user#'.Auth::user()->id, 60);
		});

		$ssoCharacters = Auth::user()->ssoCharacters;
		foreach($ssoCharacters as $character)
		{
			if( $character->character_id != SiggySession::getCharacterId() 
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


			if($charData->canAccessMap(SiggySession::getGroup()->id,SiggySession::getAccessData()['active_chain_map']))
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
					if( $character->character_id == SiggySession::getCharacterId() )
					{
						$update['location']['id'] = (int)$currentLocation->system_id;
					}

					if( !SiggySession::getGroup()->always_broadcast )
					{
						$broadcast = isset($_COOKIE['broadcast']) ? intval($_COOKIE['broadcast']) : true;
					}
					else
					{
						$broadcast = true;
					}
					
					if($broadcast)
					{
						Redis::pipeline(function($pipe) use($character) {
							$value = $character->character_id . ":" .$character->character->name;
							$pipe->zadd('siggy:actives:chainmap#'.SiggySession::getAccessData()['active_chain_map'], [$value => time()]);
							$pipe->expire('siggy:actives:chainmap#'.SiggySession::getAccessData()['active_chain_map'], 60);
						});
					}
				}
			}
		}

		$group_last_cache_time = isset($_POST['group_cache_time']) ? intval($_POST['group_cache_time']) : 0;
		if( $group_last_cache_time < SiggySession::getGroup()->cache_time )
		{
			$update['chainmaps_update'] = 1;

			$chainmaps = array();
			foreach( SiggySession::accessibleChainMaps() as $c )
			{
				$chainmaps[ $c->chainmap_id ] = ['id' => (int)$c->chainmap_id,
														'name' => $c->chainmap_name];
			}

			$update['chainmaps'] = $chainmaps;

			$update['global_notes_update'] = (int) 1;
			$update['globalNotes'] = SiggySession::getGroup()->notes;
		}

		$update['group_cache_time'] = (int) SiggySession::getGroup()->last_update;


		$latestDisplayed = isset($_POST['newest_notification']) ? (int) $_POST['newest_notification']  : 0;
		$returnLastRead = Notification::lastReadTimestamp( SiggySession::getGroup()->id, SiggySession::getCharacterId() );

		$notifications = Notification::latest($latestDisplayed, SiggySession::getGroup()->id, SiggySession::getCharacterId());
		$update['notifications'] = array('last_read' => $returnLastRead, 'items' => $notifications);


		return response()->json($update);
	}

	
	public function siggy()
	{
		$update = [
						'systemUpdate' => 0,
						'sigUpdate' => 0,
						'globalNotesUpdate' => 0,
						'mapUpdate' => 0
		];

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
													'group' => SiggySession::getGroup()->id,
													'chainmap' => SiggySession::getAccessData()['active_chain_map']
												]);
								
			$recordedLastUpdate = ($activeSystem != null) ? $activeSystem->lastUpdate: time();

			if( ($_POST['lastUpdate'] < $recordedLastUpdate) || ( $_POST['lastUpdate'] == 0 ) || $forceUpdate || $update['systemUpdate'] )
			{
				$additional = '';

				$update['sigData'] = Signature::findByGroupSystem(SiggySession::getGroup()->id,$selectedSystemID);

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

			$update['chainmap_id'] = SiggySession::getAccessData()['active_chain_map'];

			$update['lastUpdate'] = (int)$recordedLastUpdate;
		}
		else
		{
			$update['error'] = 'You suck';
		}

		return response()->json($update);
	}
	
	private function getSystemData( $id )
	{
		$systemData = DB::selectOne("SELECT ss.*,se.effectTitle, r.regionName, c.constellationName,
													COALESCE(sa.displayName,'') as displayName,
													COALESCE(sa.inUse,0) as inUse,
													COALESCE(sa.activity,0) as activity
													FROM solarsystems ss
													LEFT JOIN systemeffects se ON ss.effect = se.id
													INNER JOIN eve_map_regions r ON ss.region = r.regionID
													INNER JOIN eve_map_constellations c ON ss.constellation = c.constellationID
													LEFT OUTER JOIN activesystems sa ON (ss.id = sa.systemID  AND sa.groupID = :group AND sa.chainmap_id=:chainmap)
													WHERE ss.id=:id",
													[
														'id' => $id,
														'group' => SiggySession::getGroup()->id,
														'chainmap' => SiggySession::getAccessData()['active_chain_map'] 
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

		$jumps = \Siggy\SolarSystemJump::where('system_id', '=', $systemData->id)
											->where('date_start', '>=', Carbon::now()->subDay())->get(['date_start', 'ship_jumps']);
		$kills = \Siggy\SolarSystemKill::where('system_id', '=', $systemData->id)
											->where('date_start', '>=', Carbon::now()->subDay())->get(['date_start', 'ship_kills','npc_kills','pod_kills']);

		$systemData->stats = [
				'jumps' => $jumps,
				'kills' => $kills
		];

		$hubJumps = DB::select("SELECT ss.id as system_id, pr.num_jumps,ss.name as destination_name 
												FROM precomputedroutes pr
												 INNER JOIN solarsystems ss ON ss.id = pr.destination_system
												 WHERE pr.origin_system=? AND pr.destination_system != ?
												 ORDER BY pr.num_jumps ASC", [$systemData->id,$systemData->id]);

		$systemData->hubJumps = $hubJumps;

		$systemData->poses = $this->getPOSes( $systemData->id );

		$systemData->dscans = $this->getDScans( $systemData->id );

		$systemData->structures = Structure::findAllByGroupSystem( SiggySession::getGroup()->id, $systemData->id );

		return $systemData;
	}

	private function getPOSes( $systemID )
	{
		$poses = DB::select("SELECT p.id, p.location_planet, p.location_moon, p.online, p.type_id, p.size,
												p.added_date, p.owner, p.notes
												FROM poses p
												WHERE p.group_id=:group_id AND p.system_id=:system_id
												ORDER BY p.location_planet ASC, p.location_moon ASC",
												[
													'group_id' => SiggySession::getGroup()->id,
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
													'group_id' => SiggySession::getGroup()->id,
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
		if( $this->getChainmap() == NULL )
		{
			return;
		}

		if( $origin == $dest )
		{
			//failure condition that happens sometimes, bad for the JS engine
			return;
		}

		$kspaceJump = DB::selectOne("SELECT `fromSolarSystemID`, `toSolarSystemID`
													FROM eve_map_solar_system_jumps
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
											'group' => SiggySession::getGroup()->id,
											'chainmap' => SiggySession::getAccessData()['active_chain_map']
										]);

		if( $connection == null )
		{
			$notifierSystems = array();
			if( !$this->getChainmap()->system_is_mapped($origin) )
			{
				$notifierSystems[] = $origin;
			}

			if( !$this->getChainmap()->system_is_mapped($dest) )
			{
				$notifierSystems[] = $origin;
			}

			//new wh
			$this->getChainmap()->add_system_to_map($origin, $dest);

			$this->doSystemMappedNotifications($notifierSystems);


			SiggySession::getGroup()->incrementStat('wormholes', SiggySession::getAccessData());
		}
		else
		{
			//existing wh
			DB::table('wormholes')
				->where('hash', '=', $whHash)
				->where('group_id', '=', SiggySession::getGroup()->id)
				->where('chainmap_id', '=', SiggySession::getAccessData()['active_chain_map'])
				->update(['last_jump' => time()]);
		}

		//TODO fix me......this is a more involved one
		
		if( SiggySession::getGroup()->jump_log_enabled )
		{
			$charID = ( SiggySession::getGroup()->jump_log_record_names ? SiggySession::getCharacterId() : 0 );

			$data = [
				'wormhole_hash' => $whHash,
				'destination_id' => $dest,
				'origin_id' => $origin,
				'group_id' => SiggySession::getGroup()->id,
				'ship_id' => $record->ship_id,
				'character_id' => $charID
			];
			
			$jumpEntry = WormholeJump::create($data);
		}
	}

	private function doSystemMappedNotifications($systems)
	{
		foreach( Notifier::allByGroupCharacter(SiggySession::getGroup()->id, SiggySession::getCharacterId()) as $notifier )
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
			$q = POS::with('system')->where('group_id',SiggySession::getGroup()->id)
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
														SiggySession::getCharacterName(),
														SiggySession::getCharacterId(),
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
													SiggySession::getCharacterName(),
													SiggySession::getCharacterId(),
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
															SiggySession::getCharacterName(),
															SiggySession::getCharacterId(),
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

		Notification::createFancy(SiggySession::getGroup()->id, $charID, $notifier->type, $eventData);
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

			Notification::createFancy(SiggySession::getGroup()->id, $charID, $notifier->type, $eventData);
		}


	public function getChainmap()
	{
		if($this->chainmap == null)
		{
			if( SiggySession::getAccessData()['active_chain_map'] )
			{
				$this->chainmap = Chainmap::find(SiggySession::getAccessData()['active_chain_map'],SiggySession::getGroup()->id);
			}
		}

		return $this->chainmap;
	}

	private function _update_process_map(&$update)
	{
		$chainMapOpen = ( isset($_POST['mapOpen']) ? filter_var($_POST['mapOpen'], FILTER_VALIDATE_BOOLEAN) : false );

		if( $this->getChainmap() != null )
		{
			$this->mapData = $this->getChainmap()->get_map_cache();
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
					$results = Redis::zrangebyscore('siggy:actives:chainmap#'.SiggySession::getAccessData()['active_chain_map'], time()-60,"+inf");

					if( is_array($results) && count($results) > 0 )
					{
						$entries = [];
						foreach($results as $result)
						{
							$split = explode(":",$result);
							list($id, $name) = $split;	
							$id = (int)$id;
							$location = CharacterLocation::findWithinCutoff($id, 60);
							if( $location != null )
							{
								$entries[$location->system_id][] = [
									'character_id' => $id,
									'character_name' => $name,
									'ship_id' => $location->ship_id
								];
							}
						}

						if(count($entries) > 0)
						{
							array_walk($entries, function(&$value, $key){
								usort($value, function($a, $b) {
									return strcmp($a["character_name"], $b["character_name"])  > 0 ? 1 : -1;
								});
							});

							$update['chainMap']['actives'] = $entries;
						}
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

	
	public function saveSystem()
	{
		$id = intval($_POST['systemID']);
		if( !$id )
		{
			return response()->json(false);
		}

		$update = array();

		$system_data = $this->getSystemData($id);
		$log_message = sprintf('%s edited system %s; ', SiggySession::getCharacterName(), $system_data->name );

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
			return response()->json(false);
		}

		$this->getChainmap()->update_system($_POST['systemID'], $update);

		SiggySession::getGroup()->logAction('editsystem', $log_message );

		$this->getChainmap()->rebuild_map_data_cache();

		return response()->json(true);
	}
	
	public function notesSave()
	{
		$notes = htmlspecialchars($_POST['notes']);

		SiggySession::getGroup()->notes = $notes;
		SiggySession::getGroup()->save();

		return response()->json(time());
	}
	
	public function saveCharacterSettings(Request $request)
	{
		$settingsData = json_decode($request->getContent(), true);

		$themeID = intval($settingsData['theme_id']);
		$combineScanIntel = intval($settingsData['combine_scan_intel']);
		$language = $settingsData['language'];
		$activity = !empty($settingsData['default_activity']) ? $settingsData['default_activity'] : null;

		$theme = Theme::findByGroup(SiggySession::getGroup()->id, $themeID);

		if( $theme != null )
		{
			Auth::user()->theme_id = $themeID;
			Auth::user()->language = $language;
			Auth::user()->combine_scan_intel = $combineScanIntel;
			Auth::user()->default_activity = $activity;
			
			Auth::user()->save();
		}

		return response()->json(true);
	}
}
