<?php

use Illuminate\Support\Facades\DB;

final class groupUtils {
	static function deleteLinkedSigWormholes($groupID, $hashes)
	{
		if(empty($hashes))
		{
			return;
		}

		$whsigs = DB::table('wormhole_signatures')
            ->join('chainmaps', 'chainmaps.id', '=', 'wormhole_signatures.chainmap_id')
			->whereIn('wormhole_signatures.wormhole_hash',$hashes)
			->where('chainmaps.group_id', $groupID)
			->get()
			->all();

		//build list of chainmaps to touch
		$chainmaps = [];
		$sigs = [];
		foreach($whsigs as $sig)
		{
			$chainmaps[] = $sig->chainmap_id;
			$sigs[] = $sig->signature_id;
		}

		$sigs = array_unique($sigs);

		foreach($chainmaps as $k => $cId)
		{
			$c = Chainmap::find($cId,$groupID);
			$systemIDs = $c->delete_wormholes($hashes);

			//update system to make sigs we deleted disappear
			foreach($systemIDs as $id)
			{
				$c->update_system( $id, ['lastUpdate' => time(),
										'lastActive' => time()]
											);
			}

			$c->reset_systems( $systemIDs );

			$c->rebuild_map_data_cache();
		}

		//delete hashes from chainmaps
		if(!empty($sigs))
		{
			DB::table('wormhole_signatures')
				->whereIn('signature_id', $sigs)
				->delete();

			DB::table('systemsigs')
				->whereIn('id', $sigs)
				->delete();
		}
	}
}
