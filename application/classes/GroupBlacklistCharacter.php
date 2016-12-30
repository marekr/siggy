<?php

use Carbon\Carbon;

class GroupBlacklistCharacter
{
	public $id;
	public $character_id;
	public $group_id;
	public $reason;
	public $created;

	public $character = null;

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

	public static function destroy(int $groupID, int $id)
	{
		DB::delete('group_character_blacklist')
			->where('id', '=', $id)
			->where('group_id','=', $groupID)
			->execute();
	}

	public function character()
	{
		if( $this->character == null || $this->character->id != $this->character_id )
		{
			$this->character = Character::find($this->character_id);
		}

		return $this->character;
	}

	public static function create(array $props): GroupBlacklistCharacter
	{
		$props['created_at'] = Carbon::now()->toDateTimeString();

		$result = DB::insert('group_character_blacklist', array_keys($props) )
				->values(array_values($props))
				->execute();

		$props['id'] = $result[0];
		return new GroupBlacklistCharacter($props);
	}

	public static function findByGroup(int $groupId): array
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
	
	public static function findByGroupAndChar(int $groupId, int $charId)
	{
		$data = DB::query(Database::SELECT, 'SELECT * FROM group_character_blacklist 
												WHERE character_id =:charID 
													AND group_id = :groupId')
												->param(':groupId', $groupId)
												->param(":charID", $charId)
												->execute()
												->as_array();

		if($data != null)
		{
			return new GroupBlacklistCharacter($data);
		}

		return null;
	}
}