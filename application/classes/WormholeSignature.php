<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;
use Mpociot\HasCompositeKey\HasCompositeKey;

class WormholeSignature extends Model {
	use InsertOnDuplicateKey;
    use HasCompositeKey;
	
	public $table = 'wormhole_signatures';
	public $timestamps = false;
	protected $primaryKey = ['signature_id','chainmap_id','wormhole_hash'];

	protected $fillable =  [
			'signature_id',
			'chainmap_id',
			'wormhole_hash'
		];

	public static function findAllBySig(int $sigId)
	{
		return self::where('signature_id', $sigId)
				->get()
				->all();
	}

	public static function findByChainMapSig(int $chainMap, int $sigId)
	{
		return self::where('chainmap_id', $chainMap)
				->where('signature_id', $sigId)
				->first();
	}
}