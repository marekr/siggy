<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;

class UserSSOCharacter extends Model {
	public $table = 'user_ssocharacter';
	public $timestamps = true;

	protected $fillable =  [
			'user_id',
			'character_owner_hash',
			'character_id',
			'access_token',
			'access_token_expiration',
			'refresh_token',
			'valid',
			'scope_character_location_read',
			'scope_character_navigation_write',
			'scope_esi_location_read_location',
			'scope_esi_location_read_ship_type',
			'scope_esi_ui_write_waypoint',
			'scope_esi_ui_open_window',
		];

	public function character()
	{
		return $this->belongsTo('Character');
	}
}