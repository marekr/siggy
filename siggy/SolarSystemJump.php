<?php

namespace Siggy;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class SolarSystemJump extends Model {
	public $timestamps = false;
	public $table = 'solarsystem_jumps';
	
    protected $fillable = ['system_id', 'ship_jumps', 'date_start', 'date_end'];

}