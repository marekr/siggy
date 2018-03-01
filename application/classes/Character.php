<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

use Siggy\ESI\Client as ESIClient;

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

			if($char->corporation == null)
			{
				$char->corporation = Corporation::find($char->corporation_id);
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
			
			if($res->corporation == null)
			{
				$res->corporation = Corporation::find($res->corporation_id);
			}

			return $res;
		}
	}

	public static function getAPICharacterAffiliation(int $id): ?array
	{
		$details = null;

		$client = new ESIClient();
		$result = $client->getCharacterInformationV4($id);

		if($result != null)
		{
			$details = [
				'corporation_id' => $result->corporation_id,
				'character_name' => $result->name
			];
		}

		return $details;
	}
	
	static function searchEVEAPI(string $name, bool $strict = false): ?array
	{
		$results = [];

		$client = new ESIClient();
		$result = $client->getSearchV2($name, ['character'], 'en-us', $strict);

		if($result != null)
		{	
			if(property_exists($result,'character'))
			{
				foreach($result->character as $id)
				{
					$char = Character::find($id);
					if($char != null)
					{
						$results[$id] = $char;
					}
				}
			}
		}

		return $results;
	}
}