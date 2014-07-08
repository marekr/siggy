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
	
	public function action_findNearestExits()
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
			$targetID = mapUtils::findSystemByName($target, $this->groupData['groupID'], $this->groupData['subGroupID'] );
		}
		
		if( $targetID == 0 || $targetID >= 31000000 )
		{
			echo json_encode(array('error' => 1, 'errorMsg' => 'Invalid system'));
			exit();
		}
		
 
		
		$systems = DB::query(Database::SELECT, "( SELECT DISTINCT w.`to` as sys_id,ss.name
												FROM wormholes w 
												LEFT JOIN solarsystems ss ON (ss.id = w.`to`)
												WHERE w.`to`< 31000000 AND w.groupID=:group AND w.subGroupID=:subGroupID)
												UNION DISTINCT
											( SELECT DISTINCT w.`from` as sys_id, ss.name
											FROM wormholes w
											LEFT JOIN solarsystems ss ON (ss.id = w.`from`)
											WHERE w.`from` < 31000000 AND w.groupID=:group AND w.subGroupID=:subGroupID)")
						->param(':group', $this->groupData['groupID'])
						->param(':subGroupID', $this->groupData['subGroupID'])
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
				
				miscUtils::setActiveSystem($system['id'], array('x' => $system['x'],
																'y' => $system['y']),
											$this->groupData['groupID'],
											$this->groupData['subGroupID']
											);
			}
			
			groupUtils::log_action($this->groupData['groupID'], 'editmap', $this->groupData['charName']. " edited the map");
		
			groupUtils::rebuildMapCache($this->groupData['groupID'], $this->groupData['subGroupID']);
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
		if( is_array($hashes) && count($hashes) > 0 )
		{
			foreach( $hashes as $k =>	 $v )
			{
				$hashes[$k] = "'".($v)."'";
			}
			$hashes = implode(',', $hashes);
			
			$wormholes = DB::query(Database::SELECT, 'SELECT * FROM	 wormholes WHERE hash IN('.$hashes.') AND groupID=:groupID AND subGroupID=:subGroupID')
							->param(':groupID', $this->groupData['groupID'])
							->param(':subGroupID', $this->groupData['subGroupID'])
							->execute();
			
			$systemIDs = array();
			foreach( $wormholes as $wh )
			{
				$systemIDs[] = $wh['to'];
				$systemIDs[] = $wh['from'];
			}
			$systemIDs = array_unique( $systemIDs );
			
			DB::query(Database::DELETE, 'DELETE FROM wormholes WHERE hash IN('.$hashes.') AND groupID=:groupID AND subGroupID=:subGroupID')
							->param(':groupID', $this->groupData['groupID'])
							->param(':subGroupID', $this->groupData['subGroupID'])
							->execute();
			
			
			DB::query(Database::DELETE, 'DELETE FROM wormholetracker WHERE whHash IN('.$hashes.') AND groupID=:groupID AND subGroupID=:subGroupID')
							->param(':groupID', $this->groupData['groupID'])
							->param(':subGroupID', $this->groupData['subGroupID'])
							->execute();
			
			$message = $this->groupData['charName'].' deleted wormholes with IDs: '.implode(',', $systemIDs);
			groupUtils::log_action($this->groupData['groupID'],'delwhs', $message );
			
			$this->sysResetByMap( $systemIDs );
			
			groupUtils::rebuildMapCache($this->groupData['groupID'], $this->groupData['subGroupID']);
		}
		exit();
	}
	
	private function sysResetByMap($systemIDs)
	{
		if( !is_array($systemIDs) || !count($systemIDs)	 )
		{
			return;
		}
		
		$homeSystems = groupUtils::getHomeSystems($this->groupData['groupID'], $this->groupData['subGroupID']);
		
		//only enable this "Feature" if we have a home system, a.k.a. RAGE INSURANCE
		if( !count($homeSystems)	)
		{
			return;
		}
		
		foreach( $systemIDs as $systemID )
		{
			if( !in_array($systemID, $homeSystems) )
			{
				$check = DB::query(Database::SELECT, 'SELECT * FROM	 wormholes WHERE groupID=:groupID AND subGroupID=:subGroupID AND (`to`=:id OR `from`=:id)')
								->param(':groupID', $this->groupData['groupID'])
								->param(':subGroupID', $this->groupData['subGroupID'])
								->param(':id', $systemID)
								->execute()
								->current();
				
				if( !$check['hash'] )
				{ 
					miscUtils::setActiveSystem($systemID, array('displayName' => '',
																'inUse' => 0,
																'activity' => 0 ),
												$this->groupData['groupID'],
												$this->groupData['subGroupID']
												);
				}
			}
		}
	}
	
	public function action_wh_disconnect()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		
		$hash = ($_POST['hash']);
	 
		$wormhole = DB::query(Database::SELECT, 'SELECT * FROM	wormholes WHERE hash=:hash AND groupID=:groupID AND subGroupID=:subGroupID')
							->param(':hash',$hash)
							->param(':groupID', $this->groupData['groupID'])
							->param(':subGroupID', $this->groupData['subGroupID'])
							->execute()
							->current();
				
		if( !$wormhole['hash'] )
		{
			return;
		}		 
				
		DB::query(Database::DELETE, 'DELETE FROM wormholes WHERE hash=:hash AND groupID=:groupID AND subGroupID=:subGroupID')
								->param(':hash',$hash)
								->param(':groupID', $this->groupData['groupID'])
								->param(':subGroupID', $this->groupData['subGroupID'])
								->execute();
		
		DB::query(Database::DELETE, 'DELETE FROM wormholetracker WHERE whHash=:hash AND groupID=:groupID AND subGroupID=:subGroupID')
								->param(':hash',$hash)
								->param(':groupID', $this->groupData['groupID'])
								->param(':subGroupID', $this->groupData['subGroupID'])
								->execute();
			
		$message = $this->groupData['charName'].' deleted wormhole between system IDs: '.implode(',', array($wormhole['to'], $wormhole['from']) );

		groupUtils::log_action($this->groupData['groupID'],'delwh', $message );
			
		$this->sysResetByMap( array($wormhole['to'], $wormhole['from']) );
		
		groupUtils::rebuildMapCache($this->groupData['groupID'], $this->groupData['subGroupID']);
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
			
			$wormhole = DB::query(Database::SELECT, 'SELECT * FROM	wormholes WHERE hash=:hash AND groupID=:groupID AND subGroupID=:subGroupID')
								->param(':hash',$hash)->param(':groupID', $this->groupData['groupID'])
								->param(':subGroupID', $this->groupData['subGroupID'])
								->execute()
								->current();
					
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
		
			DB::update('wormholes')
					->set( $update )
					->where('hash', '=', $hash)
					->where('groupID', '=', $this->groupData['groupID'])
					->where('subGroupID', '=', $this->groupData['subGroupID'])
					->execute();
					
			groupUtils::rebuildMapCache($this->groupData['groupID'], $this->groupData['subGroupID']);
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
				$fromSysID = mapUtils::findSystemByName($fromSys, $this->groupData['groupID'], $this->groupData['subGroupID'] );
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
				$toSysID = mapUtils::findSystemByName($toSys, $this->groupData['groupID'], $this->groupData['subGroupID'] );
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
			
			$connection = DB::query(Database::SELECT, "SELECT `hash` FROM wormholes WHERE hash=:hash AND groupID=:group AND subGroupID=:subGroupID")
								->param(':hash', $whHash)
								->param(':group', $this->groupData['groupID'])
								->param(':subGroupID', $this->groupData['subGroupID'])
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
			
						
			mapUtils::addSystemToMap($this->groupData['groupID'],$this->groupData['subGroupID'],$whHash, $fromSysID, $toSysID, $eol, $mass);

			$message = $this->groupData['charName'].' added wormhole manually between system IDs' . $fromSysID . ' and ' . $toSysID;

			groupUtils::log_action($this->groupData['groupID'],'addwh', $message );
		}
		echo json_encode( array('success' => 1) );
		
		exit();
	}
}