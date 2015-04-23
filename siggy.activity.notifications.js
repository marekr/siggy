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
