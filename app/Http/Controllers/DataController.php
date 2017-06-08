<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Siggy\StructureType;
use Siggy\POSType;

class DataController extends Controller {

	public function systems()
	{
		$systems = DB::select("SELECT ss.id, ss.name, r.regionName as region_name, ss.sec, ss.sysClass as class
													FROM solarsystems ss
													LEFT JOIN eve_map_regions r ON(ss.region = r.regionID)");

		return response()->json($systems);
	}

	public function structures()
	{
		$structures = StructureType::all()->keyBy('id');

		return response()->json($structures);
	}

	public function poses()
	{
		$poses = POSType::all()->keyBy('id');

		return response()->json($poses);
	}

	public function sigTypes()
	{
		$output = [];
		$wormholeTypes = DB::select("SELECT * FROM statics");

		$types = [];
		foreach($wormholeTypes as &$row)
		{
			$types[ $row->id ] = $row;
		}
		$output['wormhole_types'] = $types;

		$whStaticMap = DB::select("SELECT * FROM wormhole_class_map
												ORDER BY position ASC");

		$outWormholes = [];
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

		return response()->json($output);
	}
}
