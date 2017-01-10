<?php

use Illuminate\Database\Capsule\Manager as DB;

class Notification {

	public static function latest($cutoff, int $groupID, int $charID = 0, int $offset = 0, int $limit = 5 )
	{
		$data = DB::select("SELECT id, data, type, created_at FROM notifications
											WHERE (( group_id=:group1 AND
												character_id=0 )
											OR
											( group_id=:group2 AND
										 	character_id=:char )
											OR
											( group_id=0 AND
										 	character_id= 0 ) )
											AND created_at > :cutoff
											ORDER BY created_at DESC
											LIMIT :offset, :limit",[
												'cutoff' => $cutoff,
												'group1' => $groupID,
												'group2' => $groupID,
												'char' => $charID,
												'limit' => $limit,
												'offset' => $offset
											]);
						
		foreach($data as &$d)
		{
			$d->data = json_decode($d->data);
		}
		return $data;
	}

	public static function total($cutoff, int $groupID, int $charID = 0)
	{
		$data = DB::selectOne("SELECT COUNT(*) as sum FROM notifications
											WHERE (( group_id=:group1 AND
												character_id=0 )
											OR
											( group_id=:group2 AND
										 	character_id=:char )
											OR
											( group_id=0 AND
										 	character_id= 0 ))
											AND created_at > :cutoff",[
												'cutoff' => $cutoff,
												'group1' => $groupID,
												'group2' => $groupID,
												'char' => $charID
											]);
		$total = 0;

		if( $data != null )
		{
			$total = $data->sum;
		}
		return $total;
	}

	public static function lastReadTimestamp( $groupID, $charID )
	{
		$characterGroup = CharacterGroup::find($charID, $groupID);

		if( $characterGroup != null )
		{
			return $characterGroup->last_notification_read;
		}
		else
		{
			return 0;
		}
	}

	public static function create($groupID, $characterID, $type, $data)
	{
		$notification = array(
							'type' => $type,
							'data' => json_encode($data),
							'group_id' => $groupID,
							'character_id' => $characterID,
							'created_at' => time()
							);

		$dscanID = DB::table('notifications')->insert($notification);
	}
}
