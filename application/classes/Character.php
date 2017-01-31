<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;

class Character extends Model {
	
	public $timestamps = true;
	public $incrementing = false;

	protected $fillable = [
		'id',
		'corporation_id',
		'name',
		'location_processed_at',
		'last_sync_attempt_at',
		'last_sync_successful_at'
	];
	
	public $dates = ['location_processed_at','last_sync_attempt_at','last_sync_successful_at'];

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

	public static function find(int $id, bool $avoidAPIFetch = false): ?Character
	{
		$char = self::where('id', $id)->first();

		if($char != null)
		{
			if( ($char->last_sync_attempt_at == null ||
				$char->last_sync_attempt_at->addMinutes(60) < Carbon::now()) &&
				!$avoidAPIFetch )
			{
				$rawData = self::getAPICharacterAffiliation($id);
				$update = [];
				if( $rawData != null )
				{
					$update = [ 
								'name' => $rawData['character_name'],
								'corporation_id' => $rawData['corporation_id'],
								'last_sync_successful_at' => Carbon::now()->toDateTimeString(),
								'last_sync_attempt_at' => Carbon::now()->toDateTimeString()
							];

				}
				else 
				{
					$update = [ 
								'last_sync_attempt_at' => Carbon::now()->toDateTimeString()
							];
				}

				$char->fill($update);
				$char->save();
			}

			return $char;
		}
		else 
		{
			$rawData = self::getAPICharacterAffiliation($id);

			if( $rawData == null )
				return null;

			$insert = [ 'id' => $id,
						'name' => $rawData['character_name'],
						'corporation_id' => $rawData['corporation_id'],
						'updated_at' => Carbon::now()->toDateTimeString(),
						'last_sync_attempt_at' =>  Carbon::now()->toDateTimeString(),
						'last_sync_successful_at' => Carbon::now()->toDateTimeString()
					];

			$res = self::create($insert);
			
			return $res;
		}
	}

	public static function getAPICharacterAffiliation(int $id): ?array
	{
		$details = [];
		$api_instance = new ESI\Api\CharacterApi();
		$datasource = "tranquility"; // string | The server name you would like data from

		try {
			$result = $api_instance->getCharactersCharacterId($id, $datasource);
			$details = [
				'corporation_id' => $result['corporation_id'],
				'character_name' => $result['name']
			];
		} catch (Exception $e) {
			return null;
		}

		return $details;
	}
	
	static function searchEVEAPI(string $name, bool $strict = false): ?array
	{
		$results = [];

		$api_instance = new ESI\Api\SearchApi();

		$categories = ['character'];
		
		$language = "en-us"; // string | Search locale
		$datasource = "tranquility"; // string | The server name you would like data from

		try {
			$result = $api_instance->getSearch($name, $categories, $language, $strict, $datasource);
			
			if(isset($result['character']))
			{
				foreach($result['character'] as $id)
				{
					$char = Character::find($id);
					if($char != null)
					{
						$results[$id] = $char;
					}
				}
			}
		} catch (Exception $e) {
			return null;
		}

		return $results;
	}
}