/*
 * @license Proprietary
 * @copyright Copyright (c) 2015 borkedLabs - All Rights Reserved
 */

siggy2.Notifications = function(core)
{
	this.core = core;
	this.counter = $('#notification-count');

	this.lastRead = 0;

	var $this = this;
	$('#notifications-button').click( function()
	{
		$this.lastRead = time();
		$this.setNotificationCount(0);
		$.ajax({
			url: $this.core.settings.baseUrl + 'notifications/read',
			dataType: 'json',
			cache: false,
			async: true,
			method: 'post',
			success: function (data)
			{
			}
		});
	});

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
		for( var i in data.items )
		{
			var n = data.items[i];
			var ele = $('<li>');
			ele.html( this.getNotificationString(n.type, n.data) );
			ele.addClass('notification-dropdown-item');

			$('#notifications-menu').prepend( ele );

			if( n.created_at > this.lastRead )
				counter++;
		}

		if( counter > 0 )
			this.setNotificationCount(counter);
	}
}


siggy2.Notifications.prototype.getNotificationString = function(type, data)
{
	if( type == 'system_mapped' )
	{
		return _('<b>{0}</b> found marked system <b>{1}').format(data.character_name, data.name)
	}
	else if( type == 'system_resident_found' )
	{
		return _('<b>{0}</b> found marked residents <b>{1}</b> in system <b>{2}</b>').format(data.discoverer_name, data.resident_name, data.system_name)
	}
}
