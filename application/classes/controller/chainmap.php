<?php

require_once APPPATH.'classes/FrontController.php';
require_once APPPATH.'classes/access.php';
require_once APPPATH.'classes/astar.php';
require_once APPPATH.'classes/systempathfinder.php';

class Controller_Chainmap extends FrontController
{
	/*
		Key value array
	*/
	public $template = 'template/public';

	protected $output_array = array();


	public function before()
	{
		parent::before();

		if( $this->groupData['active_chain_map'] )
		{
			$this->chainmap = new Chainmap($this->groupData['active_chain_map'],$this->groupData['groupID']);
		}
	}

	public function action_find_nearest_exits()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1\


		if(	 !$this->siggyAccessGranted() )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid auth'));
			exit();
		}


		$target = isset($_REQUEST['target']) ? trim($_REQUEST['target']) : "";
		$targetCurrentSys = isset($_REQUEST['current_system']) ? intval($_REQUEST['current_system']) : 0;

		$targetID = 0;


		if( $targetCurrentSys )
		{
			$targetID = $_SERVER['HTTP_EVE_SOLARSYSTEMID'];
		}
		else if (!empty($target))
		{
			$targetID = mapUtils::findSystemByName($target, $this->groupData['groupID'], $this->groupData['active_chain_map'] );
		}

		if( $targetID == 0 || $targetID >= 31000000 )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid system'));
			exit();
		}

		$systems = DB::query(Database::SELECT, "( SELECT DISTINCT w.`to` as sys_id,ss.name
												FROM wormholes w
												LEFT JOIN solarsystems ss ON (ss.id = w.`to`)
												WHERE w.`to`< 31000000 AND w.groupID=:group AND w.chainmap_id=:chainmap)
												UNION DISTINCT
											( SELECT DISTINCT w.`from` as sys_id, ss.name
											FROM wormholes w
											LEFT JOIN solarsystems ss ON (ss.id = w.`from`)
											WHERE w.`from` < 31000000 AND w.groupID=:group AND w.chainmap_id=:chainmap)")
						->param(':group', $this->groupData['groupID'])
						->param(':chainmap', $this->groupData['active_chain_map'])
						->execute()->as_array();

		$pather = new SystemPathFinder();
		$result = array();
		foreach($systems as $system)
		{
			$path = $pather->PathFind($targetID, $system['sys_id']);

			$result[] = array('system_id' => $system['sys_id'], 'system_name' => $system['name'], 'number_jumps' => count($path) );
		}

		usort($result, array('Controller_Chainmap','sortResults'));
		echo json_encode(array('result' => $result));
		exit();
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
				if( !isset($system['y']) || $system['y'] < 0 || $system['y'] > 400 )
				{
					$system['y'] = 0;
				}

				if( !isset($system['x']) || $system['x'] < 0 )
				{
					$system['x'] = 0;
				}

				$this->chainmap->update_system($system['id'], array('x' => $system['x'], 'y' => $system['y']));
			}

			groupUtils::log_action($this->groupData['groupID'], 'editmap', $this->groupData['charName']. " edited the map");

			$this->chainmap->rebuild_map_data_cache();
		}

		exit();
	}

	public function action_wh_mass_delete()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1

		$hashes = json_decode($_POST['hashes']);
		$log_message = $this->groupData['charName'].' performed a mass delete of the following wormholes: ';
		if( is_array($hashes) && count($hashes) > 0 )
		{
			foreach( $hashes as $k =>	 $v )
			{
				$hashes[$k] = "'".($v)."'";
			}
			$hashes = implode(',', $hashes);

			$wormholes = DB::query(Database::SELECT, 'SELECT w.*, sto.name as to_name, sfrom.name as from_name
														FROM	 wormholes w
														INNER JOIN solarsystems sto ON sto.id = w.to
														INNER JOIN solarsystems sfrom ON sfrom.id = w.from
														WHERE w.hash IN('.$hashes.') AND w.groupID=:groupID AND w.chainmap_id=:chainmap')
							->param(':groupID', $this->groupData['groupID'])
							->param(':chainmap', $this->groupData['active_chain_map'])
							->execute();

			$systemIDs = array();
			foreach( $wormholes as $wh )
			{
				$systemIDs[] = $wh['to'];
				$systemIDs[] = $wh['from'];

				$log_message .= $wh['to_name'] . ' to ' . $wh['from_name'] . ', ';
			}
			$systemIDs = array_unique( $systemIDs );

			DB::query(Database::DELETE, 'DELETE FROM wormholes WHERE hash IN('.$hashes.') AND groupID=:groupID AND chainmap_id=:chainmap')
							->param(':groupID', $this->groupData['groupID'])
							->param(':chainmap', $this->groupData['active_chain_map'])
							->execute();


			DB::query(Database::DELETE, 'DELETE FROM wormholetracker WHERE whHash IN('.$hashes.') AND groupID=:groupID AND chainmap_id=:chainmap')
							->param(':groupID', $this->groupData['groupID'])
							->param(':chainmap', $this->groupData['active_chain_map'])
							->execute();

			groupUtils::log_action($this->groupData['groupID'],'delwhs', $log_message );

			$this->chainmap->reset_systems( $systemIDs );

			$this->chainmap->rebuild_map_data_cache();
		}
		exit();
	}

	public function action_wh_disconnect()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;

		$hash = ($_POST['hash']);

		$wormhole = DB::query(Database::SELECT, 'SELECT w.*, sto.name as to_name, sfrom.name as from_name
												 FROM wormholes w
												 INNER JOIN solarsystems sto ON sto.id = w.to
												 INNER JOIN solarsystems sfrom ON sfrom.id = w.from
												 WHERE w.hash=:hash AND w.groupID=:groupID AND w.chainmap_id=:chainmap')
							->param(':hash',$hash)
							->param(':groupID', $this->groupData['groupID'])
							->param(':chainmap', $this->groupData['active_chain_map'])
							->execute()
							->current();

		if( !$wormhole['hash'] )
		{
			return;
		}

		DB::query(Database::DELETE, 'DELETE FROM wormholes WHERE hash=:hash AND groupID=:groupID AND chainmap_id=:chainmap')
								->param(':hash',$hash)
								->param(':groupID', $this->groupData['groupID'])
								->param(':chainmap', $this->groupData['active_chain_map'])
								->execute();

		DB::query(Database::DELETE, 'DELETE FROM wormholetracker WHERE whHash=:hash AND groupID=:groupID AND chainmap_id=:chainmap')
								->param(':hash',$hash)
								->param(':groupID', $this->groupData['groupID'])
								->param(':chainmap', $this->groupData['active_chain_map'])
								->execute();

		$log_message = $this->groupData['charName'].' deleted wormhole between systems '.$wormhole['to_name'].' and '.$wormhole['from_name'];
		groupUtils::log_action($this->groupData['groupID'],'delwh', $log_message );

		$this->chainmap->reset_systems( array($wormhole['to'], $wormhole['from']) );

		$this->chainmap->rebuild_map_data_cache();
	}


	public function action_wh_save()
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

			$wormhole = DB::query(Database::SELECT, 'SELECT * FROM	wormholes WHERE hash=:hash AND groupID=:groupID AND chainmap_id=:chainmap')
								->param(':hash',$hash)
								->param(':groupID', $this->groupData['groupID'])
								->param(':chainmap', $this->groupData['active_chain_map'])
								->execute()
								->current();

			if( !$wormhole['hash'] )
			{
				echo json_encode(array('error' => 1, 'errorMsg' => 'Wormhole does not exist.'));
				exit();
			}

			$update['eol'] = intval($_POST['eol']);
			$update['frigate_sized'] = intval($_POST['frigate_sized']);

			$update['mass'] = intval($_POST['mass']);

			if( !$wormhole['eol'] && $update['eol'] )
			{
				$update['eolToggled'] = time();
			}
			elseif( $wormhole['eol'] && !$update['eol'] )
			{
				$update['eolToggled'] = 0;
			}

			DB::update('wormholes')
					->set( $update )
					->where('hash', '=', $hash)
					->where('groupID', '=', $this->groupData['groupID'])
					->where('chainmap_id', '=', $this->groupData['active_chain_map'])
					->execute();

			$this->chainmap->rebuild_map_data_cache();
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
				$fromSysID = $this->chainmap->find_system_by_name($fromSys);
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
				$toSysID = $this->chainmap->find_system_by_name($toSys);
				if( !$toSysID )
				{
					$errors[] = "The 'to' system could not be looked up by name.";
				}
			}

            if( $fromSysID == $toSysID )
            {
				$errors[] = "You cannot link a system to itself!";
            }

			$whHash = mapUtils::whHashByID($fromSysID , $toSysID);

			$connection = DB::query(Database::SELECT, "SELECT `hash` FROM wormholes WHERE hash=:hash AND groupID=:group AND chainmap_id=:chainmap")
								->param(':hash', $whHash)
								->param(':group', $this->groupData['groupID'])
								->param(':chainmap', $this->groupData['active_chain_map'])
								->execute()->current();

			if( isset($connection['hash']) )
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

			$this->chainmap->add_system_to_map($whHash, $fromSysID, $toSysID, $eol, $mass);

			$message = $this->groupData['charName'].' added wormhole manually between system IDs' . $fromSysID . ' and ' . $toSysID;

			groupUtils::log_action($this->groupData['groupID'],'addwh', $message );
		}
		echo json_encode( array('success' => 1) );

		exit();
	}

	public function action_switch()
	{
		$desired_chainmap = intval($_POST['chainmap_id']);

		$selected_id = 0;
		$default_id = 0;
		foreach($this->groupData['accessible_chainmaps'] as $c)
		{
			if( $c['chainmap_id'] == $desired_chainmap )
			{
				$selected_id = $c['chainmap_id'];
			}
		}

		if( $selected_id )
		{
			Cookie::set('chainmap', $selected_id);
					print_r($selected_id);
		}

		if( !$selected_id )
		{
			throw new Exception("Selected chain map not found!");
		}
	}
	
	public function action_autocomplete_wh()
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
