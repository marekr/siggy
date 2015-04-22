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
	this.sigClocks = {};
	this.eolClocks = {};
	this.updateRate = 60000;

	this.templateRow = Handlebars.compile( $("#template-scanned-system-table-row").html() );

	this.table = $('#scanned-systems-table tbody');


	var tableSorterHeaders = {
		0: {
			sortInitialOrder: 'asc'
		}
	};

	$('#scanned-systems-table').tablesorter(
	{
		headers: tableSorterHeaders
	});

	$('#scanned-systems-table').trigger("sorton", [ [[0,0]] ]);

	$('#scanned-systems-table').on('click','.scanned-system-view', function(e) {
		$this.core.loadActivity('siggy', {systemID: $(this).data('id')});
	});
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
}

siggy2.Activity.Notifications.prototype.updateTable = function( systems )
{
	var $this = this;
}
