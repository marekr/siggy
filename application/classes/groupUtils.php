<?php

final class groupUtils {

	static function getCharacterUsageCount( $group_id )
	{
		$num_corps = DB::query(Database::SELECT, "SELECT SUM(DISTINCT c.member_count) as total FROM groupmembers gm
										LEFT JOIN corporations c ON(gm.eveID = c.id)
										WHERE gm.groupID=:group AND gm.memberType='corp'")
										->param(':group', $group_id)->execute()->current();

		$num_corps = $num_corps['total'];

		$num_chars = DB::query(Database::SELECT, "SELECT COUNT(DISTINCT eveID) as total FROM groupmembers
										WHERE groupID=:group AND memberType ='char' ")
										->param(':group', $group_id)->execute()->current();
		$num_chars = $num_chars['total'];

		return ($num_corps + $num_chars);
	}


	static function deleteLinkedSigWormholes($groupID, $hashes)
	{
		if(empty($hashes))
		{
			return;
		}

		$hashStr = miscUtils::hash_array_to_string($hashes);
		$whsigs = DB::query(Database::SELECT, "SELECT s.*
											FROM wormhole_signatures s
											JOIN chainmaps c
											ON(c.chainmap_id = s.chainmap_id)
											WHERE s.wormhole_hash IN(".$hashStr.")
											AND c.group_id=:group")
								->param(':group', $groupID)
								->execute()
								->as_array();

		//build list of chainmaps to touch
		$chainmaps = [];
		$sigs = [];
		foreach($whsigs as $sig)
		{
			$chainmaps[] = $sig['chainmap_id'];
			$sigs[] = $sig['signature_id'];
		}

		$sigs = array_unique($sigs);
		$sigs = implode(',',$sigs);

		foreach($chainmaps as $k => $cId)
		{
			$c = new Chainmap($cId,$groupID);
			$systemIDs = $c->delete_wormholes($hashes);

			//update system to make sigs we deleted disappear
			foreach($systemIDs as $id)
			{
				$c->update_system( $id, array('lastUpdate' => time(),
											  'lastActive' => time() )
											);
			}

			$c->reset_systems( $systemIDs );

			$c->rebuild_map_data_cache();
		}

		//delete hashes from chainmaps
		if(!empty($sigs))
		{
			DB::query(Database::DELETE, 'DELETE FROM wormhole_signatures WHERE signature_id IN('.$sigs.')')
							->execute();


			DB::query(Database::DELETE, 'DELETE FROM systemsigs WHERE id IN('.$sigs.')')
							->execute();
		}
	}


	static function applyISKCharge($group_id, $amount)
	{
		DB::update('groups')->set( array( 'iskBalance' => DB::expr('iskBalance - :amount') ) )->param(':amount', $amount)->where('id', '=',  $group_id)->execute();
	}

	static function applyISKPayment($group_id, $amount)
	{
		DB::update('groups')->set( array( 'iskBalance' => DB::expr('iskBalance + :amount'), 'billable' => 1 ) )->param(':amount', $amount)->where('id', '=',  $group_id)->execute();
	}

	static function recacheGroup( $id )
	{
		$id = intval($id);
		if( !$id )
		{
			return FALSE;
		}

		$cache = Cache::instance( CACHE_METHOD );
		$group = DB::query(Database::SELECT, "SELECT * FROM groups WHERE id = :group")
								->param(':group', $id)
								->execute()
								->current();


		if( $group['id'] )
		{
			$group['cache_time'] = time();

			$cache->set('group-'.$group['id'], $group);
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

		$corp_memberships = DB::query(Database::SELECT, "SELECT g.*,gr.name, gr.ticker
														FROM groupmembers g
														LEFT JOIN groups as gr ON(g.groupID=gr.id)
														WHERE g.memberType='corp' AND g.eveID= :id")
										->param(':id', $id)
										->execute()
										->as_array();

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
															 'group_name' => $cm['name']
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
		$char_memberships = DB::query(Database::SELECT, "SELECT g.*,gr.name, gr.ticker FROM groupmembers g
													LEFT JOIN groups as gr ON(g.groupID=gr.id)
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
															 'group_name' => $cm['name']
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

		return self::recacheGroup($group_id);

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
