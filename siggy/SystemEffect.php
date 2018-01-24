<?php

namespace Siggy;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class SystemEffect extends Model {
	public $timestamps = false;
	public $table = 'systemeffects';
}