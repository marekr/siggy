<?php

use Carbon\Carbon;

class CharacterLocation
{
	public $character_id;
	public $system_id;
	public $updated_at;

	public function __construct($props)
	{
		foreach ($props as $key => $value) 
		{
			$this->$key = $value;
		}
	}
	
	public static function find(int $id)
	{
		$data = DB::query(Database::SELECT, 'SELECT * FROM character_location WHERE character_id=:id')
												->param(':id', $id)
												->execute()
												->current();

		if($data != null)
		{
			return new CharacterLocation($data);
		}

		return null;
	}

	public static function findWithinCutoff(int $id, int $cutOffSeconds = 15)
	{
		$cutoff = Carbon::now()->subSeconds($cutOffSeconds)->toDateTimeString();
		$data = DB::query(Database::SELECT, 'SELECT * FROM character_location 
											WHERE character_id=:id
											AND updated_at >= :cutoff')
												->param(':id', $id)
												->param(':cutoff', $cutoff)
												->execute()
												->current();

		if($data != null)
		{
			return new CharacterLocation($data);
		}

		return null;
	}
}