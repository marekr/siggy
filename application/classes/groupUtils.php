<?php

final class groupUtils
{	
		static function getMapCache($groupID, $subGroupID)
		{
				$cache = Cache::instance( CACHE_METHOD );
				
				$cacheName = 'mapCache-'.$groupID.'-'.$subGroupID;
				
				if( $mapData = $cache->get( $cacheName, FALSE ) )
				{
					return $mapData;
				}
				else
				{
					$mapData = self::rebuildMapCache($groupID, $subGroupID);
											
					return $mapData;			
				}
		}
		
		static function createNewGroup($data)
		{
				$homeSysData = self::parseHomeSystems($data['homeSystems']);
				
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
									'homeSystems' => $homeSysData['homeSystems'],
									'homeSystemIDs' => $homeSysData['homeSystemIDs'],
									'dateCreated' => time()
								);
				$result = DB::insert('groups', array_keys($insert) )->values( array_values($insert) )->execute();
				$groupID = $result[0];
				self::createGroupActiveSystems( $groupID );
				
				return $groupID;
		}
		
		static function hashGroupPassword( $password, $salt )
		{
			return sha1($password . $salt);
		}
		
		static function parseHomeSystems( $homeSystems )
		{
			$return = array();
		
			$homeSystems = trim($homeSystems);
			if( !empty($homeSystems) )
			{
					$homeSystems = explode(',', $homeSystems);
					$homeSystemIDs = array();
					if( is_array( $homeSystems ) )
					{
							foreach($homeSystems as $k => $v)
							{
									if( trim($v) != '' )
									{
											$id = miscUtils::findSystemByName(trim($v));
											if( $id != 0 )
											{
													$homeSystemIDs[] = $id;
											}
											else
											{
													unset($homeSystems[ $k ] );
											}
									}
									else
									{
											unset($homeSystems[ $k ] );
									}
							}
					}
					$return['homeSystemIDs'] = implode(',', $homeSystemIDs);
					$return['homeSystems'] = implode(',', $homeSystems);
			}
			else
			{
				$return['homeSystems'] = '';
				$return['homeSystemIDs'] = '';
			}	
			
			return $return;	
		}
		
		static function createGroupActiveSystems($groupID, $subGroup = 0)
		{
			$systems = DB::select('id')->from('solarsystems')->order_by('id', 'ASC')->execute()->as_array();
			
			foreach($systems as $system)
			{		
				DB::query(Database::INSERT, 'INSERT INTO activesystems (`systemID`,`groupID`,`subGroupID`) VALUES(:systemID, :groupID, :subGroupID) ON DUPLICATE KEY UPDATE systemID=systemID')
														->param(':systemID', $system['id'] )->param(':groupID', $groupID )->param(':subGroupID', $subGroup )->execute();
			}
		}


		static function rebuildMapCache($groupID, $subGroupID = 0)
		{
				$cache = Cache::instance( CACHE_METHOD );
				$cacheName = 'mapCache-'.$groupID.'-'.$subGroupID;
				
				$homeSystems = self::getHomeSystems($groupID, $subGroupID);
				$mapData = mapUtils::rebuildMapData($groupID, $subGroupID, $homeSystems);
				
				$cache->set($cacheName, $mapData);		 
				
				return $mapData;
		}
		
		static function bookKeeping($groupID, $amount)
		{
			//$amount = (float)$amount;
			
			
			$payments = DB::query(Database::SELECT, "SELECT paymentID, paymentAmount FROM billing_payments WHERE groupID=:groupID AND usageStatus != 'used' ORDER BY paymentProcessedTime ASC")
						->param(':groupID', $groupID)
						  ->execute()->as_array();  
						  
			//oldest bills first sorted and not paid yet
			$bills = DB::query(Database::SELECT, "SELECT billID, appliedPayment, paid, charge FROM billing_bills WHERE groupID=:groupID AND paid != 1 ORDER BY dateCreated ASC")
						->param(':groupID', $groupID)
						  ->execute()->as_array();  
			
			if( count( $bills ) > 0 )
			{
				foreach($bills as $bill)
				{
				
					foreach( $payments as $payment )
					{
						$toPay = $bill['charge'] - $bill['paid'];
						
						if( $payment['paymentAmount'] > $toPay )
						{
						}
						else
						{
							
						}
					}
					
					
					DB::update('billing_bills')->set( $bill )->where('billID', '=',  $bill['billID'])->execute();
					
					//stop when we run out of isk!
					if( $amount <= 0 )
					{
						break;
					}
				}
			}
			else
			{
			}
			
			
			
			
			
			DB::update('groups')->set( array( 'iskBalance' => DB::expr('iskBalance + :amount') ) )->param(':amount', $amount)->where('groupID', '=',  $groupID)->execute();
		
		}
	
	
		static function getHomeSystems($groupID, $subGroupID = 0)
		{			
				$groupData = self::getGroupData($groupID, $subGroupID);
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
						  ->execute()->current();
						  
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

				$corpMemberships = DB::query(Database::SELECT, "SELECT g.*,gr.groupName, gr.groupTicker, sr.sgName FROM groupmembers g 
																LEFT JOIN groups as gr ON(g.groupID=gr.groupID) 
																LEFT JOIN subgroups as sr ON(g.subGroupID=sr.subGroupID) 
																WHERE g.memberType='corp' AND g.eveID= :id")
												->param(':id', $id)
						  ->execute()->as_array();   
					   
				if( count($corpMemberships) > 0 )
				{
						$corp = array();
						$corp['accessName'] = $corpMemberships[0]['accessName'];
						$corp['eveID'] = $corpMemberships[0]['eveID'];
						$corp['memberType'] = $corpMemberships[0]['memberType'];

						foreach( $corpMemberships as $cm )
						{
						//	$corp['groups'][ $cm['groupID'] ] = array( $cm['groupID'], $cm['subGroupID'] );
								$corp['groups'][ $cm['groupID'] ][]  = $cm['subGroupID'];
							//$corp['subgroups'][] = array( 'groupID' => $cm['groupID'], 'groupName' => $cm['groupName'], 'subGroupID' => $cm['subGroupID'], 'subGroupName' => $cm['sgName'] );
						//	$corp['subgroups'][] = $cm['subGroupID'];
							
							$corp['groupDetails']['group'][ $cm['groupID'] ] = array( 'groupName' => $cm['groupName'] );
							$corp['groupDetails']['subgroup'][ $cm['subGroupID'] ] = array( 'subGroupName' => $cm['sgName'], 'accessName' => $cm['accessName'] );
						}

						//	$corp['groups'] = array_unique( $corp['groups'] );

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
				$charMemberships = DB::query(Database::SELECT, "SELECT g.*,gr.groupName, gr.groupTicker, sr.sgName FROM groupmembers g 
															LEFT JOIN groups as gr ON(g.groupID=gr.groupID) 
															LEFT JOIN subgroups as sr ON(g.subGroupID=sr.subGroupID) 
															WHERE g.memberType='char' AND g.eveID= :id")
												->param(':id', $id)
						  ->execute()->as_array();  
						  
				if( count($charMemberships) > 0 )
				{
						$char = array();
						$char['accessName'] = $charMemberships[0]['accessName'];
						$char['eveID'] = $charMemberships[0]['eveID'];
						$char['memberType'] = $charMemberships[0]['memberType'];
						foreach( $charMemberships as $cm )
						{
								//reduce iteration later by indexing by groupID
								//$char['groups'][ $cm['groupID'] ] = array( $cm['groupID'], $cm['subGroupID'] );
								$char['groups'][ $cm['groupID'] ][] = $cm['subGroupID'];
								
								$char['groupDetails']['group'][ $cm['groupID'] ] = array( 'groupName' => $cm['groupName'] );
								$char['groupDetails']['subgroup'][ $cm['subGroupID'] ] = array( 'subGroupName' => $cm['sgName'], 'accessName' => $cm['accessName'] );
						}
						
						
					//	$char['groups'] = array_unique( $char['groups'] );
						
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
				$subGroup = DB::query(Database::SELECT, "SELECT * FROM subgroups WHERE subGroupID = :subGroup")
													->param(':subGroup', $id)
							  ->execute()->current();
						  
				if( $subGroup['subGroupID'] )
				{
					$cache->set('subGroup-'.$subGroup['subGroupID'], $subGroup);         
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
				
				$corpList = $cache->get('corpList');
				
				if( $corpList != null )
				{
						return $corpList;
				}
				else
				{
						$corps = DB::query(Database::SELECT, "SELECT eveID FROM groupmembers WHERE memberType='corp'")
														->execute()->as_array('eveID');  
						
						$corpList = array();
						foreach($corps as $c)
						{
							$corpList[] = $c['eveID'];
						}
						
						$corpList = array_unique( $corpList );
						
						$cache->set('corpList', $corpList);    	
								
						return $corpList;
				}
		}		

		static function getCharList()
		{
				$cache = Cache::instance(CACHE_METHOD);
				
				$charList = $cache->get('charList');
				
				if( is_array($charList) || $charList != null )
				{
						return $charList;
				}
				else
				{
						$chars = DB::query(Database::SELECT, "SELECT eveID FROM groupmembers WHERE memberType='char'")
														->execute()->as_array('eveID');  
						
						$charList = array();
						foreach($chars as $c)
						{
							$charList[] = $c['eveID'];
						}
						
						$cache->set('charList', $charList);    	
								
						return $charList;
				}
		}	
	
		static function getCorpData( $corpID )
		{
				if( !$corpID )
				{
						return FALSE;
				}
				
				$cache = Cache::instance(CACHE_METHOD);
				
				$corpData = $cache->get('corp-'.$corpID);
				if( $corpData != null )
				{
						return $corpData;
				}
				else
				{
						$corpMemberships = DB::query(Database::SELECT, "SELECT g.*,gr.groupName, gr.groupTicker, sr.sgName FROM groupmembers g 
																														LEFT JOIN groups as gr ON(g.groupID=gr.groupID) 
																														LEFT JOIN subgroups as sr ON(g.subGroupID=sr.subGroupID) 
																														WHERE g.memberType='corp' AND g.eveID= :id")
														->param(':id', $corpID)
														->execute()->as_array();   
														
						if( count($corpMemberships) > 0 )
						{
							$corp = array();
							$corp['accessName'] = $corpMemberships[0]['accessName'];
							$corp['eveID'] = $corpMemberships[0]['eveID'];
							$corp['memberType'] = $corpMemberships[0]['memberType'];
							
							foreach( $corpMemberships as $cm )
							{
							//	$corp['groups'][ $cm['groupID'] ] = array( $cm['groupID'], $cm['subGroupID'] );
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
	
		static function getGroupData( $groupID, $subGroupID = 0 )
		{
				if( !$groupID )
				{
						return FALSE;
				}
				
				$cache = Cache::instance(CACHE_METHOD);
				$groupData = $cache->get('group-'.$groupID);
				
				
				if( $groupData == null )
				{
						$groupData = DB::query(Database::SELECT, "SELECT * FROM groups WHERE groupID = :group")
														->param(':group', $groupID)
														->execute()->current();
														
						if( $groupData['groupID'] )
						{
								$cache->set('group-'.$groupData['groupID'], $groupData);			
						}
						else
						{
								return FALSE;
						}
				}
				
				$subGroupData = array('subGroupID' => 0, 'sgName' => 'Default', 'sgAuthPassword' => '', 'sgHomeSystems' => '', 'sgHomeSystemIDs' => '');
				if( $subGroupID != 0 )
				{
						$subGroupData = $cache->get('subgroup-'.$subGroupID);
						
						if( $subGroupData == null )
						{
								$subGroupData = DB::query(Database::SELECT, "SELECT * FROM subgroups WHERE subGroupID = :subGroup")
																->param(':subGroup', $subGroupID)
																->execute()->current();
																
																
								if( $subGroupData['subGroupID'] )
								{
										$cache->set('subGroup-'.$subGroupData['subGroupID'], $subGroupData);				 
								}
								else
								{
										return FALSE;
								}
						}
				}
				
				return array_merge($groupData, $subGroupData);
		}
	
		static function getCharData( $charID )
		{
				if( !$charID )
				{
					return FALSE;
				}
				
				$cache = Cache::instance( CACHE_METHOD );
				
				$charData = $cache->get('char-'.$charID);
				
				if( $charData != null )
				{
						return $charData;
				}
				else
				{
						$charMemberships = DB::query(Database::SELECT, "SELECT g.*,gr.groupName, gr.groupTicker, sr.sgName FROM groupmembers g 
																														LEFT JOIN groups as gr ON(g.groupID=gr.groupID) 
																														LEFT JOIN subgroups as sr ON(g.subGroupID=sr.subGroupID) 
																														WHERE g.memberType='char' AND g.eveID= :id")
														->param(':id', $charID)
														->execute()->as_array();  
														
						if( count($charMemberships) > 0 )
						{
							$char = array();
							$char['accessName'] = $charMemberships[0]['accessName'];
							$char['eveID'] = $charMemberships[0]['eveID'];
							$char['memberType'] = $charMemberships[0]['memberType'];
							foreach( $charMemberships as $cm )
							{
									$char['groups'][ $cm['groupID'] ][] = $cm['subGroupID'];
							//	$char['groups'][ $cm['groupID'] ] = array( $cm['groupID'], $cm['subGroupID'] );
								
								$char['groupDetails']['group'][ $cm['groupID'] ] = array( 'groupName' => $cm['groupName'], 'groupTicker' => $cm['groupTicker'] );
								$char['groupDetails']['subgroup'][ $cm['subGroupID'] ] = array( 'subGroupName' => $cm['sgName'], 'accessName' => $cm['accessName'] );
							}
							
						//	$char['groups'] = array_unique( $char['groups'] );
							
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