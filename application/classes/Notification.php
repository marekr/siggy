<?php

class NotificationTypes {
	const SystemMappedByName = 'system_mapped';
	const SystemMapppedWithResident = 'system_resident_found';
}

class Notification {

	public static function latest($cutoff, $groupID, $charID = 0)
	{
		$data = DB::query(Database::SELECT, "SELECT * FROM notifications
											WHERE (( group_id=:group AND
												character_id=0 )
											OR
											( group_id=:group AND
										 	character_id=:char ))
											AND created_at > :cutoff
											ORDER BY created_at DESC
											LIMIT 10")
						->param(':cutoff', $cutoff)
						->param(':group', $groupID)
						->param(':char', $charID)
						->execute()
						->as_array();

		foreach($data as &$d)
		{
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
