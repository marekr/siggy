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

	this.historyTable = $('#notifications-history-table tbody');


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
