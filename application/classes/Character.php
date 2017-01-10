<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;
use Pheal\Pheal;

class Character extends Model {
	
	public $timestamps = true;
	public $incrementing = false;

	protected $fillable = [
		'id',
		'corporation_id',
		'name',
		'location_processed_at'
	];
	
	public $dates = ['location_processed_at'];

	public function canAccessMap(int $groupId, int $chainmap): bool
	{
		$access = DB::selectOne('SELECT * FROM groupmembers gm 
														JOIN chainmaps_access ca ON(gm.id=ca.groupmember_id)
														WHERE gm.groupID=:groupID AND ca.chainmap_id = :chainmap
														AND ((gm.memberType="corp" AND gm.eveID=:corpID) 
														OR (gm.memberType="char" AND gm.eveID=:charID))',
														[
															'groupID' => $groupId,
															'chainmap' => $chainmap,
															'corpID' => $this->corporation_id,
															'charID' => $this->id,
														]);

		if($access != null)
		{
			return true;
		}

		return false;
	}

	public function corporation()
	{
		return $this->belongsTo('Corporation');
	}

	public static function find(int $id, bool $avoidAPIFetch = false)
	{
		$char = self::where('id', $id)->first();

		if($char != null)
		{
			if( ($char->updated_at != null &&
				$char->updated_at->addMinutes(60) > Carbon::now()) ||
				$avoidAPIFetch )
			{
				return $char;
			}
			else 
			{
				$rawData = self::getAPICharacterAffiliation($id);

				if( $rawData == null )
					return null;

				$update = [ 'name' => $rawData['character_name'],
							'corporation_id' => $rawData['corporation_id']
						];

				$char->fill($update);
				$char->save();

				return $char;
			}
		}
		else 
		{
			$rawData = self::getAPICharacterAffiliation($id);

			if( $rawData == null )
				return null;

			$insert = [ 'id' => $id,
						'name' => $rawData['character_name'],
						'corporation_id' => $rawData['corporation_id'],
						'updated_at' => Carbon::now()->toDateTimeString()
					];

			$res = self::create($insert);
			
			return $res;
		}
	}

	public static function getAPICharacterAffiliation(int $id)
	{
		//recheck
		PhealHelper::configure();
		$pheal = new Pheal( null, null, 'eve' );

		try {
			$results = $pheal->eveScope->CharacterAffiliation(array('ids' => $id));
			if (array_key_exists(0, $results->characters)) {
				$charData = $results->characters[0];
				if (count($charData) > 0) {
					if ($id == $charData['characterID']) {
						return [
							'corporation_id' => $charData['corporationID'],
							'corporation_name' => $charData['corporationName'],
							'character_name' => $charData['characterName']
						];
					}
				}
			}
		}
		catch(Exception $e) {
			return null;
		}

		return null;
	}
}