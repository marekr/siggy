<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CharacterLocation extends Model
{
	public $timestamps = false;
	public $table = 'character_location';
	public $dates = ['updated_at'];
	protected $primaryKey = 'character_id';

	public static function findWithinCutoff(int $id, int $cutOffSeconds = 15)
	{
		$cutoff = Carbon::now()->subSeconds($cutOffSeconds)->toDateTimeString();

		return self::where('character_id', $id)
			->where('updated_at','>=',$cutoff)
			->first();
	}
}