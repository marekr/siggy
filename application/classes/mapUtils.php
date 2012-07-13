<?php

final class MapUtils
{

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
	
	static function rebuildMapData($groupID, $subGroupID = 0, $additionalSystems = null)
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
					
					$chainMapSystems = DB::query(Database::SELECT, "SELECT sa.systemID,ss.name,sa.displayName,sa.inUse,sa.x,sa.y,sa.activity,ss.sysClass,ss.effect FROM activesystems sa 
					 INNER JOIN solarsystems ss ON ss.id = sa.systemID
					WHERE sa.systemID IN(".$systemsToPoll.") AND sa.groupID=:group AND sa.subGroupID=:subgroup ORDER BY sa.systemID ASC")
												->param(':group', $groupID)->param(':subgroup', $subGroupID)->execute()->as_array('systemID');	
					
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