<?php

final class groupUtils
{
	static function log_action( $group_id, $type, $message )
	{
		$insert = array( 'groupID' => $group_id,
										'type' => $type,
										'message' => $message,
										'entryTime' => time()
						);
		DB::insert('logs', array_keys($insert) )->values(array_values($insert))->execute();
	}
	
	static function getMapCache($group_id, $sub_group_id)
	{
		$cache = Cache::instance( CACHE_METHOD );
		
		$cache_name = 'mapCache-'.$group_id.'-'.$sub_group_id;
		
		if( $map_data = $cache->get( $cache_name, FALSE ) )
		{
			return $map_data;
		}
		else
		{
			$map_data = self::rebuildMapCache($group_id, $sub_group_id);

			return $map_data;			
		}
	}
	
	static function getCharacterUsageCount( $group_id )
	{
		$num_corps = DB::query(Database::SELECT, "SELECT SUM(DISTINCT c.memberCount) as total FROM groupmembers gm
										LEFT JOIN corporations c ON(gm.eveID = c.corporationID)
										WHERE gm.groupID=:group AND gm.memberType='corp'")
										->param(':group', $group_id)->execute()->current();
										
		$num_corps = $num_corps['total'];
		
		$num_chars = DB::query(Database::SELECT, "SELECT COUNT(DISTINCT eveID) as total FROM groupmembers
										WHERE groupID=:group AND memberType ='char' ")
										->param(':group', $group_id)->execute()->current();
		$num_chars = $num_chars['total'];
		
		return ($num_corps + $num_chars);
	}
	
	static function createNewGroup($data)
	{
		$home_sys_data = self::parseHomeSystems($data['homeSystems']);
		
		$salt = miscUtils::generateSalt(10);
		$password = "";
		if( $data['groupPassword'] != "" && $data['authMode'] == 1 )
		{
			$password = self::hashGroupPassword( $data['groupPassword'], $salt );
		}

		$insert = array(
							'groupName' => $data['groupName'],
							'groupTicker' => $data['groupTicker'],
							'billingContact' => $data['ingameContact'],
							'authMode' => $data['authMode'],
							'authSalt' => $salt,
							'authPassword' => $password,
							'homeSystems' => $home_sys_data['homeSystems'],
							'homeSystemIDs' => $home_sys_data['homeSystemIDs'],
							'dateCreated' => time(),
							'paymentCode' => miscUtils::generateString(14),
							'billable' => 1
						);
		$result = DB::insert('groups', array_keys($insert) )->values( array_values($insert) )->execute();
		
		return $result[0];
	}
	
	static function hashGroupPassword( $password, $salt )
	{
		return sha1($password . $salt);
	}
	
	static function parseHomeSystems( $home_systems )
	{
		$return = array();
	
		$home_systems = trim($home_systems);
		if( !empty($home_systems) )
		{
			$home_systems = explode(',', $home_systems);
			$home_system_ids = array();
			if( is_array( $home_systems ) )
			{
				foreach($home_systems as $k => $v)
				{
					if( trim($v) != '' )
					{
						$id = miscUtils::findSystemByName(trim($v));
						if( $id != 0 )
						{
							$home_system_ids[] = $id;
						}
						else
						{
							unset($home_systems[ $k ] );
						}
					}
					else
					{
						unset($home_systems[ $k ] );
					}
				}
			}
			$return['homeSystemIDs'] = implode(',', $home_system_ids);
			$return['homeSystems'] = implode(',', $home_systems);
		}
		else
		{
			$return['homeSystems'] = '';
			$return['homeSystemIDs'] = '';
		}	
		
		return $return;	
	}

	static function rebuildMapCache($group_id, $sub_group_id = 0)
	{
		$cache = Cache::instance( CACHE_METHOD );
		$cache_name = 'mapCache-'.$group_id.'-'.$sub_group_id;
		
		$home_systems = self::getHomeSystems($group_id, $sub_group_id);
		$map_data = mapUtils::rebuildMapData($group_id, $sub_group_id, $home_systems);
		
		$cache->set($cache_name, $map_data, 1800);		 
		
		return $map_data;
	}
	
	static function applyISKCharge($group_id, $amount)
	{
		DB::update('groups')->set( array( 'iskBalance' => DB::expr('iskBalance - :amount') ) )->param(':amount', $amount)->where('groupID', '=',  $group_id)->execute();
	}

	static function applyISKPayment($group_id, $amount)
	{
		DB::update('groups')->set( array( 'iskBalance' => DB::expr('iskBalance + :amount'), 'billable' => 1 ) )->param(':amount', $amount)->where('groupID', '=',  $group_id)->execute();
	}


	static function getHomeSystems($group_id, $sub_group_id = 0)
	{			
		$groupData = self::getGroupData($group_id, $sub_group_id);
		if( empty($groupData) )
		{
			return;
		}
		
		$homeSystems = array();
		if( $groupData['subGroupID'] != 0 )
		{
			if( $groupData['sgHomeSystemIDs'] != '' )
			{
				$homeSystems = explode(',', $groupData['sgHomeSystemIDs']);
			}
		}
		else
		{
			if( $groupData['homeSystemIDs'] != '' )
			{
				$homeSystems = explode(',', $groupData['homeSystemIDs']);
			}
		}

		return $homeSystems;
	}

	static function recacheCorpList()
	{
		$cache = Cache::instance( CACHE_METHOD );
		$corps = DB::query(Database::SELECT, "SELECT eveID FROM groupmembers WHERE memberType='corp'")
					  ->execute()->as_array('eveID');  

		$list = array();
		foreach($corps as $c)
		{
				$list[] = $c['eveID'];
		}

		$list = array_unique($list);

		$cache->set('corpList', $list);        
	}

	static function recacheCharList()
	{
		$cache = Cache::instance( CACHE_METHOD );
		$corps = DB::query(Database::SELECT, "SELECT eveID FROM groupmembers WHERE memberType='char'")
				  ->execute()->as_array('eveID');  

		$list = array();
		foreach($corps as $c)
		{
			$list[] = $c['eveID'];
		}
		$list = array_unique($list);

		$cache->set('charList', $list);
	}  

	static function recacheGroups()
	{
		$cache = Cache::instance( CACHE_METHOD );
		$groups = DB::query(Database::SELECT, "SELECT * FROM groups")
				  ->execute()->as_array('groupID');  

		$cache->set('groupCache', $groups);         
	}

	static function recacheGroup( $id )
	{
		$id = intval($id);
		if( !$id )
		{
			return FALSE;
		}

		$cache = Cache::instance( CACHE_METHOD );
		$group = DB::query(Database::SELECT, "SELECT * FROM groups WHERE groupID = :group")
										->param(':group', $id)
								->execute()
								->current();
		if( $group['groupID'] )
		{
			$cache->set('group-'.$group['groupID'], $group);         
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	static function recacheCorp( $id )
	{
		$id = intval($id);
		if( !$id )
		{
			return FALSE;
		}

		$cache = Cache::instance( CACHE_METHOD );

		$corp_memberships = DB::query(Database::SELECT, "SELECT g.*,gr.groupName, gr.groupTicker, sr.sgName FROM groupmembers g 
														LEFT JOIN groups as gr ON(g.groupID=gr.groupID) 
														LEFT JOIN subgroups as sr ON(g.subGroupID=sr.subGroupID) 
														WHERE g.memberType='corp' AND g.eveID= :id")
										->param(':id', $id)
										->execute()->as_array();   
			   
		if( count($corp_memberships) > 0 )
		{
			$corp = array();
			$corp['accessName'] = $corp_memberships[0]['accessName'];
			$corp['eveID'] = $corp_memberships[0]['eveID'];
			$corp['memberType'] = $corp_memberships[0]['memberType'];

			foreach( $corp_memberships as $cm )
			{
				$corp['groups'][ $cm['groupID'] ][]  = $cm['subGroupID'];
				
				$corp['groupDetails']['group'][ $cm['groupID'] ] = array( 'groupName' => $cm['groupName'] );
				$corp['groupDetails']['subgroup'][ $cm['subGroupID'] ] = array( 'subGroupName' => $cm['sgName'], 'accessName' => $cm['accessName'] );
			}

			$cache->set('corp-'.$corp['eveID'], $corp);    
				 
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	static function recacheChar( $id )
	{		
		$id = intval($id);
		if( !$id )
		{
			return FALSE;
		}

		$cache = Cache::instance( CACHE_METHOD );
		$char_memberships = DB::query(Database::SELECT, "SELECT g.*,gr.groupName, gr.groupTicker, sr.sgName FROM groupmembers g 
													LEFT JOIN groups as gr ON(g.groupID=gr.groupID) 
													LEFT JOIN subgroups as sr ON(g.subGroupID=sr.subGroupID) 
													WHERE g.memberType='char' AND g.eveID= :id")
										->param(':id', $id)
										->execute()->as_array();  
				  
		if( count($char_memberships) > 0 )
		{
			$char = array();
			$char['accessName'] = $char_memberships[0]['accessName'];
			$char['eveID'] = $char_memberships[0]['eveID'];
			$char['memberType'] = $char_memberships[0]['memberType'];
			foreach( $char_memberships as $cm )
			{
				//reduce iteration later by indexing by groupID
				$char['groups'][ $cm['groupID'] ][] = $cm['subGroupID'];
				
				$char['groupDetails']['group'][ $cm['groupID'] ] = array( 'groupName' => $cm['groupName'] );
				$char['groupDetails']['subgroup'][ $cm['subGroupID'] ] = array( 'subGroupName' => $cm['sgName'], 'accessName' => $cm['accessName'] );
			}
			
			$cache->set('char-'.$char['eveID'], $char);
				 
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	static function recacheSubGroup( $id )
	{
		$id = intval($id);
		if( !$id )
		{
			return FALSE;
		}

		$cache = Cache::instance( CACHE_METHOD );
		$sub_group = DB::query(Database::SELECT, "SELECT * FROM subgroups WHERE subGroupID = :subGroup")
											->param(':subGroup', $id)
					  ->execute()->current();
				  
		if( $sub_group['subGroupID'] )
		{
			$cache->set('subGroup-'.$sub_group['subGroupID'], $sub_group);         
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}	
   
	static function deleteSubGroupCache( $id )
	{
		$cache = Cache::instance( CACHE_METHOD );
		
		$cache->delete('subGroup-'.$id);
	}
   
	static function deleteCorpCache( $id )
	{
		$cache = Cache::instance( CACHE_METHOD );
		
		$cache->delete('corp-'.$id);
	}
   
	static function deleteCharCache( $id )
	{
		$cache = Cache::instance( CACHE_METHOD );

		$cache->delete('char-'.$id);
	}

	static function getCorpList()
	{
		$cache = Cache::instance(CACHE_METHOD);
		
		$corp_list = $cache->get('corpList');
		
		if( $corp_list != null )
		{
			return $corp_list;
		}
		else
		{
			$corps = DB::query(Database::SELECT, "SELECT eveID FROM groupmembers WHERE memberType='corp'")
											->execute()->as_array('eveID');  
			
			$corp_list = array();
			foreach($corps as $c)
			{
				$corp_list[] = $c['eveID'];
			}
			
			$corp_list = array_unique( $corp_list );
			
			$cache->set('corpList', $corp_list);    	
					
			return $corp_list;
		}
	}		

	static function getCharList()
	{
		$cache = Cache::instance(CACHE_METHOD);
		
		$char_list = $cache->get('charList');
		
		if( is_array($char_list) || $char_list != null )
		{
			return $char_list;
		}
		else
		{
			$chars = DB::query(Database::SELECT, "SELECT eveID FROM groupmembers WHERE memberType='char'")
											->execute()->as_array('eveID');  
			
			$char_list = array();
			foreach($chars as $c)
			{
				$char_list[] = $c['eveID'];
			}
			
			$cache->set('charList', $char_list);    	
					
			return $char_list;
		}
	}	

	static function getCorpData( $corpID )
	{
		if( !$corpID )
		{
			return FALSE;
		}
		
		$cache = Cache::instance(CACHE_METHOD);
		
		$corp_data = $cache->get('corp-'.$corpID);
		if( $corp_data != null )
		{
			return $corp_data;
		}
		else
		{
			$corp_memberships = DB::query(Database::SELECT, "SELECT g.*,gr.groupName, gr.groupTicker, sr.sgName FROM groupmembers g 
																											LEFT JOIN groups as gr ON(g.groupID=gr.groupID) 
																											LEFT JOIN subgroups as sr ON(g.subGroupID=sr.subGroupID) 
																											WHERE g.memberType='corp' AND g.eveID= :id")
											->param(':id', $corpID)
											->execute()->as_array();   
											
			if( count($corp_memberships) > 0 )
			{
				$corp = array();
				$corp['accessName'] = $corp_memberships[0]['accessName'];
				$corp['eveID'] = $corp_memberships[0]['eveID'];
				$corp['memberType'] = $corp_memberships[0]['memberType'];
				
				foreach( $corp_memberships as $cm )
				{
					$corp['groups'][ $cm['groupID'] ][] = $cm['subGroupID'];			//groupID is stored in the array key i guess
					
					$corp['groupDetails']['group'][ $cm['groupID'] ] = array( 'groupName' => $cm['groupName'], 'groupTicker' => $cm['groupTicker'] );
					$corp['groupDetails']['subgroup'][ $cm['subGroupID'] ] = array( 'subGroupName' => $cm['sgName'], 'accessName' => $cm['accessName'] );
				}
				
				
				$cache->set('corp-'.$corp['eveID'], $corp);    
						 
				return $corp;
			}				
			else
			{
				return FALSE;
			}
		}
	}

	static function getGroupData( $group_id, $sub_group_id = 0 )
	{
		if( !$group_id )
		{
				return FALSE;
		}
		
		$cache = Cache::instance(CACHE_METHOD);
		$group_data = $cache->get('group-'.$group_id);
		
		if( $group_data == null )
		{
			$group_data = DB::query(Database::SELECT, "SELECT * FROM groups WHERE groupID = :group")
											->param(':group', $group_id)
											->execute()->current();
											
			if( $group_data['groupID'] )
			{
				$cache->set('group-'.$group_data['groupID'], $group_data);			
			}
			else
			{
				return FALSE;
			}
		}
		
		$sub_group_data = array('subGroupID' => 0, 'sgName' => 'Default', 'sgAuthPassword' => '', 'sgHomeSystems' => '', 'sgHomeSystemIDs' => '');
		if( $sub_group_id != 0 )
		{
			$sub_group_data = $cache->get('subgroup-'.$sub_group_id);
			
			if( $sub_group_data == null )
			{
				$sub_group_data = DB::query(Database::SELECT, "SELECT * FROM subgroups WHERE subGroupID = :subGroup")
												->param(':subGroup', $sub_group_id)
												->execute()->current();
												
												
				if( $sub_group_data['subGroupID'] )
				{
					$cache->set('subGroup-'.$sub_group_data['subGroupID'], $sub_group_data);				 
				}
				else
				{
					return FALSE;
				}
			}
		}
		
		return array_merge($group_data, $sub_group_data);
	}

	static function getCharData( $char_id )
	{
		if( !$char_id )
		{
			return FALSE;
		}
		
		$cache = Cache::instance( CACHE_METHOD );
		
		$charData = $cache->get('char-'.$char_id);
		
		if( $charData != null )
		{
				return $charData;
		}
		else
		{
			$char_memberships = DB::query(Database::SELECT, "SELECT g.*,gr.groupName, gr.groupTicker, sr.sgName FROM groupmembers g 
																											LEFT JOIN groups as gr ON(g.groupID=gr.groupID) 
																											LEFT JOIN subgroups as sr ON(g.subGroupID=sr.subGroupID) 
																											WHERE g.memberType='char' AND g.eveID= :id")
											->param(':id', $char_id)
											->execute()->as_array();  
											
			if( count($char_memberships) > 0 )
			{
				$char = array();
				$char['accessName'] = $char_memberships[0]['accessName'];
				$char['eveID'] = $char_memberships[0]['eveID'];
				$char['memberType'] = $char_memberships[0]['memberType'];
				foreach( $char_memberships as $cm )
				{
					$char['groups'][ $cm['groupID'] ][] = $cm['subGroupID'];
					
					$char['groupDetails']['group'][ $cm['groupID'] ] = array( 'groupName' => $cm['groupName'], 'groupTicker' => $cm['groupTicker'] );
					$char['groupDetails']['subgroup'][ $cm['subGroupID'] ] = array( 'subGroupName' => $cm['sgName'], 'accessName' => $cm['accessName'] );
				}
				
				$cache->set('char-'.$char['eveID'], $char);
						 
				return $char;
			}
			else
			{
				return FALSE;
			}
		}
	}		   
}