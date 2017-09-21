<?php

namespace Siggy;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class DScanRecord extends Model {
	public $table = 'dscan_records';

	public $incrementing = true;
	public $timestamps = false;

	protected $fillable = [
		'dscan_id',
		'record_name',
		'item_distance',
		'type_id'
	];

	protected $hidden = [
		'dscan_id',
		'type'
	];

	public static function boot()
	{
		parent::boot();
	}

	public function dscan()
	{
		return $this->belongsTo('Siggy\DScan', 'dscan_id');
	}

	public function type()
	{
		return $this->belongsTo('Siggy\EveInvType', 'type_id','typeID');
	}
}