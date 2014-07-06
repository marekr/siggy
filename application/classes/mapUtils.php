<?php

final class MapUtils
{
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