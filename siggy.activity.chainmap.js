/*
* @license Proprietary
* @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
*/

siggy2.Activity = siggy2.Activity || {};

siggy2.Activity.Chainmap = function(core)
{
	var $this = this;
	this.key = 'chainmap';
	this._updateTimeout = null;
	this.core = core;
	this.updateRate = 10000;

	this.chainMapID = 0;


	this.templateTableRow = Handlebars.compile( $("#template-chainmap-table-row").html() );

	this.table = $('#chainmap-connections-table tbody');

	$('#chainmap-connections-table').on('click','.chainmap-connection-delete', function(e) {
		var $row = this;
		$this.core.confirmDialog("Are you sure you want to delete the wormhole?", function() {
			var type = $($row).data('type')+"s";

			var hashes = {count: 1};
			hashes[type] = [ $($row).data('hash') ];

			$this.core.activities.siggy.map.processConnectionDelete(hashes);

			$($row).parent().parent().remove();
		});
	});
}

siggy2.Activity.Chainmap.prototype.start = function(args)
{
	if( typeof(args) != 'undefined' )
	{
		if( typeof(args.chainMapID) != 'undefined' )
		{
			this.chainMapID = args.chainMapID;
			this.update();
		}
	}

	$('#activity-' + this.key).show();
}

siggy2.Activity.Chainmap.prototype.stop = function()
{
	clearTimeout(this._updateTimeout);
	$('#activity-' + this.key).hide();
}

siggy2.Activity.Chainmap.prototype.updateTable = function( data )
{
	var $this = this;

	this.table.empty();

	for( var i in data.connections )
	{
		var connection = data.connections[i];
		var row = this.templateTableRow({
										toSystem: data.systems[connection.to_system_id],
										connection: connection,
										fromSystem: data.systems[connection.from_system_id]
									});

		this.table.append(row);
	}
}

siggy2.Activity.Chainmap.prototype.update = function()
{
	var $this = this;
	$.ajax({
			url: this.core.settings.baseUrl + 'chainmap/connections',
			dataType: 'json',
			cache: false,
			async: true,
			method: 'get',
			data: {chainmap: this.chainMapID},
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
