<?php

namespace Siggy;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Structure extends Model {
	public $timestamps = true;
	public $table = 'structures';

	protected $fillable = [
		'group_id',
		'system_id',
		'creator_character_id',
		'type_id',
		'notes',
		'corporation_name',
		'corporation_id'
	];

	protected $hidden = [
		'group_id'
	];

    protected $appends = ['vulnerabilities'];

	public function creator()
	{
		return $this->belongsTo('Character','creator_id');
	}
	
	public function type()
	{
		return $this->belongsTo('Siggy\StructureType','type_id');
	}
	
	public function system()
	{
		return $this->belongsTo('System', 'system_id');
	}

	public function vulnerabilities()
	{
		return $this->hasMany('Siggy\StructureVulnerability', 'id', 'id');
	}
	
    public function getVulnerabilitiesAttribute() {
        return $this->vulnerabilities()->get()->all();
    }

	public static function findWithSystemByGroup(int $groupId, int $id): ?Structure
	{
		return self::with('system')->with('vulnerabilities')
			->where('group_id',$groupId)
			->where('id',$id)
			->first();
	}

	public static function findAllByGroupSystem(int $groupId, int $system): array
	{
		return self::where('system_id',$system)
				->where('group_id',$groupId)
				->get()
				->keyBy('id')
				->all();
	}
}