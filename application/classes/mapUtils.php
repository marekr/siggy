<?php

final class MapUtils
{	
	static function addSystemToMap($groupID, $subGroupID, $whHash, $sys1,$sys2, $eol=0, $mass=0)
	{
		$sys1Connections = mapUtils::getConnectedSystems($sys1, $groupID, $subGroupID);	
		$sys2Connections = mapUtils::getConnectedSystems($sys2, $groupID, $subGroupID);	
		
		$sys1Count = count($sys1Connections);
		$sys2Count = count($sys2Connections);
		
		if( $sys1Count == 0 && $sys2Count != 0 )
		{
			self::placeSystem($groupID, $subGroupID,$sys2,$sys2Connections, $sys1);
		}
		else if( $sys2Count == 0 && $sys1Count != 0 )
		{
			//sys2 is "new"
			self::placeSystem($groupID, $subGroupID,$sys1,$sys1Connections, $sys2);
		}
		else if( $sys1Count == 0 && $sys2Count == 0 )
		{
			//both are new
			//we just map one
			//ensure its not a home system, those stay fixed lol
			$homeSystems = groupUtils::getHomeSystems($groupID, $subGroupID);
			if( in_array($sys2, $homeSystems) && !in_array($sys1, $homeSystems) )
			{
				//sys2 is home system
				//map sys1 instead
				self::placeSystem($groupID, $subGroupID,$sys2,$sys2Connections,$sys1);
			}
			else if( in_array($sys1, $homeSystems) && !in_array($sys2, $homeSystems) )
			{
				//sys1 is home system
				//map sys2 instead
				self::placeSystem($groupID, $subGroupID,$sys1,$sys1Connections,$sys2);
			}
			else
			{
				//don't mess with the system positions if both are home systems
			}
		}
		
		
		//default case is both systems already mapped, so just connect them
		try
		{
			DB::query(Database::INSERT, 'INSERT INTO wormholes (`hash`, `to`, `from`, `groupID`, `subGroupID`, `lastJump`, `eol`, `mass`)
														 VALUES(:hash, :to, :from, :groupID, :subGroupID, :lastJump, :eol, :mass)')
							->param(':hash', $whHash )
							->param(':to', $sys1 )
							->param(':from', $sys2)
							->param(':eol', $eol )
							->param(':mass', $mass )
							->param(':groupID', $groupID )
							->param(':subGroupID', $subGroupID )
							->param(':lastJump', time() )->execute();
		}
		catch( Exception $e )
		{
			//do nothing
			throw new Exception("HALO");
			return;
		}
		
		groupUtils::rebuildMapCache($groupID, $subGroupID);
	}
	
	
	static function placeSystem($groupID, $subGroupID, $originSys, $originSystems, $systemToBePlaced)
	{
		$sysPos = NULL;
		$sysData = DB::query(Database::SELECT, "SELECT * FROM activesystems 
														WHERE groupID=:group AND
														subGroupID=:subGroupID AND
														systemID=:sys")
						->param(':sys', $originSys)
						->param(':group', $groupID)
						->param(':subGroupID', $subGroupID)
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
		
		miscUtils::setActiveSystem($systemToBePlaced, array( 'x' => intval($sysPos['x']),
															'y' => intval($sysPos['y']),
															'lastUpdate' => time() ),
											$groupID,
											$subGroupID
									);
	}
	
	
	static function getConnectedSystems($system, $groupID, $subGroupID)
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
																		WHERE (w.`to`=:sys OR w.`from`=:sys) AND w.groupID=:group AND w.subGroupID=:subGroupID)")
						->param(':sys', intval($system))
						->param(':group', intval($groupID))
						->param(':subGroupID', intval($subGroupID))
						->execute()
						->as_array();	
	}
	
	static function generatePossibleSystemLocations($x, $y)
	{
		$originBB = self::coordsToBB($x,$y);
		
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
	
	static function doBoxesIntersect($a, $b)
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
	
	static function coordsToBB($x,$y)
	{
		return array( 'left' => $x,
					  'top' => $y,
					  'width' => 78,
					  'height' => 38,
					  'right' => $x+78,
					  'bottom' => $y+38 );
	}
	
	static function whHashByID($to, $from)
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
	
	static function findSystemByName($name, $groupID, $subGroupID = 0)
	{
		$systemID = 0;
		if( empty($name) )
		{
			return 0;
		}
		
		$name = strtolower($name);
		$systemID = DB::query(Database::SELECT, "SELECT systemID,displayName FROM activesystems WHERE groupID=:groupID AND subGroupID=:subGroupID AND displayName LIKE 'name'")
													->param(':name', $name )->param(':groupID', $groupID)->param(':subGroupID', $subGroupID)->execute()->get('systemID', 0);
													
		if( $systemID == 0 )
		{
			$systemID = DB::query(Database::SELECT, 'SELECT id,name FROM solarsystems WHERE LOWER(name) = :name')
																->param(':name', $name )->execute()->get('id', 0);
																
		}
		
		return $systemID;
	}
	
	static function rebuildMapData($groupID, $subGroupID = 0, $additionalSystems = array())
	{
		$data = array();
		
		$wormholes = DB::query(Database::SELECT, "SELECT `hash`, `to`, `from`, eol, mass, eolToggled FROM wormholes WHERE groupID=:group AND subGroupID=:subGroupID")
						 ->param(':group', $groupID)->param(':subGroupID', $subGroupID)->execute()->as_array('hash');	 
		
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
															LEFT OUTER JOIN activesystems sa ON (ss.id = sa.systemID AND sa.groupID=:group AND sa.subGroupID=:subgroup)
															WHERE ss.id IN(".$systemsToPoll.")  ORDER BY ss.id ASC")
										->param(':group', $groupID)
										->param(':subgroup', $subGroupID)
										->param(':kill_cutoff', $killCutoff)
										->execute()->as_array('systemID');	
			
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
}