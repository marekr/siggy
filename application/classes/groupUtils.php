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
	
	static function applyISKCharge($group_id, $amount)
	{
		DB::update('groups')->set( array( 'iskBalance' => DB::expr('iskBalance - :amount') ) )->param(':amount', $amount)->where('groupID', '=',  $group_id)->execute();
	}

	static function applyISKPayment($group_id, $amount)
	{
		DB::update('groups')->set( array( 'iskBalance' => DB::expr('iskBalance + :amount'), 'billable' => 1 ) )->param(':amount', $amount)->where('groupID', '=',  $group_id)->execute();
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
			$chainmaps = DB::query(Database::SELECT, "SELECT * FROM chainmaps WHERE group_id = :group")
								->param(':group', $id)
								->execute()
								->as_array();
			$group['chainmaps'] = $chainmaps;
		
			$cache->set('group-'.$group['groupID'], $group);         
			return $group;
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

		$corp_memberships = DB::query(Database::SELECT, "SELECT g.*,gr.groupName, gr.groupTicker FROM groupmembers g 
														LEFT JOIN groups as gr ON(g.groupID=gr.groupID)
														WHERE g.memberType='corp' AND g.eveID= :id")
										->param(':id', $id)
										->execute()->as_array();   
		if( count($corp_memberships) > 0 )
		{
			$corp = array();
			$corp['accessName'] = $corp_memberships[0]['accessName'];
			$corp['eveID'] = $corp_memberships[0]['eveID'];
			$corp['memberType'] = $corp_memberships[0]['memberType'];
			$corp['groups'] = array();
			foreach( $corp_memberships as $cm )
			{
				$corp['groups'][ $cm['groupID'] ]  = array(
															 'group_id' => $cm['groupID'],
															 'group_name' => $cm['groupName']
															 );
			}
			
			$cache->set('corp-'.$corp['eveID'], $corp);    
				 
			return $corp;
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
		$char_memberships = DB::query(Database::SELECT, "SELECT g.*,gr.groupName, gr.groupTicker FROM groupmembers g 
													LEFT JOIN groups as gr ON(g.groupID=gr.groupID) 
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
				$char['groups'][ $cm['groupID'] ]  = array(
															 'group_id' => $cm['groupID'],
															 'group_name' => $cm['groupName']
															 );
			}
			
			$cache->set('char-'.$char['eveID'], $char);
				 
			return $char;
		}
		else
		{
			return FALSE;
		}
	}

	static function deleteCorpCache($id)
	{
		$cache = Cache::instance( CACHE_METHOD );
		
		$cache->delete('corp-'.$id);
	}
   
	static function deleteCharCache($id)
	{
		$cache = Cache::instance( CACHE_METHOD );

		$cache->delete('char-'.$id);
	}

	static function getCorpData($corpID)
	{
		if( !$corpID )
		{
			return FALSE;
		}
		
		$cache = Cache::instance(CACHE_METHOD);
		
		$corp_data = $cache->get('corp-'.$corpID);
		$corp_data = null;
		if( $corp_data != null )
		{
			return $corp_data;
		}
		else
		{
			return self::recacheCorp($corpID);
		}
	}

	static function getGroupData($group_id)
	{
		if( !$group_id )
		{
				return FALSE;
		}
		
		$cache = Cache::instance(CACHE_METHOD);
		$group_data = $cache->get('group-'.$group_id);
		$group_data = null;
		
		if( $group_data == null )
		{
			return self::recacheGroup($group_id);
		}
		
		return $group_data;
	}

	static function getCharData( $char_id )
	{
		if( !$char_id )
		{
			return FALSE;
		}
		
		$cache = Cache::instance( CACHE_METHOD );
		
		$charData = $cache->get('char-'.$char_id);
		$charData = null;
		
		if( $charData != null )
		{
			return $charData;
		}
		else
		{
			return self::recacheChar($char_id);
		}
	}		   
}