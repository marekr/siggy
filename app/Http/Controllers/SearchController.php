<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Facades\Auth;
use Siggy\POS;

class SearchController extends Controller {

	public function everything(Request $request)
	{
		$results = array();

		$query = "%".$request->input('query')."%";
		$poses = POS::with('system')
			->where('group_id',Auth::session()->group->id)
			->where('owner', 'LIKE', $query)
			->get()
			->all();

		foreach($poses as $pos)
		{
			$results[] = array('type' => 'pos',
								'system_id' => $pos->system_id,
								'data' => $pos
							);
		}

		$sigs = DB::select("SELECT ss.sig, ss.systemID as system_id, s.name as system_name, ss.description,
												ss.type, ss.created_at, ss.siteID as type_id
												FROM systemsigs ss
												INNER JOIN  solarsystems s ON(ss.systemID=s.id)
												WHERE ss.groupID = :groupID AND ss.description LIKE :query
												",[
													'groupID' => Auth::session()->group->id,
													'query' => $query
												]);

		foreach($sigs as $sig)
		{
			if( $sig->sig == 'POS' )
			{
				$results[] = array('type' => 'legacy_pos',
									'system_id' => $sig->system_id,
									'data' => $sig
				);
			}
		}

		return response()->json($results);
	}

}
