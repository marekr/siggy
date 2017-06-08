<?php 

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use \Group;
use \Chainmap;
use Dingo\Api\Routing\Helpers;

class ChainmapsController extends BaseController {

    use Helpers;

	public function getList(Request $request) {
		
		$group = Group::find($this->user()->group_id);
		$output = [];

		foreach($group->chainMaps() as $c)
		{
			$hs = explode(",", $c->chainmap_homesystems);
			$output[] = [
							'id' => (int)$c->chainmap_id,
							'name' => $c->chainmap_name,
							'homesystems' => $hs
						];
		}

		return $this->response->array($output);
	}
	
	public function getChainmap($id, Request $request) {
		$group = Group::find(1);
		$chainmap = null;
		
		try
		{
			$chainmap = Chainmap::find($id, 1);
		}
		catch(Exception $e)
		{
			$this->_error($e);
			return;
		}

		$data = $chainmap->get_map_cache();
		
		$output['wormholes'] = [];
		foreach($data['wormholes'] as $w)
		{
			$output['wormholes'][] = [
										'hash' => $w->hash,
										'to_system_id' => (int)$w->to_system_id,
										'from_system_id' => (int)$w->from_system_id,
										'eol' => (int)$w->eol,
										'mass' => (int)$w->mass,
										'frigate_sized' => (bool)$w->frigate_sized,
										'created_at' => $w->created_at,
										'updated_at' => $w->updated_at,
										'total_tracked_mass' => (int)$w->total_tracked_mass,
									];
		}
		
		return $this->response->array($output);
	}
}
