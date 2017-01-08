<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;

class UserGroupPassword extends Model {
	public $table = 'user_group_passwords';
	public $timestamps = false;
}