<?php

class chainmap
{
	private $id = 0;
	public $data = array();
	private $group_id = 0;
	
	public function __construct($chainmap_id, $group_id)
	{
		$this->load($chainmap_id, $group_id);
	}
	
	public function load($chainmap_id, $group_id)
	{
		$this->id = 0;
		$this->group_id = 0;
		$this->data = array();
	
		$data = DB::query(Database::SELECT, "SELECT * FROM chainmaps 
											  WHERE chainmap_id=:chainmap_id")
						->param(':chainmap_id', $chainmap_id)
						->execute()
						->current();
						
		if( !isset($data['chainmap_id']) )
		{
			throw new Exception("Invalid chain map ID");
		}

		$this->id = $chainmap_id;
		$this->data = $data;
		$this->group_id = $group_id;
	}
	
	public function get_map_cache()
	{
		$cache = Cache::instance( CACHE_METHOD );
		
		$cache_name = 'map_data_cache-'.$this->id;
		
		if( $map_data = $cache->get( $cache_name, FALSE ) )
		{
			return $map_data;
		}
		else
		{
			$map_data = $this->rebuild_map_data_cache();

			return $map_data;			
		}
	}
	
	public function get_map_data()
	{
		$additionalSystems = $this->get_home_systems();
		
		$data = array();
		
		$wormholes = DB::query(Database::SELECT, "SELECT `hash`, `to`, `from`, eol, mass, eolToggled FROM wormholes WHERE groupID=:group AND chainmap_id=:chainmap")
								 ->param(':group', $this->group_id)
								 ->param(':chainmap', $this->id)
								 ->execute()
								 ->as_array('hash');	 
		
		$systemsToPoll = array();
		$wormholeHashes = array();
		foreach( $wormholes as $wormhole )
		{
			$systemsToPoll[] = $wormhole['to'];
			$systemsToPoll[] = $wormhole['from'];
			$wormholeHashes[] = $wormhole['hash'];
		}
		
		$data['systems'] = array();
		$data['systemIDs'] = array();
		
		$systemsToPoll = array_unique($systemsToPoll);
		

		if( $additionalSystems != null && is_array($additionalSystems) && count($additionalSystems) > 0 )
		{
			$systemsToPoll = array_merge($systemsToPoll, $additionalSystems);
		}

		if( count($systemsToPoll) > 0 )
		{
			$systemsToPoll = implode(',', $systemsToPoll);
			
			$killCutoff = time()-(3600*1);	//minus 2 hours
			
			$chainMapSystems = DB::query(Database::SELECT, "SELECT ss.name,
															ss.id as systemID,
															COALESCE(sa.displayName,'') as displayName,
															COALESCE(sa.x,0) as x,
															COALESCE(sa.y,10) as y,
															COALESCE(sa.activity,0) as activity,
															COALESCE(sa.inUse,0) as inUse,
															ss.sysClass,
															ss.effect,
															(SELECT SUM(kills) FROM apihourlymapdata WHERE systemID=ss.id AND hourStamp >= :kill_cutoff) as kills_in_last_2_hours,
															(SELECT SUM(npcKills) FROM apihourlymapdata WHERE systemID=ss.id AND hourStamp >= :kill_cutoff) as npcs_kills_in_last_2_hours
															FROM solarsystems ss
															LEFT OUTER JOIN activesystems sa ON (ss.id = sa.systemID AND sa.groupID=:group AND sa.chainmap_id=:chainmap)
															WHERE ss.id IN(".$systemsToPoll.")  ORDER BY ss.id ASC")
											->param(':group', $this->group_id)
											->param(':chainmap', $this->id)
											->param(':kill_cutoff', $killCutoff)
											->execute()
											->as_array('systemID');	
			
			foreach( $chainMapSystems as &$sys ) 
			{
				if( in_array( $sys['systemID'], $additionalSystems ) )
				{
						$sys['special'] = 1;
				}
				else
				{
						$sys['special'] = 0;
				}
			}
			$data['systems'] = $chainMapSystems;
			$data['systemIDs'] = explode(',', $systemsToPoll);
		}
		
		$data['wormholeHashes'] = $wormholeHashes;
		$data['wormholes'] = $wormholes;
		$data['updateTime'] = time();
		
		return $data;
	}		
	
	public function rebuild_map_data_cache()
	{
		$cache = Cache::instance( CACHE_METHOD );
		$cache_name = 'map_data_cache-'.$this->id;
		
		$map_data = $this->get_map_data();
		
		$cache->set($cache_name, $map_data, 1800);		 
		
		return $map_data;
	}

	public function get_home_systems()
	{			
		$homeSystems = array();
		if( $this->data['chainmap_homesystems_ids'] != '' )
		{
			$homeSystems = explode(',', $this->data['chainmap_homesystems_ids']);
		}

		return $homeSystems;
	}
	
	public function get_connected_system($system)
	{
		return DB::query(Database::SELECT, "SELECT x,y FROM activesystems 
														WHERE groupID=:group AND
														chainmap_id=:chain AND
														systemID IN (SELECT
																		CASE WHEN w.`to`=:sys
																			THEN w.`from`
																			ELSE w.`to`
																		END AS `connected_system` 
																		FROM wormholes w
																		WHERE (w.`to`=:sys OR w.`from`=:sys) AND w.groupID=:group AND w.chainmap_id=:chain)")
						->param(':sys', intval($system))
						->param(':group', $this->group_id)
						->param(':chain', $this->id)
						->execute()
						->as_array();	
	}
	
	public function add_system_to_map($whHash, $sys1,$sys2, $eol=0, $mass=0)
	{
		$sys1Connections = $this->get_connected_system($sys1);	
		$sys2Connections = $this->get_connected_system($sys2);	
		
		$sys1Count = count($sys1Connections);
		$sys2Count = count($sys2Connections);
		
		if( $sys1Count == 0 && $sys2Count != 0 )
		{
			$this->_placeSystem($sys2,$sys2Connections, $sys1);
		}
		else if( $sys2Count == 0 && $sys1Count != 0 )
		{
			//sys2 is "new"
			$this->_placeSystem($sys1,$sys1Connections, $sys2);
		}
		else if( $sys1Count == 0 && $sys2Count == 0 )
		{
			//both are new
			//we just map one
			//ensure its not a home system, those stay fixed lol
			$homeSystems = $this->get_home_systems();
			if( in_array($sys2, $homeSystems) && !in_array($sys1, $homeSystems) )
			{
				//sys2 is home system
				//map sys1 instead
				$this->_placeSystem($sys2,$sys2Connections,$sys1);
			}
			else if( in_array($sys1, $homeSystems) && !in_array($sys2, $homeSystems) )
			{
				//sys1 is home system
				//map sys2 instead
				$this->_placeSystem($sys1,$sys1Connections,$sys2);
			}
			else
			{
				//don't mess with the system positions if both are home systems
			}
		}
		
		//default case is both systems already mapped, so just connect them
		try
		{
			DB::query(Database::INSERT, 'INSERT INTO wormholes (`hash`, `to`, `from`, `groupID`, `chainmap_id`, `lastJump`, `eol`, `mass`)
														 VALUES(:hash, :to, :from, :groupID, :chainmap, :lastJump, :eol, :mass)')
							->param(':hash', $whHash )
							->param(':to', $sys1 )
							->param(':from', $sys2)
							->param(':eol', $eol )
							->param(':mass', $mass )
							->param(':groupID', $this->group_id )
							->param(':chainmap', $this->id )
							->param(':lastJump', time() )
							->execute();
		}
		catch( Exception $e )
		{
			//do nothing
			throw new Exception("HALO");
			return;
		}
		
		$this->rebuild_map_data_cache();
	}
	
	
	private function _placeSystem($originSys, $originSystems, $systemToBePlaced)
	{
		$sysPos = NULL;
		$sysData = DB::query(Database::SELECT, "SELECT * FROM activesystems 
														WHERE groupID=:group AND
														chainmap_id=:chainmap AND
														systemID=:sys")
								->param(':sys', $originSys)
								->param(':group', $this->group_id)
								->param(':chainmap', $this->id)
								->execute()
								->current();

		$spots = mapUtils::generatePossibleSystemLocations($sysData['x'], $sysData['y']);

		foreach($spots as $spot)
		{
			$intersect = false;
			foreach($originSystems as $sys)
			{
				if( mapUtils::doBoxesIntersect(mapUtils::coordsToBB($spot['x'],$spot['y']), mapUtils::coordsToBB($sys['x'],$sys['y'])) )
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
		
		$this->update_system($systemToBePlaced, array( 'x' => intval($sysPos['x']),
															'y' => intval($sysPos['y']),
															'lastUpdate' => time() )
									);
	}
	
	public function update_system($system_id, $data)
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
		
		$q = DB::query(Database::INSERT, 'INSERT INTO activesystems (`systemID`, `groupID`, `chainmap_id`'.$extraIns.')
										  VALUES(:system_id, :group_id, :chainmap'.$extraInsVal.')
										  ON DUPLICATE KEY UPDATE '.$extraUp)
							->param(':system_id', $system_id )
							->param(':group_id', $this->group_id )
							->param(':chainmap', $this->id );
			
		foreach($data as $k => $v)
		{
			$q->param(':'.$k, $v);
		}
		
		$q->execute();
	}
	
	
	public function reset_systems($system_ids)
	{
		if( !is_array($system_ids) || !count($system_ids)	)
		{
			return;
		}
		
		$home_systems = $this->get_home_systems();

		//only enable this "Feature" if we have a home system, a.k.a. RAGE INSURANCE
		if( !count($home_systems)	)
		{
			return;
		}
		
		foreach( $system_ids as $sys_id )
		{
			if( !in_array($sys_id, $home_systems) )
			{
				$check = DB::query(Database::SELECT, 'SELECT * FROM	 wormholes WHERE groupID=:groupID AND chainmap_id=:chain_map AND (`to`=:id OR `from`=:id)')
								->param(':groupID', $this->group_id)
								->param(':chain_map', $this->id)
								->param(':id', $sys_id)
								->execute()
								->current();
				
				if( !$check['hash'] )
				{ 
					$this->update_system($sys_id, array('displayName' => '', 'inUse' => 0, 'activity' => 0 ) );
				}
			}
		}
	}
}