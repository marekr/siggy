<?php

namespace Siggy;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class DScan extends Model {
	public $table = 'dscan';

	public $primaryKey = 'id';
	public $incrementing = false;
	public $timestamps = true;

	protected $fillable = [
		'id',
		'group_id',
		'system_id',
		'title',
		'added_by',
		'title',
		'created_at',
		'updated_at'
	];

	protected $hidden = [
		'group_id'
	];

	public static function boot()
	{
		parent::boot();

		self::creating(function($model){
			$model->id = Str::random(14);
		});
	}

	public function system()
	{
		return $this->belongsTo('Siggy\System', 'system_id');
	}

	public function records()
	{
		return $this->hasMany('Siggy\DScanRecord','dscan_id','id');
	}

	public static function findByGroup(int $groupId, string $id)
	{
		return self::where('group_id',$groupId)
			->where('id',$id)
			->first();
	}

	public static function getAllByGroupAndSystem(int $groupId, int $systemId)
	{
		return self::where('group_id',$groupId)
			->where('system_id',$systemId)
			->get();
	}

	public static function findWithSystemByGroup(int $groupId, string $id)
	{
		return self::with('system')
			->where('group_id',$groupId)
			->where('id',$id)
			->first();
	}
}