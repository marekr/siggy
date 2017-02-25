<?php

use Illuminate\Database\Capsule\Manager as DB;

use Siggy\StructureType;
use Siggy\POSType;

class Controller_Data extends FrontController {

	public function before()
    {
		if( Kohana::$environment == Kohana::PRODUCTION )
		{
			ob_start( 'ob_gzhandler' );
		}
	}

	public function action_systems()
	{
		$this->profiler = NULL;
		$this->response->noCache();

		$systems = DB::select("SELECT ss.id, ss.name, r.regionName as region_name, ss.sec, ss.sysClass as class
													FROM solarsystems ss
													LEFT JOIN regions r ON(ss.region = r.regionID)");

		$this->response->json($systems,JSON_NUMERIC_CHECK);
	}

	public function action_structures()
	{
		$this->profiler = NULL;
		$this->response->noCache();

		$structures = StructureType::all()->keyBy('id');

		$this->response->json($structures);
	}

	public function action_poses()
	{
		$this->profiler = NULL;
		$this->response->noCache();

		$poses = POSType::all()->keyBy('id');

		$this->response->json($poses);
	}

	public function action_sig_types()
	{
		$this->profiler = NULL;
		$this->response->noCache();

		$output = array();

		$wormholeTypes = DB::select("SELECT * FROM statics");

		$types = array();
		foreach($wormholeTypes as &$row)
		{
			$type[ $row->id ] = $row;
		}

		$output['wormhole_types'] = $type;

		$whStaticMap = DB::select("SELECT * FROM wormhole_class_map
												ORDER BY position ASC");

		$outWormholes = array();
		foreach($whStaticMap as $entry)
		{
			$outWormholes[ $entry->system_class ][] = array('static_id' => (int)$entry->static_id, 'position' => (int)$entry->position);
		}

		$output['wormholes'] = $outWormholes;


		$siteTypes = DB::select("SELECT * FROM sites");

		foreach($siteTypes as $site)
		{
			$output['sites'][$site->id] = array('id' => (int)$site->id, 'name' => $site->name, 'type' => $site->type, 'description' => $site->description);
		}


		$extra = DB::select("SELECT s.id, scm.system_class, s.name, s.type FROM site_class_map scm
													LEFT JOIN sites s ON(s.id=scm.site_id)");

		foreach($extra as $site)
		{
			$output['maps'][ $site->type ][ $site->system_class ][] = $site->id;
		}

		$this->response->json($output);
	}
}
