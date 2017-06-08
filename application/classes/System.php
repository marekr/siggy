<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class System extends Model {
	public $table = 'solarsystems';
	public $timestamps = false;
	public $incrementing = false;

	protected $fillable =  [
			'id',
			'name',
			'belts',
			'planets',
			'moons',
			'sysClass',
			'truesec',
			'sec',
			'effect',
			'radius',
			'region',
			'constellation'
		];

	public static function findByName(string $name): ?System
	{
		return self::where('name',$name)->first();
	}


	public static function get($id, $groupID, $mode = 'advanced')
	{
		$systemData = collect(DB::selectOne("SELECT
			ss.id,
			ss.name,
			ss.belts,
			ss.planets,
			ss.moons,
			ss.sysClass as system_class,
			ss.sec,
			ss.truesec as true_sec,
			ss.radius,
			ss.constellation as constellation_id,
			ss.region as region_id,
			ss.effect as effect_id,
			se.effectTitle as effect_title,
			r.regionName as region_name,
			c.constellationName as constellation_name
													FROM solarsystems ss
													LEFT JOIN systemeffects se ON ss.effect = se.id
													INNER JOIN eve_map_regions r ON ss.region = r.regionID
													INNER JOIN eve_map_constellations c ON ss.constellation = c.constellationID
													WHERE ss.id=?", [$id]))->toArray();

		if( !isset($systemData['id']) )
		{
			return FALSE;
		}

		if( $mode != 'basic' )
		{
			$systemData['statics'] = array();

			$staticData = DB::select("SELECT sm.static_id as id FROM staticmap sm
														WHERE sm.system_id=?", [$systemData['id']]);

			if( count( $staticData ) > 0 )
			{
				$systemData['statics'] = $staticData;
			}

			$end = miscUtils::getHourStamp();
			$start = miscUtils::getHourStamp(-24);
			$apiData = collect(DB::select("SELECT hourStamp, jumps, kills, npcKills 
													FROM apihourlymapdata 
													WHERE systemID=? AND hourStamp >= ?
													AND hourStamp <= ?
													ORDER BY hourStamp asc LIMIT 0,24", [$systemData['id'], $start, $end]))->keyBy('hourStamp')->all();

			$trackedJumps = collect(DB::select("SELECT hourStamp, jumps FROM jumpstracker 
			WHERE systemID=?
			AND groupID=? AND hourStamp >= ? 
			AND hourStamp <= ?
			ORDER BY hourStamp asc 
			LIMIT 0,24",[$systemData['id'], $groupID, $start, $end]))->keyBy('hourStamp')->all();
		
			$systemData['stats'] = array();
			for($i = 23; $i >= 0; $i--)
			{
				$hourStamp = miscUtils::getHourStamp($i*-1);
				$apiJumps = ( isset($apiData[ $hourStamp ]) ? $apiData[ $hourStamp ]['jumps'] : 0);
				$apiKills = ( isset($apiData[ $hourStamp ]) ? $apiData[ $hourStamp ]['kills'] : 0);
				$apiNPC = ( isset($apiData[ $hourStamp ]) ? $apiData[ $hourStamp ]['npcKills'] : 0);
				$siggyJumps = ( isset($trackedJumps[ $hourStamp ]) ? $trackedJumps[ $hourStamp ]['jumps'] : 0);
				$systemData['stats'][] = array( $hourStamp*1000, $apiJumps, $apiKills, $apiNPC, $siggyJumps);
			}
		}
	//	$systemData['poses'] = $this->getPOSes( $systemData['id'] );
	//	$systemData['dscans'] = $this->getDScans( $systemData['id'] );

		return $systemData;
	}
}
