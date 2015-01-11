<?php

use Carbon\Carbon;
require_once APPPATH.'classes/FrontController.php';

class Controller_Search extends FrontController
{
	protected $output_array = array();

	public function before()
	{
		parent::before();
	}

	public function action_everything()
	{
		$results = array();

		$query = "%".$this->request->query('query')."%";

		$poses = DB::query(Database::SELECT, "SELECT p.pos_id, p.pos_system_id as system_id,
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
												")
			->param(':groupID', Auth::$session->groupID )
			->param(':query', $query)
			->execute()
			->as_array();

		foreach($poses as $pos)
		{
			$results[] = array('type' => 'pos',
								'system_id' => $pos['system_id'],
								'data' => $pos
							);
		}

		$sigs = DB::query(Database::SELECT, "SELECT ss.sig, ss.systemID as system_id, s.name as system_name, ss.description,
												ss.type, ss.created, ss.siteID as type_id
												FROM systemsigs ss
												INNER JOIN  solarsystems s ON(ss.systemID=s.id)
												WHERE ss.groupID = :groupID AND ss.description LIKE :query
												")
			->param(':groupID', Auth::$session->groupID )
			->param(':query', $query)
			->execute()
			->as_array();

		foreach($sigs as $sig)
		{
			if( $sig['sig'] == 'POS' )
			{
				$results[] = array('type' => 'legacy_pos',
									'system_id' => $sig['system_id'],
									'data' => $sig
				);
			}
		}

		print json_encode($results);
		exit();
	}
}
