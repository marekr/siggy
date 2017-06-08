<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class UserGroupPassword extends Model {
	public $table = 'user_group_passwords';
	public $timestamps = false;
}