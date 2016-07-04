<?php

use Carbon\Carbon;
use Pheal\Pheal;

class Character {
	
	public $id;
	public $corporation_id;
	public $created_at;
	public $updated_at;
	public $name;

	public $corporation = null;

	public function __construct($props)
	{
		foreach ($props as $key => $value) 
		{
    		$this->$key = $value;
		}
	}
	
	public function save($props)
	{
		foreach ($props as $key => $value) 
		{
    		$this->$key = $value;
		}

		DB::update('characters')
			->set( $props )
			->where('id', '=',  $this->id)
			->execute();
	}

	public static function create($props)
	{
		DB::insert('characters', array_keys($props) )
				->values(array_values($props))
				->execute();

		return new Character($props);
	}

	public function corporation()
	{
		if( $this->corporation == null || $this->corporation->id != $this->corporation_id )
		{
			$this->corporation = Corporation::find($this->corporation_id);
		}

		return $this->corporation;
	}

	public static function find(int $id)
	{
		$char = DB::query(Database::SELECT, 'SELECT * FROM characters WHERE id=:id')
												->param(':id', $id)
												->execute()
												->current();

		if($char != null)
		{
			if( $char['updated_at'] != null &&
				Carbon::parse($char['updated_at'])->addMinutes(60) > Carbon::now() )
			{
				return new Character($char);
			}
			else 
			{
				$rawData = self::getAPICharacterAffiliation($id);

				if( $rawData == null )
					return null;

				$res = new Character($char);
				
				$update = [ 'name' => $rawData['character_name'],
							'corporation_id' => $rawData['corporation_id'],
							'updated_at' => Carbon::now()->toDateTimeString()
						];
				$res->save($update);

				return $res;
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
						'created_at' => Carbon::now()->toDateTimeString(),
						'updated_at' => Carbon::now()->toDateTimeString()
					];

			$res = self::create($insert);
			
			return $res;
		}

		return null;
	}

	public static function getAPICharacterAffiliation(int $id)
	{
		//recheck
		PhealHelper::configure();
		$pheal = new Pheal( null, null, 'eve' );

		$results = $pheal->eveScope->CharacterAffiliation(array('ids' => $id));
		if(array_key_exists(0, $results->characters))
		{
			$charData = $results->characters[0];
			if( count($charData) > 0 )
			{
				if( $id == $charData['characterID'] )
				{
					return [
							'corporation_id' => $charData['corporationID'],
							'corporation_name' => $charData['corporationName'],
							'character_name' => $charData['characterName']
							];
				}
			}
		}

		return null;
	}
}