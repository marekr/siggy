<?php

namespace Siggy;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class EveInvType extends Model {
	public $table = 'eve_inv_types';

	public $incrementing = false;
	public $timestamps = false;

	public $primaryKey = 'typeID';
	
	public function group()
	{
		return $this->belongsTo('Siggy\EveInvGroup', 'groupID', 'groupID');
	}
}