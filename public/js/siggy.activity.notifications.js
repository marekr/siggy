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
	this._updateTimeoutNotifiers = null;
	this.core = core;
	this.updateRate = 60000;


	this.notifierFormState = 'none';
	this.notifierID = 0;
	this.notifierType = '';


	this.historyTable = $('#notifications-history-table tbody');
	this.notifierTable = $('#notifications-notifier-table tbody');


	this.templateHistoryRow = Handlebars.compile( $("#template-notification-history-table-row").html() );
	this.templateNotifierRow = Handlebars.compile( $("#template-notification-notifier-table-row").html() )
	this.notifierFormSystemMapped = Handlebars.compile( $("#template-notification-mapped-system").html() );
	this.notifierFormResidentFound = Handlebars.compile( $("#template-notification-resident-found").html() );

	var typeOptions = [
		{
			selector: '#notifier_add_system_mapped',
			key: 'system_mapped'
		},
		{
			selector: '#notifier_add_resident_found',
			key: 'resident_found'
		}
	]

	for(var i in typeOptions)
	{
		(function(t){
			$(t.selector).click( function()
			{
				$this.notifierFormState = 'add';
				$this.openNotifierForm(t.key);
			} );
		})(typeOptions[i]);
	}


	$('#notifier-form form').on('submit', function(e)
	{
		e.preventDefault();
		$this.notifierSubmit();
	});


	$('#notifications-notifier-table').on('click','.notifier-delete', function(e) {
		//$(this).data('id')
		$this.notifierDelete($(this).data('id'));
	});
}

siggy2.Activity.Notifications.prototype.start = function()
{
	$('#activity-' + this.key).show();
	this.update();
	this.updateNotifiers();
}

siggy2.Activity.Notifications.prototype.stop = function()
{
	clearTimeout(this._updateTimeout);
	clearTimeout(this._updateTimeoutNotifiers);
	$('#activity-' + this.key).hide();
}

siggy2.Activity.Notifications.prototype.notifierSubmit = function()
{
	var $this = this;
	var data = $('#notifier-form form').serializeObject();

	var url = '';
	if( this.notifierFormState == 'add' )
	{
		url = this.core.settings.baseUrl + 'notifications/notifiers_add';
	}
	else if( this.notifierFormState == 'edit' )
	{
		url = this.core.settings.baseUrl + 'notifications/notifiers_edit';

		data.id = 0;
	}
	else
	{
		return;
	}

	$.post(url, data,
	function (ret)
	{
		$this.updateNotifiers();
		$.unblockUI();
	});
}

siggy2.Activity.Notifications.prototype.notifierDelete = function(id)
{
	var $this = this;
	var data = {
		id: id
	}

	$.post(this.core.settings.baseUrl + 'notifications/notifiers_delete', data,
	function (ret)
	{
		$this.updateNotifiers();
	});
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
		],
		notifier: {
			type: notifier
		}
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


siggy2.Activity.Notifications.prototype.updateNotifiers = function()
{
	var $this = this;
	clearTimeout(this._updateTimeoutNotifiers);

	$.ajax({
			url: this.core.settings.baseUrl + 'notifications/notifiers',
			dataType: 'json',
			cache: false,
			async: true,
			method: 'get',
			success: function (data)
			{
				$this.updateNotifiersTable(data);

				$this._updateTimeoutNotifiers = setTimeout(function(thisObj)
				{
					thisObj.updateNotifiers()
				}, $this.updateRate, $this);
			}
		});
}


siggy2.Activity.Notifications.prototype.updateNotifiersTable = function( items )
{
	var $this = this;
	this.notifierTable.empty();

	for( var i in items )
	{
		var item = items[i];
		var row = this.templateNotifierRow( item );

		this.notifierTable.append(row);
	}
}
