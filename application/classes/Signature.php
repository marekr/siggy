<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model 
{
    public $timestamps = true;
	public $table = 'systemsigs';
	protected $fillable = ['sig', 'description', 'updated_at','siteID', 'type','sigSize','lastUpdater'];

	public $character = null;
	public $chainmap_wormholes = [];

	public static function findByGroupSystem(int $groupId, int $system): array
	{
		return self::where('systemID',$system)
				->where('groupID',$groupId)
				->get()
				->keyBy('id')
				->all();
	}

	public static function findWithGroup(int $groupId,int $id)
	{
		return self::where('id',$id)
				->where('groupID',$groupId)
				->first();
	}
}