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
			'valid'
		];

	public function character()
	{
		return $this->belongsTo('Character');
	}
}