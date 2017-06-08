<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class UserGroupPermission extends Model {
	public $table = 'users_group_acl';
	public $timestamps = false;

	public static function findByUser(int $userId)
	{
		return self::where('user_id', $userId)
					->get()
					->keyBy('group_id')
					->all();
	}
}