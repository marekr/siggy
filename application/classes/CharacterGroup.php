<?php

use Carbon\Carbon;
use Pheal\Pheal;

class CharacterGroup {
	public $character_id;
	public $group_id;

	public $group = null;
	public $character = null;

	public function __construct($props = [])
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

		DB::update('character_group')
			->set( $props )
			->where('character_id', '=',  $this->character_id)
			->where('group_id', '=',  $this->group_id)
			->execute();
	}

	public function updateGroupAccess()
	{
		$this->save(['last_group_access' => Carbon::now()->toDateTimeString()]);
	}

	public static function create($props)
	{
		$result = DB::insert('character_group', array_keys($props) )
				->values(array_values($props))
				->execute();

		$props['id'] = $result[0];
		return new GroupMember($props);
	}

	public static function find(int $character, int $group)
	{
		$data = DB::query(Database::SELECT, 'SELECT * FROM character_group 
												WHERE character_id=:id AND group_id=:group_id')
												->param(':id', $character)
												->param(':group_id', $group)
												->execute()
												->current();

		if($data != null)
		{
			return new CharacterGroup($data);
		}

		return null;
	}
}