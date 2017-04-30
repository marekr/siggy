<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


class Notifier extends Model  {
	public $timestamps = false;

	protected $fillable = [
		'type',
		'data',
		'group_id',
		'scope',
		'character_id',
		'created_at'
	];

	public static function allByGroupCharacter(int $groupID, int $characterID)
	{
		$data = DB::select("SELECT * FROM notifiers
											WHERE (( group_id=:group1 AND
												scope='group' )
											OR
											( group_id=:group2 AND
											character_id=:char AND
											scope='personal' ))
											ORDER BY id DESC",[
												'group1' => $groupID,
												'group2' => $groupID,
												'char' => $characterID
											]);

		foreach($data as &$d)
		{
			$d->data = json_decode($d->data);
		}

		return $data;
	}

	public static function createFancy(string $type, string $scope, int $groupID, int $characterID, array $data)
	{
		$keys = array();
		$keys = NotificationTypes::getDataKeys($type);
		
		// validate keys
		foreach($data as $k => $v)
		{
			if( !in_array($k, $keys) )
			{
				unset($data[$k]);
			}
		}

		foreach($keys as $k)
		{
			if( !isset($data[$k]) )
			{
				throw new Exception("Missing key for notifier");
			}
		}

		$notifier = [
			'type' => $type,
			'data' => json_encode($data),
			'group_id' => $groupID,
			'scope' => $scope,
			'character_id' => $characterID,
			'created_at' => time()
		];

		return self::create($notifier);
	}

	public static function updateFancy(int $id, string $scope, array $data)
	{
		$keys = array();
		$keys = NotificationTypes::getDataKeys($type);

		// validate keys
		foreach($data as $k => $v)
		{
			if( !in_array($k, $keys) )
			{
				unset($data[$k]);
			}
		}

		foreach($keys as $k)
		{
			if( !isset($data[$k]) )
			{
				throw new Exception("Missing key for notifier");
			}
		}

		$notifier = array(
							'data' => json_encode($data),
							'scope' => $scope,
							'updated_at' => time()
							);

		DB::table('notifiers')
				->where('id', $id)
				->update( $notifier );
	}

	public static function deleteByIdGroupCharacter(int $id, int $groupID, int $charID)
	{
		DB::delete("DELETE FROM notifiers WHERE id=:id AND ((scope='group' AND group_id=:groupID) OR
															(scope='personal' AND character_id=:charID))",
																[
																	'id' => $id,
																	'groupID' => $groupID,
																	'charID' => $charID
																]);

	}
}
