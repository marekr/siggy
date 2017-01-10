<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;

class CharacterSetting extends Model {
	public $timestamps = false;
	public $table = 'character_settings';
	public $primaryKey = 'char_id';

	
}