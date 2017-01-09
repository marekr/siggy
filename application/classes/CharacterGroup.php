<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;
use Mpociot\HasCompositeKey\HasCompositeKey;

class CharacterGroup extends Model {
    use HasCompositeKey;

	public $timestamps = false;
	public $table = 'character_group';
	public $dates = ['last_group_access_at'];
	protected $primaryKey = ['character_id','group_id'];

	public function updateGroupAccess()
	{
		$this->last_group_access_at = Carbon::now()->toDateTimeString();
		$this->save();
	}

	public static function find(int $character, int $group)
	{
		return self::where('character_id', $character)
					->where('group_id', $group)
					->first();
	}
}