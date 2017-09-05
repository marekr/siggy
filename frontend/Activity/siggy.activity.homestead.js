/*
* @license Proprietary
* @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
*/

siggy2.Activity = siggy2.Activity || {};

siggy2.Activity.Homestead = function(core)
{
	var $this = this;

	this.key = 'homestead';
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

siggy2.Activity.Homestead.prototype.start = function()
{
	$('#activity-' + this.key).show();
	this.update();
}

siggy2.Activity.Homestead.prototype.stop = function()
{
	clearTimeout(this._updateTimeout);
	$('#activity-' + this.key).hide();
}

siggy2.Activity.Homestead.prototype.update = function()
{
	var $this = this;
	$.ajax({
			url: this.core.settings.baseUrl + 'sig/scanned_systems',
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

siggy2.Activity.Homestead.prototype.updateTable = function( systems )
{
	var $this = this;

	$('#scanned-systems-table tbody').empty();

	for( var i in systems )
	{
		var system = systems[i];
		var row = this.templateRow( system );

		this.table.append(row);
	}

	$('#scanned-systems-table').trigger('update');
}