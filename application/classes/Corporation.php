<?php

use Pheal\Pheal;
use Carbon\Carbon;

class Corporation {

	public $id;
	public $name;
	public $ticker;
	public $description;
	public $member_count;
	public $created_at;
	public $updated_at;

	public function __construct(array $props)
	{
		foreach ($props as $key => $value) 
		{
    		$this->$key = $value;
		}
	}
	
	public function save(array $props)
	{
		foreach ($props as $key => $value) 
		{
    		$this->$key = $value;
		}

		DB::update('corporations')
			->set( $props )
			->where('id', '=',  $this->id)
			->execute();
	}

	public static function create(array $props): Corporation
	{
		DB::insert('corporations', array_keys($props) )
				->values(array_values($props))
				->execute();

		if(!isset($props['id']))
		{
			$props['id'] = $result[0];
		}

		return new Corporation($props);
	}

	public function syncWithApi()
	{
		$rawData = self::getAPICorpDetails($this->id);

		if( $rawData == null )
			return FALSE;

		$update = [ 'member_count' => $rawData['member_count'],
					'description' => $rawData['description'],
					'ticker' => $rawData['ticker'],
					'name' => $rawData['name'],
					'updated_at' => Carbon::now()->toDateTimeString()
				];

		$this->save($update);

		return TRUE;
	}

	public static function find(int $id)
	{
		$corp = DB::query(Database::SELECT, 'SELECT * FROM corporations WHERE id=:id')
												->param(':id', $id)
												->execute()
												->current();

		
		if($corp != null)
		{
			$res = new Corporation($corp);
			if( $corp['updated_at'] == null ||
				Carbon::parse($corp['updated_at'])->addMinutes(60) < Carbon::now() )
			{
				$res->syncWithApi();
			}

			return $res;
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
						'created_at' => Carbon::now()->toDateTimeString(),
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