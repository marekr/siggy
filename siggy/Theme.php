<?php

namespace Siggy;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model {
	public $timestamps = false;

	public static function findByGroup(int $groupId, int $id)
	{
		return self::where('visibility', 'all')
					->orWhere(function ($query) use ($groupId) {
							$query->where('group_id', $groupId)
								->where('visibility', 'group');
						})
					->first();
	}

	public static function allByGroup(int $groupId)
	{
		return self::where('visibility', 'all')
					->orWhere(function ($query) use ($groupId) {
							$query->where('group_id', $groupId)
								->where('visibility', 'group');
						})
					->get()
					->all();
	}
}