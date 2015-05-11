<?php

class NotificationTypes {
	const SystemMappedByName = 'system_mapped';
	const SystemMapppedWithResident = 'system_resident_found';
	const SiteFound = 'site_found';

	const SystemMappedByNameKeys = array('system_name', 'system_id', 'num_jumps');
	const SiteFoundKeys = array('site_id');

	public static function asArray()
	{
		return array(
					NotificationTypes::SystemMappedByName,
					NotificationTypes::SystemMapppedWithResident,
					NotificationTypes::SiteFound
					);
	}

	public static function getDataKeys( $type )
	{
		$keys = array();
		if( $type == NotificationTypes::SystemMappedByName )
		{
			$keys = NotificationTypes::SystemMappedByNameKeys;
		}
		else if( $type == NotificationTypes::SiteFound )
		{
			$keys = NotificationTypes::SiteFoundKeys;
		}

		return $keys;
	}
}
