<?php

namespace Siggy;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class SolarSystemKill extends Model {
	public $timestamps = false;
	public $table = 'solarsystem_kills';
	
    protected $fillable = ['system_id', 'ship_kills', 'npc_kills', 'pod_kills', 'date_start', 'date_end'];

}