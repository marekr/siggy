<?php

class NotificationTypes {
	const SystemMappedByName = 'system_mapped';
	const SystemMapppedWithResident = 'system_resident_found';
}

class Notification {

	public static function latest($cutoff, $groupID, $charID = 0, $limit = 5)
	{
		$data = DB::query(Database::SELECT, "SELECT id, data, type, created_at FROM notifications
											WHERE (( group_id=:group AND
												character_id=0 )
											OR
											( group_id=:group AND
										 	character_id=:char ))
											AND created_at > :cutoff
											ORDER BY created_at DESC
											LIMIT :limit")
						->param(':cutoff', $cutoff)
						->param(':group', $groupID)
						->param(':char', $charID)
						->param(':limit', $limit)
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
}
