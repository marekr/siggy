<?php

class NotificationTypes {
	const SystemMappedByName = 'system_mapped';
	const SystemMapppedWithResident = 'system_resident_found';


	const SystemMappedByNameKeys = array('system_name');

	public static function asArray()
	{
		return array(
					NotificationTypes::SystemMappedByName,
					NotificationTypes::SystemMapppedWithResident
					);
	}

	public static function getDataKeys( $type )
	{
		$keys = array();
		if( $type == NotificationTypes::SystemMappedByName )
		{
			$keys = NotificationTypes::SystemMappedByNameKeys;
		}

		return $keys;
	}
}
