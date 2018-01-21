<?php

namespace Siggy;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Mpociot\HasCompositeKey\HasCompositeKey;
use Yadakhov\InsertOnDuplicateKey;

class ActiveSystem extends Model {
	use HasCompositeKey;
	use InsertOnDuplicateKey;

	public $incrementing = false;
	public $timestamps = false;
	public $table = 'activesystems';
	protected $primaryKey = ['systemID','chainmap_id','groupID'];

	protected $fillable = [
		'systemID',
		'groupID',
		'lastUpdate',
		'displayName',
		'lastActive',
		'inUse',
		'chainmap_id',
		'x',
		'y',
		'activity',
		'rally',
		'hazard'
	];

	public static function find(int $group, int $chainmapId, int $systemId)
	{
		return self::where('systemID', $systemId)
					->where('groupID', $group)
					->where('chainmap_id', $chainmapId)
					->first();
	}
}