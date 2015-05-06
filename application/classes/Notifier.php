<?php

class Notifier {

	public static function all($groupID, $characterID)
	{
		$data = DB::query(Database::SELECT, "SELECT * FROM notifiers
											WHERE (( group_id=:group AND
												scope='group' )
											OR
											( group_id=:group AND
											character_id=:char AND
											scope='personal' ))
											ORDER BY id DESC")
						->param(':group', $groupID)
						->param(':char', $characterID)
						->execute()
						->as_array();

		foreach($data as &$d)
		{
			$d['data'] = json_decode($d['data']);
		}

		return $data;
	}

	public static function create($type, $scope, $groupID, $characterID, $data)
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
							'type' => $type,
							'data' => json_encode($data),
							'group_id' => $groupID,
							'scope' => $scope,
							'character_id' => $characterID,
							'created_at' => time()
							);

		$dscanID = DB::insert('notifiers', array_keys($notifier) )
							->values(array_values($notifier) )
							->execute();
	}

	public static function update($id, $scope, $data)
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

		DB::update('notifiers')
				->set( $notifier )
				->where('id', '=', $id)
				->execute();
	}
}
