<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;

class Corporation extends Model {

	public $timestamps = true;
	public $incrementing = false;

	protected $fillable = [
		'id',
		'name',
		'ticker',
		'description',
		'member_count',
		'updated_at',
		'last_sync_attempt_at',
		'last_sync_successful_at'
	];

	public $dates = ['last_sync_attempt_at','last_sync_successful_at'];

	public function syncWithApi()
	{
		$update = [];
		$rawData = self::getAPICorpDetails($this->id);
		if( $rawData != null )
		{
			$update = [ 'member_count' => $rawData['member_count'],
					//todo, fix me once ESI adds it back :/
					//	'description' => $rawData['description'],
						'ticker' => $rawData['ticker'],
						'name' => $rawData['name'],
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

		$this->fill($update);
		$this->save();

		return;
	}

	public static function find(int $id): ?Corporation
	{
		$corp = self::where('id', $id)->first();
		if($corp != null)
		{
			if( $corp->last_sync_attempt_at == null ||
				$corp->last_sync_attempt_at->addMinutes(90) < Carbon::now() )
			{
				$corp->syncWithApi();
			}

			return $corp;
		}
		else
		{
			$rawData = self::getAPICorpDetails($id);

			if( $rawData == null )
				return null;

			$insert = [ 'id' => $id,
						'name' => $rawData['name'],
						'member_count' => $rawData['member_count'],
						//todo, fix me once ESI adds it back :/
						//'description' => $rawData['description'],
						'ticker' => $rawData['ticker'],
						'updated_at' => Carbon::now()->toDateTimeString(),
						'last_sync_attempt_at' =>  Carbon::now()->toDateTimeString(),
						'last_sync_successful_at' => Carbon::now()->toDateTimeString()
					];

			$res = self::create($insert);
			
			return $res;
		}
	}

	
	static function getAPICorpDetails( int $id ): ?array
	{
		$details = null;

		ESIHelper::configure();

		$api_instance = new ESI\Api\CorporationApi();
		$datasource = "tranquility"; // string | The server name you would like data from

		try {
			$result = $api_instance->getCorporationsCorporationId($id, $datasource);
			
			$details = ['member_count' => (int)$result['member_count'],
				'name' => $result['corporation_name'],
				'ticker' => $result['ticker'],
				'alliance_id' => (int)$result['alliance_id'],
				//todo, add back description
			];
		} catch (Exception $e) {
			return null;
		}

		return $details;
	}
	
	static function searchEVEAPI( string $name ): ?array
	{
		$results = [];

		$api_instance = new ESI\Api\SearchApi();

		$categories = ['corporation'];
		
		$language = "en-us"; // string | Search locale
		$strict = false; // bool | Whether the search should be a strict match
		$datasource = "tranquility"; // string | The server name you would like data from

		try {
			$result = $api_instance->getSearch($name, $categories, $language, $strict, $datasource);
			if(isset($result['corporation']))
			{
				foreach($result['corporation'] as $id)
				{
					$corp = Corporation::find($id);
					if($corp != null)
					{
						$results[$id] = $corp;
					}
				}
			}
		} catch (Exception $e) {
			return null;
		}

		return $results;
	}
}