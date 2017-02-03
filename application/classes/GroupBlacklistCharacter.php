<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;

class GroupBlacklistCharacter extends Model
{
	public $timestamps = true;
	public $dates = ['created_at'];
	public $table = 'group_character_blacklist';

	protected $fillable = [
		'group_id',
		'character_id',
		'reason'
	];
	
	public $character = null;

	public function setUpdatedAt($value)
	{
		//disable timestamp
	}

	public function character()
	{
		if( $this->character == null || $this->character->id != $this->character_id )
		{
			$this->character = Character::find($this->character_id);
		}

		return $this->character;
	}

	public static function findAllByGroup(int $groupId): ?array
	{
		return self::where('group_id', $groupId)
			->get()
			->keyBy('character_id')
			->all();
	}

	public static function findByGroup(int $groupId, int $id): ?GroupBlacklistCharacter
	{
		return self::where('group_id', $groupId)
			->where('id', $id)
			->first();
	}
	
	public static function findByGroupAndChar(int $groupId, int $characterId): ?GroupBlacklistCharacter
	{
		return self::where('group_id', $groupId)
			->where('character_id', $characterId)
			->first();
	}
}