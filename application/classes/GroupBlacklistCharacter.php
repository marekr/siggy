<?php

use Carbon\Carbon;

class GroupBlacklistCharacter
{
	public $id;
	public $character_id;
	public $group_id;
	public $reason;
	public $created;

	public function __construct($props)
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

		DB::update('group_character_blacklist')
			->set( $props )
			->where('id', '=',  $this->id)
			->execute();
	}

	public static function create(array $props)
	{
		$props['created_at'] = Carbon::now()->toDateTimeString();

		DB::insert('group_character_blacklist', array_keys($props) )
				->values(array_values($props))
				->execute();

		return new GroupBlacklistCharacter($props);
	}

	public static function findByGroup(int $groupId)
	{
		$data = DB::query(Database::SELECT, 'SELECT * FROM group_character_blacklist WHERE group_id=:id')
												->param(':id', $groupId)
												->execute()
												->as_array();

		$results = [];
		if($data != null)
		{
			foreach($data as $item)
			{
				$results[] = new GroupBlacklistCharacter($item);
			}
		}

		return $results;
	}
}