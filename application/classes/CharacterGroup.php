<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Mpociot\HasCompositeKey\HasCompositeKey;

class CharacterGroup extends Model {
    use HasCompositeKey;

	public $timestamps = false;
	public $table = 'character_group';
	public $dates = ['last_group_access_at'];
	protected $primaryKey = ['character_id','group_id'];

	protected $fillable = [
		'character_id',
		'group_id',
		'last_group_access_at'
	];

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