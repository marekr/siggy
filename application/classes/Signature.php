<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model 
{
	public $timestamps = true;
	public $table = 'systemsigs';
	protected $fillable = ['sig', 
							'description', 
							'updated_at',
							'siteID', 
							'type',
							'sigSize',
							'lastUpdater',
							'systemID',
							'creator',
							'groupID',
							'systemID'
						];

	public $character = null;
	public $chainmap_wormholes = [];

	protected $appends = ['chainmap_wormholes'];

	public function getchainmapWormholesAttribute()
	{
		return $this->chainmap_wormholes;
	}

	public function system()
	{
		return $this->belongsTo('System', 'systemID');
	}

	public static function findByGroupSystem(int $groupId, int $system): array
	{
		return self::where('systemID',$system)
				->where('groupID',$groupId)
				->get()
				->keyBy('id')
				->all();
	}

	public static function findByGroup(int $groupId,int $id): ?Signature
	{
		return self::where('id',$id)
				->where('groupID',$groupId)
				->first();
	}
	
	public static function findByGroupWithSystem(int $groupId,int $id): ?Signature
	{
		return self::where('id',$id)
				->where('groupID',$groupId)
				->with('system')
				->first();
	}

	public static function findByGroupSystemSig(int $groupId, int $systemId, string $sig): ?Signature
	{
		return self::where('systemID',$systemId)
				->where('groupID',$groupId)
				->where('sig', $sig)
				->first();
	}
}