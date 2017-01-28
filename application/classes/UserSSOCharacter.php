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

	protected $avaliableScopes = [
			[
				'key' => 'scope_character_location_read',
				'name' => 'CREST characterLocationRead'
			],
			[
				'key' => 'scope_character_navigation_write',
				'name' => 'CREST characterNavigationWrite'
			],
			[
				'key' => 'scope_esi_location_read_location',
				'name' => 'ESI locationReadLocation'
			],
			[
				'key' => 'scope_esi_location_read_ship_type',
				'name' => 'ESI locationReadshipType'
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