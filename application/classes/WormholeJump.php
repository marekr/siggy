<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class WormholeJump extends Model {
	public $table = 'wormhole_jumps';
	public $timestamps = true;
    const CREATED_AT = 'jumped_at';

	protected $fillable = [
			'wormhole_hash',
			'group_id',
			'ship_id',
			'character_id',
			'origin_id',
			'destination_id',
			'jumped_at'
	];

	public function setUpdatedAt($value) {
		//disable updated_At
	}

	public function character()
	{
		return $this->belongsTo('Character');
	}
	
	public function ship()
	{
		return $this->belongsTo('Ship');
	}
	
	public function origin()
	{
		return $this->belongsTo('System', 'origin_id');
	}

	public function destination()
	{
		return $this->belongsTo('System', 'destination_id');
	}

	public static function findWithSystemByGroup(int $groupId, int $id)
	{
		return self::with('system')
			->where('group_id',$groupId)
			->where('id',$id)
			->first();
	}
}