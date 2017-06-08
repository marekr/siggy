<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CharacterLocationHistory extends Model
{
	public $timestamps = false;
	public $table = 'character_location_history';
	public $dates = ['changed_at'];

	public static function findAllByCharacterId(int $charId): array
	{
		return self::where('character_id', $charId)
			->get()
			->all();	
	}

	public static function findNewerThan(int $charId, Carbon $threshold): array
	{
		return self::where('character_id', $charId)
			->where('changed_at','>',$threshold->toDateTimeString())
			->get()
			->all();	
	}
}