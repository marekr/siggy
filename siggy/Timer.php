<?php

namespace Siggy;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Timer extends Model {
	public $table = 'timers';

	public $incrementing = true;
	public $timestamps = true;

	protected $fillable = [
		'planet',
		'moon',
		'owner',
		'system_id',
		'group_id',
		'start_at',
		'end_at',
		'type',
		'notes'
	];

	protected $hidden = [
		'group_id'
	];

	protected $dates = [
		'start_at',
		'end_at'
	];

	
	public static function findAllByGroupOrdered(int $groupId): array
	{
		return self::where('group_id',$groupId)
				->orderBy('end_at','desc')
				->get()
				->all();
	}
}