<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
			'scope_esi_location_read_location',
			'scope_esi_location_read_ship_type',
			'scope_esi_location_read_online',
			'scope_esi_ui_write_waypoint',
			'scope_esi_ui_open_window',
		];

	protected $avaliableScopes = [
			[
				'key' => 'scope_esi_location_read_location',
				'name' => 'ESI locationReadLocation'
			],
			[
				'key' => 'scope_esi_location_read_ship_type',
				'name' => 'ESI locationReadShipType'
			],
			[
				'key' => 'scope_esi_location_read_online',
				'name' => 'ESI locationReadOnline'
			],
			[
				'key' => 'scope_esi_ui_write_waypoint',
				'name' => 'ESI uiWriteWaypoint'
			],
			[
				'key' => 'scope_esi_ui_open_window',
				'name' => 'ESI uiOpenWindow'
			]
		];

	public function character()
	{
		return $this->belongsTo('Character');
	}

	public function scopes()
	{
		$scopes = [];
		foreach($this->avaliableScopes as $scope)
		{
			$scope['active'] = $this->attributes[$scope['key']] ? true : false;
			$scopes[] = $scope;
		}

		return $scopes;
	}
}