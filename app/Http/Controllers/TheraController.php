<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\Facades\Auth;
use App\Facades\SiggySession;
use Siggy\CharacterLocation;
use \Pathfinder;
use Siggy\Chainmap;
use Siggy\StandardResponse;

class TheraController extends Controller {


	public function latest_exits()
	{
		$cache_name = 'thera-exits';

		if( ($exits = Cache::get( $cache_name, FALSE )) == FALSE )
		{
			$data = $this->fetch_thera_json();

			$exits = $this->build_exits_data($data);

			if( $exits != null )
			{
				Cache::put($cache_name, $exits, 300);
			}
		}

		$currentLocation = CharacterLocation::findWithinCutoff(SiggySession::getCharacterId());
		if( $currentLocation != null )
		{
			$pather = new Pathfinder();

			$targetID = $currentLocation->system_id;

			if( $targetID < 31000000 )
			{
				foreach($exits as &$exit)
				{
					if( isset($exit['system']->id) && $exit['system']->id < 31000000 )
					{
						$path = $pather->shortest($targetID, $exit['system']->id);
						$exit['jumps'] = $path['distance'];
					}
				}
			}
		}

		return response()->json($exits);
	}


	private function build_exits_data($data)
	{
		$wormholes = DB::table('statics')
						->get()
						->keyBy('name')
						->all();

		$exits = array();
		foreach( $data as $rawExit )
		{
			$exit =  array( 'id' => $rawExit->id,
							'created_at' => Carbon::parse($rawExit->createdAt)->timestamp,
							'wormhole_type' => '',
							'out_signature' => $rawExit->signatureId,
							'in_signature' => $rawExit->wormholeDestinationSignatureId
						);


			//skip thera to thera wormholes....yea thanks guys
			if( (int)$rawExit->wormholeDestinationSolarSystemId == 31000005 )
				continue;


			$system = DB::selectOne('SELECT s.id, s.name,
													s.sysClass as sys_class,
													s.sec as sec,
													s.region as region_id,
													r.regionName as region_name, s.sec
													FROM solarsystems  s
													INNER JOIN eve_map_regions r ON(s.region=r.regionID)
													WHERE s.id=?',[$rawExit->wormholeDestinationSolarSystemId]);

			if( isset( $system->sys_class) && $system->sys_class == 7 || $system->sys_class == 8|| $system->sys_class == 9 )
			{
				$hubJumps = DB::select("SELECT ss.id as system_id, pr.num_jumps,ss.name as destination_name FROM precomputedroutes pr
														INNER JOIN solarsystems ss ON ss.id = pr.destination_system
														WHERE pr.origin_system=? AND pr.destination_system != ?
														ORDER BY pr.num_jumps ASC",[$system->id,$system->id]);

				$system->hub_jumps = $hubJumps;
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

			$exit['wormhole_type'] = (int)$wormholes[ $wormholeType ]->id;

			$system->security = (float)$system->sec;
			$exit['system'] = $system;
			$exit['jumps'] = "-";	//placeholder for real value

			$exits[$rawExit->id] = $exit;
		}

		return $exits;
	}
	
	public function import_to_chainmap()
	{
		$postData = json_decode($request->getContent(), true);
		$cache_name = 'thera-exits';

		if( ($exits = Cache::get( $cache_name, FALSE )) == FALSE )
		{
			$data = $this->fetch_thera_json();

			$exits = $this->build_exits_data($data);

			if( $exits != null )
			{
				Cache::put($cache_name, $exits, 300);
			}
		}

		$chainmap = null;
		if( isset($postData['chainmap']) )
		{
			$chainmap = Chainmap::find(intval($postData['chainmap']),SiggySession::getGroup()->id);
		}

		if( $chainmap == null )
		{
			return response()->json(StandardResponse::error('Chain map not found'), 400);
		}

		if( count($exits) > 0 )
		{
			if( isset($postData['clean']) && intval($postData['clean']) == 1 )
			{
				$chainmap->delete_all_system_connections(31000005);
			}

			foreach( $exits as $exit )
			{
				if( isset($exit['system']->id) )
				{
					try
					{
						$chainmap->add_system_to_map(31000005, $exit['system']->id);
					}
					catch (Exception $e)
					{
					}
				}
			}
		}

		return response()->json(StandardResponse::ok(count($exits)));
	}

	private function fetch_thera_json()
	{
		$opts = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: en-US,en;q=0.8\r\n" .
				"User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2171.95 Safari/537.3\r\n" .
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
