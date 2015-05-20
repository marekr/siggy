/*
 * @license Proprietary
 * @copyright Copyright (c) 2015 borkedLabs - All Rights Reserved
 */

siggy2.Notifications = function(core)
{
	this.core = core;
	this.counter = $('#notification-count');

	this.lastRead = 0;
	this.newestTimestamp = 0;

	var $this = this;

	$('#notification-header-dropdown').on('show.bs.dropdown', function () {
		$this.lastRead = time();
		$this.setNotificationCount(0);
		$.ajax({
			url: $this.core.settings.baseUrl + 'notifications/read',
			cache: false,
			method: 'post',
			success: function (data)
			{
			}
		});
	})

	$('.notification-dropdown-view-link').click( function() {
		$this.core.loadActivity('notifications');
	})

}

siggy2.Notifications.prototype.setNotificationCount = function(counter)
{
	if( counter > 0 )
	{
		this.counter.text( counter );
	}
	else
	{
		this.counter.text('');
	}
}

siggy2.Notifications.prototype.update = function(data)
{
	this.lastRead = data.last_read;

	var size = Object.size(data.items);
	if( size > 0 )
	{
		this.setNotificationCount(0);

		var items = $('li.notification-dropdown-item');

		if( items.length >= 5 )
		{
			items.slice(-1*size).remove();
		}


		var counter = 0;

		data.items = data.items.reverse();
		for( var i in data.items )
		{
			var n = data.items[i];

			var ele = $('<li>');
			ele.html( siggy2.Notifications.getNotificationString(n.type, n.data) );
			ele.addClass('notification-dropdown-item');

			$('#notifications-menu').prepend( ele );

			if( n.created_at > this.lastRead )
				counter++;

			if( n.created_at > this.newestTimestamp )
				this.newestTimestamp = n.created_at;
		}

		if( counter > 0 )
			this.setNotificationCount(counter);
	}
}


siggy2.Notifications.getNotificationString = function(type, data)
{
	if( type == 'system_mapped' )
	{
		if(typeof(data.number_jumps) != 'undefined' && data.number_jumps > 0)
		{
			return _('<b>{0}</b> found system <b>{1}</b>, {2} jumps from <b>{3}</b>').format(data.character_name, data.nearby_system_name, data.number_jumps, data.system_name);
		}
		else
		{
			return _('<b>{0}</b> found system <b>{1}</b>').format(data.character_name, data.system_name);
		}
	}
	else if( type == 'system_resident_found' )
	{
		return _('<b>{0}</b> found residents <b>{1}</b> in system <b>{2}</b>').format(data.discoverer_name, data.resident_name, data.system_name);
	}
	else if( type == 'site_found' )
	{
		var name = siggy2.StaticData.getSiteNameByID(data.site_id);
		return _('<b>{0}</b> found {1} in system {2} as signature {3}').format(data.discoverer_name, _(name), data.system_name, data.signature);
	}
}

siggy2.Notifications.getNotifierString = function(type, data)
{
	if( type == 'system_mapped' )
	{
		if(typeof(data.num_jumps) != 'undefined' && data.num_jumps > 0)
		{
			return _('Trigger a system is found within <b>{0}</b> jumps of system <b>{1}</b>.').format(data.num_jumps, data.system_name);
		}
		else
		{
			return _('Trigger when system <b>{0}</b> mapped via jump.').format(data.system_name);
		}
	}
	else if( type == 'system_resident_found' )
	{
		return _('Trigger when POS belonging to <b>{0}</b> is present in a newly mapped system.').format(data.resident_name);
	}
	else if( type == 'site_found' )
	{
		var name = siggy2.StaticData.getSiteNameByID(data.site_id);
		return _('Trigger when site <b>{0}</b> is added as a signature.').format(_(name));
	}
}
