<?php

namespace Siggy;

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;

class StructureType extends Model {
	public $timestamps = false;
	public $table = 'structure_types';
}