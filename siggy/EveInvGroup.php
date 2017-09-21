<?php

namespace Siggy;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class EveInvGroup extends Model {
	public $table = 'eve_inv_groups';

	public $incrementing = false;
	public $timestamps = false;

	public $primaryKey = 'groupID';
}