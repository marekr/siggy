<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
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
		$systemData = DB::select(Database::SELECT, "SELECT
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
													INNER JOIN systemeffects se ON ss.effect = se.id
													INNER JOIN regions r ON ss.region = r.regionID
													INNER JOIN constellations c ON ss.constellation = c.constellationID
													WHERE ss.id=:id")
									->param(':id', $id )
									->execute()
									->current();

		if( !$systemData['id'] )
		{
			return FALSE;
		}


		$systemData['radius'] = (float)$systemData['radius'];
		$systemData['true_sec'] = (float)$systemData['true_sec'];
		$systemData['sec'] = (float)$systemData['sec'];
		$systemData['system_class'] = (int)$systemData['system_class'];
		$systemData['planets'] = (int)$systemData['planets'];
		$systemData['moons'] = (int)$systemData['moons'];
		$systemData['belts'] = (int)$systemData['belts'];
		$systemData['constellation_id'] = (int)$systemData['constellation_id'];
		$systemData['region_id'] = (int)$systemData['region_id'];
		$systemData['effect_id'] = (int)$systemData['effect_id'];
		$systemData['id'] = (int)$systemData['id'];

		if( $mode != 'basic' )
		{
			$systemData['statics'] = array();

			$staticData = DB::query(Database::SELECT, "SELECT sm.static_id as id FROM staticmap sm
														WHERE sm.system_id=:id")
										->param(':id', $systemData['id'])
										->execute()
										->as_array();

			if( count( $staticData ) > 0 )
			{
				$systemData['statics'] = $staticData;
			}

			$end = miscUtils::getHourStamp();
			$start = miscUtils::getHourStamp(-24);
			$apiData = DB::query(Database::SELECT, "SELECT hourStamp, jumps, kills, npcKills FROM apihourlymapdata WHERE systemID=:system AND hourStamp >= :start AND hourStamp <= :end ORDER BY hourStamp asc LIMIT 0,24")
										->param(':system', $systemData['id'])
										->param(':start', $start)
										->param(':end', $end)
										->execute()
										->as_array('hourStamp');

			$trackedJumps = DB::query(Database::SELECT, "SELECT hourStamp, jumps FROM jumpstracker WHERE systemID=:system AND groupID=:group AND hourStamp >= :start AND hourStamp <= :end ORDER BY hourStamp asc LIMIT 0,24")
										->param(':system', $systemData['id'])
										->param(':group', $groupID)
										->param(':start', $start)
										->param(':end', $end)
										->execute()->as_array('hourStamp');

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
