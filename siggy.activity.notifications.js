/*
* @license Proprietary
* @copyright Copyright (c) 2015 borkedLabs - All Rights Reserved
*/

siggy2.Activity = siggy2.Activity || {};

siggy2.Activity.Notifications = function(core)
{
	var $this = this;

	this.key = 'notifications';
	this._updateTimeout = null;
	this.core = core;
	this.updateRate = 60000;

	this.historyTable = $('#notifications-history-table tbody');


	this.templateHistoryRow = Handlebars.compile( $("#template-notification-history-table-row").html() );
	this.notifierFormSystemMapped = Handlebars.compile( $("#template-notification-mapped-system").html() );
	this.notifierFormResidentFound = Handlebars.compile( $("#template-notification-resident-found").html() );

	$('#notifier_add_system_mapped').click( function()
	{
		$this.openNotifierForm('system_mapped')
	} );

	$('#notifier_add_resident_found').click( function()
	{
		$this.openNotifierForm('resident_found')
	} );
}

siggy2.Activity.Notifications.prototype.start = function()
{
	$('#activity-' + this.key).show();
	this.update();
}

siggy2.Activity.Notifications.prototype.stop = function()
{
	clearTimeout(this._updateTimeout);
	$('#activity-' + this.key).hide();
}

siggy2.Activity.Notifications.prototype.getNotifierTitle = function(notifier)
{
	switch( notifier )
	{
		case 'system_mapped':
			return 'System Mapped';
		case 'resident_found':
			return 'Resident Found';
	}
}

siggy2.Activity.Notifications.prototype.getNotifierTemplate = function(notifier)
{
	switch( notifier )
	{
		case 'system_mapped':
			return this.notifierFormSystemMapped;
		case 'resident_found':
			return this.notifierFormResidentFound;
	}
}


siggy2.Activity.Notifications.prototype.openNotifierForm = function(notifier)
{
	var $this = this;
	var data = {
		errors: {},
		scopes: [
			{
				value: 'personal',
				text: 'Personal'
			},
			{
				value: 'group',
				text: 'Group'
			}
		]
	}

	$this.core.openBox('#notifier-form');

	var content = this.getNotifierTemplate(notifier);

	$('#notifier-form div.form-content').html(content(data));

	var title = _('Add {0} Notifier').format($this.getNotifierTitle(notifier));
	$('#notifier-form div.box-header').html(title);
}

siggy2.Activity.Notifications.prototype.update = function()
{
	var $this = this;
	$.ajax({
			url: this.core.settings.baseUrl + 'notifications/all',
			dataType: 'json',
			cache: false,
			async: true,
			method: 'get',
			success: function (data)
			{
				$this.updateTable(data);

				$this._updateTimeout = setTimeout(function(thisObj)
				{
					thisObj.update()
				}, $this.updateRate, $this);
			}
		});
}

siggy2.Activity.Notifications.prototype.updateTable = function( items )
{
	var $this = this;
	this.historyTable.empty();

	for( var i in items )
	{
		var item = items[i];
		var row = this.templateHistoryRow( item );

		this.historyTable.append(row);
	}
}
