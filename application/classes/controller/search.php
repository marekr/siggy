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
		$poses = POS::with('system')
			->where('group_id',Auth::$session->group->id)
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
