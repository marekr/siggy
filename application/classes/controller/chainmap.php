<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;

class Controller_Chainmap extends FrontController {
	/*
		Key value array
	*/
	public $template = 'template/public';

	protected $output_array = array();


	public function before()
	{
		parent::before();

		$chainmapID = (isset($_REQUEST['chainmap']) ? (int)$_REQUEST['chainmap'] : Auth::$session->accessData['active_chain_map'] );
		if( $chainmapID )
		{
			$this->chainmap = Chainmap::find($chainmapID,Auth::$session->group->id);
		}
	}

	public function action_find_nearest_exits()
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


		$target = isset($_REQUEST['target']) ? trim($_REQUEST['target']) : "";
		$targetCurrentSys = isset($_REQUEST['current_system']) ? intval($_REQUEST['current_system']) : 0;

		$targetID = 0;

		$currentLocation = CharacterLocation::findWithinCutoff(Auth::$session->character_id);
		if( $targetCurrentSys && $currentLocation != null )
		{
			$targetID = $currentLocation->system_id;
		}
		else if (!empty($target))
		{
			$targetID = $this->chainmap->find_system_by_name($target);
		}

		if( $targetID == 0 || $targetID >= 31000000 )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid system'));
			exit();
		}

		$systems = DB::select("( SELECT DISTINCT w.to_system_id as sys_id,ss.name
												FROM wormholes w
												LEFT JOIN solarsystems ss ON (ss.id = w.to_system_id)
												WHERE w.to_system_id < 31000000 AND w.group_id=:group1 AND w.chainmap_id=:chainmap1)
												UNION DISTINCT
											( SELECT DISTINCT w.from_system_id as sys_id, ss.name
											FROM wormholes w
											LEFT JOIN solarsystems ss ON (ss.id = w.from_system_id)
											WHERE w.from_system_id < 31000000 AND w.group_id=:group2 AND w.chainmap_id=:chainmap2)",
											[
												'group1' => Auth::$session->group->id,
												'group2' => Auth::$session->group->id,
												'chainmap1' => $this->chainmap->id,
												'chainmap2' => $this->chainmap->id,
											]);

		$pather = new Pathfinder();
		$result = array();
		foreach($systems as $system)
		{
			$path = $pather->shortest($targetID, $system->sys_id);
			$path = $path['distance'];

			$result[] = array('system_id' => $system->sys_id, 'system_name' => $system->name, 'number_jumps' => $path );
		}

		usort($result, array('Controller_Chainmap','sortResults'));

		$this->response->body(json_encode(['result' => $result]));
	}

	private static function sortResults($a, $b)
	{
		if ($a['number_jumps'] == $b['number_jumps'])
		{
			return 0;
		}
		return ($a['number_jumps'] < $b['number_jumps']) ? -1 : 1;
	}

	public function action_save()
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
				if( !isset($system['y']) || $system['y'] < 0 )
				{
					$system['y'] = 0;
				}

				if( !isset($system['x']) || $system['x'] < 0 )
				{
					$system['x'] = 0;
				}

				if( !Auth::$session->group->allow_map_height_expand && $system['y'] > 400 )
				{
					$system['y'] = 380;
				}

				$this->chainmap->update_system($system['id'], array('x' => $system['x'], 'y' => $system['y']));
			}

			Auth::$session->group->logAction('editmap', Auth::$session->character_name. " edited the map");

			$this->chainmap->rebuild_map_data_cache();
		}

		exit();
	}

	private function _hash_array_to_string($arr)
	{
		foreach( $arr as $k => $v )
		{
			$arr[$k] = DB::connection()->getPdo()->quote($v);
		}
		return implode(',', $arr);
	}

	public function action_connection_delete()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		$systemIDs = array();

		$hashes = json_decode($this->request->body(), true);

		$wormholeHashes = $hashes['wormhole_hashes'];
		$stargateHashes = $hashes['stargate_hashes'];
		$jumpbridgeHashes = $hashes['jumpbridge_hashes'];
		$cynoHashes = $hashes['cyno_hashes'];

		if( is_array($cynoHashes) && count($cynoHashes) > 0 )
		{
			$log_message = Auth::$session->character_name.' performed a mass delete of the following cynos: ';

			$cynoHashes = $this->_hash_array_to_string($cynoHashes);

			$stargates = DB::select('SELECT s.*, sto.name as to_name, sfrom.name as from_name
														FROM chainmap_cynos s
														INNER JOIN solarsystems sto ON sto.id = s.to_system_id
														INNER JOIN solarsystems sfrom ON sfrom.id = s.from_system_id
														WHERE s.hash IN('.$cynoHashes.') AND s.group_id=:groupID AND s.chainmap_id=:chainmap',[
															'groupID' => Auth::$session->group->id,
															'chainmap' => Auth::$session->accessData['active_chain_map']
														]);

			foreach( $stargates as $sg )
			{
				$systemIDs[] = $sg->to_system_id;
				$systemIDs[] = $sg->from_system_id;

				$log_message .= $sg->to_name . ' to ' . $sg->from_name . ', ';
			}
			$systemIDs = array_unique( $systemIDs );

			DB::delete('DELETE FROM chainmap_cynos WHERE hash IN('.$cynoHashes.') AND group_id=:groupID AND chainmap_id=:chainmap',
						[
							'groupID' => Auth::$session->group->id,
							'chainmap' => Auth::$session->accessData['active_chain_map']
						]);

			$log_message .= ' from the chainmap "'. $this->chainmap->chainmap_name.'"';
			
			Auth::$session->group->logAction('delwhs', $log_message );
		}

		if( is_array($jumpbridgeHashes) && count($jumpbridgeHashes) > 0 )
		{
			$log_message = Auth::$session->character_name.' performed a mass delete of the following jumpbridges: ';

			$jumpbridgeHashes = $this->_hash_array_to_string($jumpbridgeHashes);

			$stargates = DB::select('SELECT s.*, sto.name as to_name, sfrom.name as from_name
														FROM chainmap_jumpbridges s
														INNER JOIN solarsystems sto ON sto.id = s.to_system_id
														INNER JOIN solarsystems sfrom ON sfrom.id = s.from_system_id
														WHERE s.hash IN('.$jumpbridgeHashes.') AND s.group_id=:groupID AND s.chainmap_id=:chainmap',[
															'groupID' => Auth::$session->group->id,
															'chainmap' => Auth::$session->accessData['active_chain_map']
														]);

			foreach( $stargates as $sg )
			{
				$systemIDs[] = $sg->to_system_id;
				$systemIDs[] = $sg->from_system_id;

				$log_message .= $sg->to_name . ' to ' . $sg->from_name . ', ';
			}
			$systemIDs = array_unique( $systemIDs );

			DB::delete('DELETE FROM chainmap_jumpbridges WHERE hash IN('.$jumpbridgeHashes.') AND group_id=:groupID AND chainmap_id=:chainmap',
						[
							'groupID' => Auth::$session->group->id,
							'chainmap' => Auth::$session->accessData['active_chain_map']
						]);

			$log_message .= ' from the chainmap "'. $this->chainmap->chainmap_name.'"';
			Auth::$session->group->logAction('delwhs', $log_message );
		}

		if( is_array($stargateHashes) && count($stargateHashes) > 0 )
		{
			$log_message = Auth::$session->character_name.' performed a mass delete of the following stargates: ';

			$stargateHashes = $this->_hash_array_to_string($stargateHashes);

			$stargates = DB::select('SELECT s.*, sto.name as to_name, sfrom.name as from_name
														FROM chainmap_stargates s
														INNER JOIN solarsystems sto ON sto.id = s.to_system_id
														INNER JOIN solarsystems sfrom ON sfrom.id = s.from_system_id
														WHERE s.hash IN('.$stargateHashes.') AND s.group_id=:groupID AND s.chainmap_id=:chainmap',[
															'groupID' => Auth::$session->group->id,
															'chainmap' => Auth::$session->accessData['active_chain_map']
														]);

			foreach( $stargates as $sg )
			{
				$systemIDs[] = $sg->to_system_id;
				$systemIDs[] = $sg->from_system_id;

				$log_message .= $sg->to_name . ' to ' . $sg->from_name . ', ';
			}
			$systemIDs = array_unique( $systemIDs );

			DB::delete('DELETE FROM chainmap_stargates WHERE hash IN('.$stargateHashes.') AND group_id=:groupID AND chainmap_id=:chainmap',
						[
							'groupID' => Auth::$session->group->id,
							'chainmap' => Auth::$session->accessData['active_chain_map']
						]);

			$log_message .= ' from the chainmap "'. $this->chainmap->chainmap_name.'"';
			Auth::$session->group->logAction('delwhs', $log_message );
		}


		if( is_array($wormholeHashes) && count($wormholeHashes) > 0 )
		{
			$tmp = $this->chainmap->delete_wormholes($wormholeHashes);
			$systemIDs = array_merge( $systemIDs, $tmp );
			$systemIDs = array_unique( $systemIDs );

			groupUtils::deleteLinkedSigWormholes(Auth::$session->group->id, $wormholeHashes);
		}

		if(!empty($systemIDs))
		{
			//update system to make sigs we deleted disappear
			foreach($systemIDs as $id)
			{
				$this->chainmap->update_system( $id, ['lastUpdate' => time(),
													'lastActive' => time()]
											);
			}

			$this->chainmap->reset_systems( $systemIDs );

			$this->chainmap->rebuild_map_data_cache();
		}

		$this->response->body(json_encode('1'));
	}

	public function action_connection_edit()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1

		if(	 !$this->siggyAccessGranted() )
		{
			echo json_encode(array('error' => 1, 'error_message' => 'Invalid auth'));
			exit();
		}

		$update = array();
		$hash = ($_POST['hash']);

		if( empty($hash) )
		{
			echo json_encode(array('error' => 1, 'error_message' => 'Missing wormhole hash'));
			exit();
		}

		$wormhole = DB::selectOne('SELECT * FROM wormholes WHERE hash=:hash AND group_id=:groupID AND chainmap_id=:chainmap',
											[
												'hash' => $hash,
												'groupID' => Auth::$session->group->id,
												'chainmap' => Auth::$session->accessData['active_chain_map']
											]);

		if( $wormhole == null )
		{
			echo json_encode(array('error' => 1, 'error_message' => 'Wormhole does not exist.'));
			exit();
		}

		if( isset($_POST['eol']) )
		{
			$update['eol'] = intval($_POST['eol']);

			if( !$wormhole->eol && $update['eol'] )
			{
				$update['eol_date_set'] = time();
			}
			elseif( $wormhole->eol && !$update['eol'] )
			{
				$update['eol_date_set'] = 0;
			}
		}

		if( isset($_POST['frigate_sized']) )
		{
			$update['frigate_sized'] = intval($_POST['frigate_sized']);
		}

		if( isset($_POST['mass']) )
		{
			$update['mass'] = intval($_POST['mass']);
		}

		if( isset($_POST['wh_type_name']) )
		{
			$update['wh_type_id'] = $this->lookupWHTypeByName($_POST['wh_type_name']);
		}

		$update['updated_at'] = Carbon::now()->toDateTimeString();

		if( !empty($update) )
		{
			DB::table('wormholes')
					->where('hash', '=', $hash)
					->where('group_id', '=', Auth::$session->group->id)
					->where('chainmap_id', '=', Auth::$session->accessData['active_chain_map'])
					->update( $update );

			$this->chainmap->rebuild_map_data_cache();
		}
	}

	private function lookupWHTypeByName(string $name): int
	{
		$static = DB::selectOne( "SELECT `id` FROM statics WHERE LOWER(name)=?",[strtolower($name)]);
		if( $static != null )
		{
			return $static->id;
		}
		return 0;
	}

	public function action_connection_add()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1

		if(	 !$this->siggyAccessGranted() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}

		$type = $_POST['type'];

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

		$currentLocation = CharacterLocation::findWithinCutoff(Auth::$session->character_id);
		$fromSysID = 0;
		if( $fromSysCurrent )
		{
			if( $currentLocation != null )
			{
				$fromSysID = $currentLocation->system_id;
			}
			else
			{
				$errors[] = "'From current location' will not work out of game";
			}
		}
		elseif( !empty($fromSys) )
		{
			$fromSysID = $this->chainmap->find_system_by_name($fromSys);
			if( !$fromSysID )
			{
				$errors[] = "The 'from' system could not be looked up by name.";
			}
		}

		$toSysID = 0;
		if( $toSysCurrent )
		{
			if( $currentLocation != null )
			{
				$toSysID = $currentLocation->system_id;
			}
			else
			{
				$errors[] = "'To current location' will not work out of game";
			}
		}
		elseif( !empty($toSys) )
		{
			$toSysID = $this->chainmap->find_system_by_name($toSys);
			if( !$toSysID )
			{
				$errors[] = "The 'to' system could not be looked up by name.";
			}
		}

		if( !$fromSysID )
		{
			$errors[] = "The 'to' system cannot be blank.";
		}

		if( !$toSysID )
		{
			$errors[] = "The 'to' system cannot be blank.";
		}

		if( $fromSysID == $toSysID )
		{
			$errors[] = "You cannot link a system to itself!";
		}

		if( $type == 'wormhole' )
		{
			$whTypeName = $_POST['wh_type_name'];
			$whTypeID = 0;
			if( !empty($whTypeName) )
			{
				$whTypeID = $this->lookupWHTypeByName($whTypeName);
				if(!$whTypeID)
				{
					$errors[] = "Invalid WH Type Name";
				}
			}

			$whHash = mapUtils::whHashByID($fromSysID , $toSysID);

			$connection = DB::selectOne("SELECT `hash` FROM wormholes WHERE hash=:hash AND group_id=:group AND chainmap_id=:chainmap",[
								'hash' => $whHash,
								'group' => Auth::$session->group->id,
								'chainmap' => Auth::$session->accessData['active_chain_map']
								]);
			if( $connection != null )
			{
				$errors[] = "Wormhole already exists";
			}

			if( count($errors) > 0 )
			{
				echo json_encode(array('success' => 0, 'dataErrorMsgs' => $errors ) );
				exit();
			}

			$eol = intval($_POST['eol']);
			$mass = intval($_POST['mass']);

			$this->chainmap->add_system_to_map($fromSysID, $toSysID, $eol, $mass, $whTypeID);

			$message = Auth::$session->character_name.' added wormhole manually between system IDs' . $fromSysID . ' and ' . $toSysID;

			Auth::$session->group->logAction('addwh', $message );
		}
		else if( $type == 'stargate' )
		{
			if( count($errors) > 0 )
			{
				echo json_encode(array('success' => 0, 'dataErrorMsgs' => $errors ) );
				exit();
			}

			$this->chainmap->add_stargate_to_map($fromSysID, $toSysID);
		}
		else if( $type == 'jumpbridge' )
		{
			if( count($errors) > 0 )
			{
				echo json_encode(array('success' => 0, 'dataErrorMsgs' => $errors ) );
				exit();
			}

			$this->chainmap->add_jumpbridge_to_map($fromSysID, $toSysID);
		}
		else if( $type == 'cyno' )
		{
			if( count($errors) > 0 )
			{
				echo json_encode(array('success' => 0, 'dataErrorMsgs' => $errors ) );
				exit();
			}

			$this->chainmap->add_cyno_to_map($fromSysID, $toSysID);
		}


		echo json_encode( array('success' => 1) );

		exit();
	}

	public function action_switch()
	{
		$desired_chainmap = intval($_POST['chainmap_id']);
		$selected_id = 0;
		$default_id = 0;
		foreach(Auth::$session->accessibleChainMaps() as $c)
		{
			if( $c->chainmap_id == $desired_chainmap )
			{
				$selected_id = $c->chainmap_id;
			}
		}

		if( $selected_id )
		{
			Cookie::set('chainmap', $selected_id);
		}

		if( !$selected_id )
		{
			throw new Exception("Selected chain map not found!");
		}
	}

	public function action_connections()
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

		$data = $this->chainmap->get_map_cache();

		$output = [
					'connections' => [],
					'systems' => $data['systems']
					];

		foreach($data['wormholes'] as $c)
		{
			$c->type = 'wormhole';
			$output['connections'][] = $c;
		}

		foreach($data['cynos'] as $c)
		{
			$c->type = 'cyno';
			$output['connections'][] = $c;
		}

		foreach($data['stargates'] as $c)
		{
			$c->type = 'stargate';
			$output['connections'][] = $c;
		}

		foreach($data['jumpbridges'] as $c)
		{
			$c->type = 'jumpbridge';
			$output['connections'][] = $c;
		}

		$this->response->body(json_encode($output));
	}

	public function action_autocomplete_wh()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		$q = '';
		if ( isset($_GET['q']) )
		{
			$q = trim(strtolower($_GET['q']));
		}

		if ( empty($q) )
		{
			return;
		}

		$output = array();
		$customsystems = DB::select('SELECT solarsystems.id, 
											solarsystems.name,
											activesystems.displayName as display_name,
											regions.regionName as region_name,
											solarsystems.sysClass as class
										FROM activesystems
										LEFT JOIN solarsystems ON(activesystems.systemID=solarsystems.id)
										LEFT JOIN regions ON(solarsystems.region=regions.regionID)
										WHERE displayName like :query
										AND groupID=:group
										AND chainmap_id=:chainmap',
										[
											'query' => $q.'%',
											'group' => Auth::$session->group->id,
											'chainmap' => Auth::$session->accessData['active_chain_map']
										]);

		foreach($customsystems as $system)
		{
			$output[] = array('id' => (int)$system->id,
								'name' => $system->name,
								'display_name' => $system->display_name,
								'region_name' => $system->region_name );
		}

		$this->response->body(json_encode($output));
	}

	public function action_jump_log()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		if(	!$this->siggyAccessGranted() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}

		if( !isset($_GET['wormhole_hash']) || empty( $_GET['wormhole_hash'] ) )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Missing wormhole_hash parameter.'));
			exit();
		}

		$hash = $_GET['wormhole_hash'];

		/* Include all the group tracked jumps from all chainmaps since this is important not to trap oneself out */
		$jumpData = array();
		$jumpData  = DB::select("SELECT wt.ship_id, c.name as character_name, wt.character_id, 
													wt.origin_id, 
													wt.destination_id, 
													wt.jumped_at, 
														s.shipName, 
														s.mass, 
														s.shipClass
													FROM wormhole_jumps wt
													LEFT JOIN ships as s ON s.shipID = wt.ship_id
													JOIN characters c ON (c.id = wt.character_id)
													WHERE wt.group_id = :groupID AND wt.wormhole_hash = :hash
													ORDER BY wt.jumped_at DESC",[
														'groupID' => Auth::$session->group->id,
														'hash' => $hash
													]);

		$totalMass = 0;
		foreach( $jumpData as $jump )
		{
			$totalMass += $jump->mass;
		}

		$output['totalMass'] = $totalMass;
		$output['jumpItems'] = $jumpData;

		$this->response->body(json_encode($output));
	}
}
