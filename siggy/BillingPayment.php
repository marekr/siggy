<?php

namespace Siggy;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

use \Character;
use \Corporation;

class BillingPayment extends Model {
	public $table = 'billing_payments';

	public $incrementing = true;
	public $timestamps = false;

	protected $fillable = [
		'ref_id',
		'amount',
		'payer_character_id',
		'payer_corporation_id',
		'paid_at',
		'processed_at',
		'group_id'
	];

	protected $hidden = [
		'group_id'
	];

	protected $dates = [
		'paid_at',
		'processed_at'
	];

	protected $appends = ['payer_name'];

	
	public static function findAllByGroupOrdered(int $groupId): array
	{
		return self::where('group_id',$groupId)
				->orderBy('paid_at','desc')
				->get()
				->all();
	}
	
	public function character()
	{
		return $this->belongsTo('\Character', 'payer_character_id');
	}

	public function corporation()
	{
		return $this->belongsTo('\Corporation', 'payer_corporation_id');
	}

	public function getPayerNameAttribute()
	{
		//TODO, if the character or corp isnt in our db...this wont display the name properly
		if($this->corporation != null) {
			return $this->character->name;
		}

		if($this->character != null) {
			return $this->character->name;
		}

		return "";
	}
}