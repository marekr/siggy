<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;
use Pheal\Pheal;

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
	];

	public function syncWithApi()
	{
		$rawData = self::getAPICorpDetails($this->id);

		if( $rawData == null )
			return FALSE;

		$update = [ 'member_count' => $rawData['member_count'],
					'description' => $rawData['description'],
					'ticker' => $rawData['ticker'],
					'name' => $rawData['name']
				];

		$this->fill($update);
		$this->save();

		return TRUE;
	}

	public static function find(int $id)
	{
		$corp = self::where('id', $id)->first();

		if($corp != null)
		{
			if( $corp->updated_at == null ||
				$corp->updated_at->addMinutes(60) < Carbon::now() )
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
						'description' => $rawData['description'],
						'ticker' => $rawData['ticker'],
						'updated_at' => Carbon::now()->toDateTimeString()
					];

			$res = self::create($insert);
			
			return $res;
		}
	}

	
	static function getAPICorpDetails( int $id )
	{
		$details = null;

		PhealHelper::configure();
		$pheal = new Pheal(null,null,'corp');

		try {
			$result = $pheal->CorporationSheet(array('corporationID' => $id))->toArray();
			//print 'found corp, storing locally!';
			$result = $result['result'];

			$details = ['member_count' => (int)$result['memberCount'],
				'name' => $result['corporationName'],
				'description' => mb_convert_encoding($result['description'], "ASCII"),
				'ticker' => $result['ticker'],
				'alliance_id' => (int)$result['allianceID']
			];
		}
		catch(Exception $e) {
			return null;
		}

		return $details;
	}
}