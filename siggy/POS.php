<?php

namespace Siggy;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class POS extends Model {
	public $table = 'poses';
	public $timestamps = false;

	protected $fillable = [
			'location_planet',
			'location_moon',
			'owner',
			'type_id',
			'online',
			'size',
			'notes',
			'group_id',
			'added_date',
			'system_id',
	];

	protected $hidden = [
		'group_id'
	];

	public function system()
	{
		return $this->belongsTo('System', 'system_id');
	}

	public static function findWithSystemByGroup(int $groupId, int $id)
	{
		return self::with('system')
			->where('group_id',$groupId)
			->where('id',$id)
			->first();
	}
}