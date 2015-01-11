<?php

use Carbon\Carbon;
require_once APPPATH.'classes/FrontController.php';

class Controller_Thera extends FrontController
{
	protected $output_array = array();

	public function before()
	{
		parent::before();
	}

	public function action_latest_exits()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		header('content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate");

		$cache = Cache::instance( CACHE_METHOD );

		$cache_name = 'thera-exits';

		if( ($exits = $cache->get( $cache_name, FALSE )) == FALSE )
		{
			$data = $this->fetch_thera_json();

			$exits = $this->build_exits_data($data);

			if( $exits != null )
			{
				$cache->set($cache_name, $exits, 300);
			}
		}

		print json_encode($exits);
		exit();
	}

	private function build_exits_data($data)
	{
		$wormholes = DB::query(Database::SELECT, 'SELECT id, name FROM statics')
								->execute()->as_array('name');

		$exits = array();
		foreach( $data as $rawExit )
		{
			$exit =  array( 'id' => $rawExit->id,
							'created_at' => Carbon::parse($rawExit->createdAt)->timestamp,
							'wormhole_type' => '',
							'out_signature' => $rawExit->signatureId,
							'in_signature' => $rawExit->wormholeDestinationSignatureId
						);


			$system = DB::query(Database::SELECT, 'SELECT s.id, s.name,
													s.sysClass as sys_class,
													s.sec as sec,
													s.region as region_id,
													r.regionName as region_name, s.sec
													FROM solarsystems  s
													INNER JOIN regions r ON(s.region=r.regionID)
													WHERE s.id=:system')
				->param(':system', $rawExit->wormholeDestinationSolarSystemId)
				->execute()
				->current();

			if( isset( $system['sys_class']) && $system['sys_class'] == 7 || $system['sys_class'] == 8|| $system['sys_class'] == 9 )
			{
				$hubJumps = DB::query(Database::SELECT, "SELECT ss.id as system_id, pr.num_jumps,ss.name as destination_name FROM precomputedroutes pr
														INNER JOIN solarsystems ss ON ss.id = pr.destination_system
														WHERE pr.origin_system=:system AND pr.destination_system != :system
														ORDER BY pr.num_jumps ASC")
					->param(':system', $system['id'])
					->execute()
					->as_array();

				$system['hub_jumps'] = $hubJumps;
			}

			$wormholeType = '';
			if( $rawExit->sourceWormholeType->id != 91 )
			{
				$wormholeType = $rawExit->sourceWormholeType->name;
			}
			else
			{
				$wormholeType = $rawExit->destinationWormholeType->name;
			}

			$exit['wormhole_type'] = (int)$wormholes[ $wormholeType ]['id'];

			$system['id'] = (int)$system['id'];
			$system['sys_class'] = (int)$system['sys_class'];
			$system['region_id'] = (int)$system['region_id'];
			$system['security'] = (float)$system['sec'];
			$exit['system'] = $system;


			$exits[$rawExit->id] = $exit;
		}

		return $exits;
	}

	private function fetch_thera_json()
	{
		$opts = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: en-US,en;q=0.8\r\n" .
				"User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.3\r\n" .
				"Cache-Control:max-age=0"
			)
		);

		$context = stream_context_create($opts);

		$data = file_get_contents('http://www.eve-scout.com/api/wormholes?limit=1000&offset=0&sort=wormholeEstimatedEol&order=asc', false, $context);

		if( !empty($data) )
		{
			$data = json_decode($data);
		}

		return $data;
	}
}
