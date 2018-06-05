<?php

namespace Siggy;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class BillingCharge extends Model {
	public $table = 'billing_charges';

	public $incrementing = true;
	public $timestamps = false;

	protected $fillable = [
		'message',
		'amount',
		'charged_at',
		'member_count',
		'group_id'
	];

	protected $hidden = [
		'group_id'
	];

	protected $dates = [
		'charged_at'
	];

	
	public static function findAllByGroupOrdered(int $groupId, int $limit = 5): array
	{
		return self::where('group_id',$groupId)
				->orderBy('charged_at','desc')
				->take($limit)
				->get()
				->all();
	}
}