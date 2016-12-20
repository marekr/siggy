<?php

final class groupUtils {
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
}
