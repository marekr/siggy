<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;

class Controller_Search extends FrontController {
	protected $output_array = array();

	public function before()
	{
		parent::before();
	}

	public function action_everything()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		$results = array();

		$query = "%".$this->request->query('query')."%";

		$poses = DB::select("SELECT p.pos_id, p.pos_system_id as system_id,
													s.name as system_name, p.pos_owner,
													p.pos_added_date as created,
													p.pos_location_planet,
													p.pos_location_moon,
													p.pos_online,
													p.pos_type,
													p.pos_size,
													p.pos_notes
												FROM pos_tracker p
												INNER JOIN  solarsystems s ON(p.pos_system_id=s.id)
												WHERE p.group_id = :groupID AND p.pos_owner LIKE :query
												",[
													'groupID' => Auth::$session->group->id,
													'query' => $query
												]);

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
													'groupID' => Auth::$session->group->id,
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

		$this->response->body(json_encode($results));
	}
}
