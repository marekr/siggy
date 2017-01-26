<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model {
	public $table = 'groupmembers';
	public $timestamps = false;

	const TypeCorp = 'corp';
	const TypeChar = 'char';

	public $corporation = null;
	public $character = null;

	protected $fillable = [
		'eveID',
		'accessName',
		'groupID',
		'memberType'
	];

	public function corporation()
	{
		if( $this->corporation == null || $this->corporation->id != $this->eveID )
		{
			$this->corporation = Corporation::find($this->eveID);
		}

		return $this->corporation;
	}
	
	public function group()
	{
		return $this->hasOne('Group', 'group_id');
	}

	public static function findByType(string $type, int $id): array
	{
		return self::where('eveID', $id)
				->where('memberType', $type)
				->get()
				->all();
	}

	public static function findByGroupAndType(int $groupId, string $type, int $id)
	{
		return self::where('eveID', $id)
			->where('memberType', $type)
			->where('groupID',$groupId)
			->first();
	}

	public static function findByGroup(int $groupId): array
	{
		return self::where('groupID', $groupId)
					->get()
					->all();
	}
}