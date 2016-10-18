<?php

use Carbon\Carbon;

class CharacterLocationHistory
{
	public $character_id;
	public $current_system_id;
	public $previous_system_id;
	public $changed_at;

	public function __construct($props)
	{
		foreach ($props as $key => $value) 
		{
			$this->$key = $value;
		}
	}
	
	public static function find(int $id): array
	{
		$data = DB::query(Database::SELECT, 'SELECT * FROM character_location_history WHERE character_id=:id')
												->param(':id', $id)
												->execute()
												->as_array();

		$results = [];
		if($data != null)
		{
			foreach($data as $item)
			{
				$results[] = new CharacterLocationHistory($item);
			}
		}

		return null;
	}

	public static function findNewerThan(int $id, Carbon $threshold): array
	{
		$data = DB::query(Database::SELECT, 'SELECT * FROM character_location_history 
											WHERE character_id=:id
											AND changed_at >= :threshold')
												->param(':id', $id)
												->param(':threshold', $threshold->toDateTimeString())
												->execute()
												->as_array();

		$results = [];
		if($data != null)
		{
			foreach($data as $item)
			{
				$results[] = new CharacterLocationHistory($item);
			}
		}

		return $results;
	}
}