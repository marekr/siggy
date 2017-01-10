<?php

use Illuminate\Database\Capsule\Manager as DB;

class Controller_Data extends FrontController {

	public function before()
    {
		if( Kohana::$environment == Kohana::PRODUCTION )
		{
			header('content-type: application/json');
			ob_start( 'ob_gzhandler' );
		}
	}

	public function action_systems()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

        $systems = DB::select("SELECT ss.id, ss.name, r.regionName as region_name, ss.sec, ss.sysClass as class
													FROM solarsystems ss
													LEFT JOIN regions r ON(ss.region = r.regionID)");

		$this->response->body(json_encode($systems,JSON_NUMERIC_CHECK));
	}

	public function action_sig_types()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

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

		$this->response->body(json_encode($output));
	}
}
