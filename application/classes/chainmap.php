<?php

use Carbon\Carbon;

class chainmap {

	public $id = 0;
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
											  WHERE chainmap_id=:chainmap_id AND group_id=:group")
						->param(':chainmap_id', $chainmap_id)
						->param(':group', $group_id)
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

		$wormholes = DB::query(Database::SELECT, "SELECT w.`hash`,
														w.to_system_id,
														w.from_system_id,
			 											w.eol,
														w.mass,
														w.eol_date_set,
														w.frigate_sized,
														w.created_at,
														w.updated_at,
														s.`mass` as wh_mass,
														s.`jump_mass` as wh_jump_mass,
														s.`lifetime` as wh_lifetime,
														s.`regen` as wh_regen,
														s.`name` as wh_name
			 										FROM wormholes AS w
													LEFT JOIN statics AS s ON(s.id=w.wh_type_id)
													WHERE group_id=:group
													AND chainmap_id=:chainmap")
								 ->param(':group', $this->group_id)
								 ->param(':chainmap', $this->id)
								 ->execute()
								 ->as_array('hash');


		$systemsToPoll = array();
		$wormholeHashes = array();
		foreach( $wormholes as $k => $wormhole )
		{
			/* Include all the group tracked jumps from all chainmaps since this is important not to trap oneself out */
			$jumpTotal  = DB::query(Database::SELECT, "SELECT COALESCE(SUM(s.mass),0) as total
														FROM wormholetracker wt
														LEFT JOIN ships as s ON s.shipID = wt.shipTypeID
														WHERE wt.group_id = :groupID AND wt.wormhole_hash = :hash")
											->param(':groupID', $this->group_id)
											->param(':hash', $wormhole['hash'])
											->execute()
											->current();

			$wormholes[$k]['total_tracked_mass'] = $jumpTotal['total'];

			$systemsToPoll[] = $wormhole['to_system_id'];
			$systemsToPoll[] = $wormhole['from_system_id'];
			$wormholeHashes[] = $wormhole['hash'];
		}
		$data['wormholes'] = $wormholes;

		/* Stargates */
		$stargates = DB::query(Database::SELECT, "SELECT s.`hash`,
													s.to_system_id,
													s.from_system_id,
													s.created_at,
													s.updated_at
			 										FROM chainmap_stargates AS s
													WHERE s.group_id=:group
													AND s.chainmap_id=:chainmap")
								 ->param(':group', $this->group_id)
								 ->param(':chainmap', $this->id)
								 ->execute()
								 ->as_array('hash');
		$data['stargates'] = $stargates;

		foreach( $stargates as $stargate )
		{
			$systemsToPoll[] = $stargate['to_system_id'];
			$systemsToPoll[] = $stargate['from_system_id'];
		}

		/* Jump bridges */
		$jumpbridges = DB::query(Database::SELECT, "SELECT s.`hash`,
													s.to_system_id,
													s.from_system_id,
													s.created_at,
													s.updated_at
			 										FROM chainmap_jumpbridges AS s
													WHERE s.group_id=:group
													AND s.chainmap_id=:chainmap")
								 ->param(':group', $this->group_id)
								 ->param(':chainmap', $this->id)
								 ->execute()
								 ->as_array('hash');
		$data['jumpbridges'] = $jumpbridges;

		foreach( $jumpbridges as $jumpbridge )
		{
			$systemsToPoll[] = $jumpbridge['to_system_id'];
			$systemsToPoll[] = $jumpbridge['from_system_id'];
		}

		/* Cynos */
		$cynos = DB::query(Database::SELECT, "SELECT s.`hash`,
													s.to_system_id,
													s.from_system_id,
													s.created_at,
													s.updated_at
			 										FROM chainmap_cynos AS s
													WHERE s.group_id=:group
													AND s.chainmap_id=:chainmap")
								 ->param(':group', $this->group_id)
								 ->param(':chainmap', $this->id)
								 ->execute()
								 ->as_array('hash');
		$data['cynos'] = $cynos;

		foreach( $cynos as $cyno )
		{
			$systemsToPoll[] = $cyno['to_system_id'];
			$systemsToPoll[] = $cyno['from_system_id'];
		}

		/* Systems */

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
															COALESCE(sa.rally,0) as rally,
															COALESCE(sa.hazard,0) as hazard,
															ss.sysClass,
															ss.effect,
															r.regionName as region_name,
															(SELECT SUM(kills) FROM apihourlymapdata WHERE systemID=ss.id AND hourStamp >= :kill_cutoff) as kills_in_last_2_hours,
															(SELECT SUM(npcKills) FROM apihourlymapdata WHERE systemID=ss.id AND hourStamp >= :kill_cutoff) as npcs_kills_in_last_2_hours
															FROM solarsystems ss
															LEFT OUTER JOIN activesystems sa ON (ss.id = sa.systemID AND sa.groupID=:group AND sa.chainmap_id=:chainmap)
															INNER JOIN regions r ON(r.regionID=ss.region)
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
																		CASE WHEN w.to_system_id=:sys
																			THEN w.from_system_id
																			ELSE w.to_system_id
																		END AS `connected_system`
																		FROM wormholes w
																		WHERE (w.to_system_id=:sys OR w.from_system_id=:sys)
																		AND w.group_id=:group AND w.chainmap_id=:chain)")
						->param(':sys', intval($system))
						->param(':group', $this->group_id)
						->param(':chain', $this->id)
						->execute()
						->as_array();
	}

	public function system_is_mapped( $system )
	{
		$exists = DB::query(Database::SELECT, "SELECT `hash` FROM wormholes WHERE (from_system_id=:system OR to_system_id=:system) AND group_id=:group AND chainmap_id=:chainmap")
					->param(':system', $system)
					->param(':group', Auth::$session->groupID)
					->param(':chainmap', Auth::$session->accessData['active_chain_map'])
					->execute()
					->current();

		if( isset($exists['hash'])  )
		{
			return true;
		}

		return false;
	}

	public function delete_all_system_connections( $system )
	{
		DB::query(Database::DELETE, 'DELETE FROM wormholes WHERE (to_system_id = :system OR from_system_id = :system) AND group_id=:groupID AND chainmap_id=:chainmap')
			->param(':groupID', Auth::$session->groupID)
			->param(':chainmap', Auth::$session->accessData['active_chain_map'])
			->param(':system', $system)
			->execute();
	}

	public function add_system_to_map($sys1, $sys2, $eol=0, $mass=0, $wh_type_id = 0)
	{
		$whHash = mapUtils::whHashByID($sys1 , $sys2);

		$this->_placeSystems($sys1,$sys2);

		try
		{
			$insert = array('hash' => $whHash,
							'to_system_id' => $sys1,
							'from_system_id' => $sys2,
							'eol' => $eol,
							'mass' => $mass,
							'group_id' => $this->group_id,
							'wh_type_id' => $wh_type_id,
							'chainmap_id' => $this->id,
							'last_jump' => time(),
							'created_at' => Carbon::now()->toDateTimeString()
							);

			DB::insert('wormholes', array_keys($insert) )->values(array_values($insert))->execute();
		}
		catch( Exception $e )
		{
			//do nothing
			throw new Exception("Failed to insert wormhole, probably a duplicate of paralle processing multiple people jumping :/");
			return;
		}

		$this->rebuild_map_data_cache();
	}

	private function _placeSystems($sys1, $sys2)
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
	}

	public function add_stargate_to_map($sys1, $sys2)
	{
		$whHash = mapUtils::whHashByID($sys1, $sys2);

		$this->_placeSystems($sys1,$sys2);
		try
		{
			$insert = array('hash' => $whHash,
							'to_system_id' => $sys1,
							'from_system_id' => $sys2,
							'group_id' => $this->group_id,
							'chainmap_id' => $this->id,
							'created_at' => Carbon::now()->toDateTimeString()
							);

			DB::insert('chainmap_stargates', array_keys($insert) )->values(array_values($insert))->execute();
		}
		catch( Exception $e )
		{
			//do nothing
			throw new Exception("Stargate already exists");
			return;
		}

		$this->rebuild_map_data_cache();
	}

	public function add_jumpbridge_to_map($sys1, $sys2)
	{
		$whHash = mapUtils::whHashByID($sys1 , $sys2);

		$this->_placeSystems($sys1,$sys2);
		try
		{
			$insert = array('hash' => $whHash,
							'to_system_id' => $sys1,
							'from_system_id' => $sys2,
							'group_id' => $this->group_id,
							'chainmap_id' => $this->id,
							'created_at' => Carbon::now()->toDateTimeString()
							);

			DB::insert('chainmap_jumpbridges', array_keys($insert) )->values(array_values($insert))->execute();
		}
		catch( Exception $e )
		{
			//do nothing
			throw new Exception("Jumpbridge already exists");
			return;
		}

		$this->rebuild_map_data_cache();
	}

	public function add_cyno_to_map($sys1, $sys2)
	{
		$whHash = mapUtils::whHashByID($sys1, $sys2);

		$this->_placeSystems($sys1,$sys2);
		try
		{
			$insert = array('hash' => $whHash,
							'to_system_id' => $sys1,
							'from_system_id' => $sys2,
							'group_id' => $this->group_id,
							'chainmap_id' => $this->id,
							'created_at' => Carbon::now()->toDateTimeString()
							);

			DB::insert('chainmap_cynos', array_keys($insert) )->values(array_values($insert))->execute();
		}
		catch( Exception $e )
		{
			//do nothing
			throw new Exception("Stargate already exists");
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
				$check = DB::query(Database::SELECT, 'SELECT * FROM	 wormholes WHERE group_id=:groupID AND chainmap_id=:chain_map AND (to_system_id=:id OR from_system_id=:id)')
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

	public function find_system_by_name($name)
	{
		$systemID = 0;
		if( empty($name) )
		{
			return 0;
		}

		$systemID = DB::query(Database::SELECT, "SELECT systemID,displayName FROM activesystems WHERE groupID=:groupID AND chainmap_id=:chainmap AND displayName LIKE :name")
													->param(':name', $name )
													->param(':groupID', $this->group_id)
													->param(':chainmap', $this->id)
													->execute()
													->get('systemID', 0);

		$name = strtolower($name);
		if( $systemID == 0 )
		{
			$systemID = DB::query(Database::SELECT, 'SELECT id,name FROM solarsystems WHERE LOWER(name) = :name')
																->param(':name', $name )
																->execute()
																->get('id', 0);

		}

		return $systemID;
	}

	private function _hash_array_to_string($arr)
	{
		foreach( $arr as $k => $v )
		{
			$arr[$k] = "'".($v)."'";
		}
		return implode(',', $arr);
	}

	public function delete_wormholes($wormholeHashes)
	{
		$log_message = Auth::$session->charName.' performed a mass delete of the following wormholes: ';

		$wormholeHashes = $this->_hash_array_to_string($wormholeHashes);

		$wormholes = DB::query(Database::SELECT, 'SELECT w.*, sto.name as to_name, sfrom.name as from_name
													FROM wormholes w
													INNER JOIN solarsystems sto ON sto.id = w.to_system_id
													INNER JOIN solarsystems sfrom ON sfrom.id = w.from_system_id
													WHERE w.hash IN('.$wormholeHashes.') AND w.group_id=:groupID AND w.chainmap_id=:chainmap')
						->param(':groupID', $this->group_id)
						->param(':chainmap', $this->id)
						->execute();

		$sigs = [];
		$systemIDs = [];
		foreach( $wormholes as $wh )
		{
			$systemIDs[] = $wh['to_system_id'];
			$systemIDs[] = $wh['from_system_id'];

			$log_message .= $wh['to_name'] . ' to ' . $wh['from_name'] . ', ';
		}

		$systemIDs = array_unique( $systemIDs );
		$sigs = array_unique( $sigs );
		$sigs = implode(',', $sigs);

		DB::query(Database::DELETE, 'DELETE FROM wormholes WHERE hash IN('.$wormholeHashes.') AND group_id=:groupID AND chainmap_id=:chainmap')
						->param(':groupID', $this->group_id)
						->param(':chainmap', $this->id)
						->execute();


		DB::query(Database::DELETE, 'DELETE FROM wormholetracker WHERE wormhole_hash IN('.$wormholeHashes.') AND group_id=:groupID AND chainmap_id=:chainmap')
						->param(':groupID', $this->group_id)
						->param(':chainmap', $this->id)
						->execute();

		$log_message .= ' from the chainmap "'. $this->data['chainmap_name'].'"';

		$group = Group::find($this->group_id);
		$group->logAction('delwhs', $log_message );

		return $systemIDs;
	}
}
