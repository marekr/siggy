<?php

class Notification {

	public static function latest($cutoff, $groupID, $charID = 0, $offset = 0, $limit = 5 )
	{
		$data = DB::query(Database::SELECT, "SELECT id, data, type, created_at FROM notifications
											WHERE (( group_id=:group AND
												character_id=0 )
											OR
											( group_id=:group AND
										 	character_id=:char )
											OR
											( group_id=0 AND
										 	character_id= 0 ) )
											AND created_at > :cutoff
											ORDER BY created_at DESC
											LIMIT :offset, :limit")
						->param(':cutoff', $cutoff)
						->param(':group', $groupID)
						->param(':char', $charID)
						->param(':limit', $limit)
						->param(':offset', $offset)
						->execute()
						->as_array();
						
		foreach($data as &$d)
		{
			$d['id'] = (int)$d['id'];
			$d['created_at'] = (int)$d['created_at'];
			$d['data'] = json_decode($d['data']);
		}
		return $data;
	}

	public static function total($cutoff, $groupID, $charID = 0)
	{
		$data = DB::query(Database::SELECT, "SELECT COUNT(*) as sum FROM notifications
											WHERE (( group_id=:group AND
												character_id=0 )
											OR
											( group_id=:group AND
										 	character_id=:char )
											OR
											( group_id=0 AND
										 	character_id= 0 ))
											AND created_at > :cutoff")
						->param(':cutoff', $cutoff)
						->param(':group', $groupID)
						->param(':char', $charID)
						->execute()
						->current();
		$total = 0;

		if( isset($data['sum']))
		{
			$total = $data['sum'];
		}
		return $total;
	}

	public static function lastReadTimestamp( $groupID, $charID )
	{
		$data = DB::query(Database::SELECT, "SELECT last_notification_read FROM character_group
											WHERE group_id=:group AND
											id=:char ")
						->param(':group', $groupID)
						->param(':char', $charID)
						->execute()
						->current();

		if( isset($data['last_notification_read']) && !empty($data['last_notification_read']) )
		{
			return $data['last_notification_read'];
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

		$dscanID = DB::insert('notifications', array_keys($notification) )
							->values(array_values($notification) )
							->execute();
	}
}
