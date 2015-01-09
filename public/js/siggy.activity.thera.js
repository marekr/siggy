/*
* @license Proprietary
* @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
*/

siggy2.Activity = siggy2.Activity || {};

siggy2.Activity.Thera = function(core)
{
	this.key = 'thera';
	this._updateTimeout = null;
	this.core = core;
	this.sigClocks = {};
	this.eolClocks = {};

	this.templateRow = Handlebars.compile( $("#template-thera-table-row").html() );

	this.table = $('#thera-exits-table tbody');


	var tableSorterHeaders = {
		0: {
			sortInitialOrder: 'asc'
		},
	};

	$('#thera-exits-table').tablesorter(
	{
		headers: tableSorterHeaders
	});
	
	$('#thera-exits-table').trigger("sorton", [ [[0,0]] ]);
}

siggy2.Activity.Thera.prototype.start = function()
{
	$('#activity-' + this.key).show();
	this.update();
}

siggy2.Activity.Thera.prototype.stop = function()
{
	clearTimeout(this._updateTimeout);
	$('#activity-' + this.key).hide();
}

siggy2.Activity.Thera.prototype.update = function()
{
	var $this = this;
	$.ajax({
				url: this.core.settings.baseUrl + 'thera/latest_exits',
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
					}, 10000, $this);
				}
		});
}

siggy2.Activity.Thera.prototype.updateTable = function( exits )
{
	var $this = this;

	$('#thera-exits-table tbody tr').each(function()
	{
		var id = $(this).data('id');

		if( typeof exits[id] == 'undefined' )
		{
			$(this).children('td.wormhole-type').qtip('destroy');

			if( typeof $this.sigClocks[id] != 'undefined' )
			{
				$this.sigClocks[i].destroy();
				delete $this.sigClocks[i];
			}

			if( typeof $this.eolClocks[id] != 'undefined' )
			{
				$this.eolClocks[i].destroy();
				delete $this.eolClocks[i];
			}

			$(this).remove();
		}
		else
		{
			exits[id].row_already_exists = true;
		}
	});


	for( var i in exits )
	{
		var exit = exits[i];
		if( exit.row_already_exists )
			continue;

		var wh = siggy2.StaticData.getWormholeByID(exit.wormhole_type);

		if( wh != null )
		{
			desc_tooltip = siggy2.StaticData.templateWormholeInfoTooltip(wh);
			exit.wormhole_name = wh.name;
		}

		var row = this.templateRow( exit );


		this.table.append(row);


		this.sigClocks[exit.id] = new siggy2.Timer(exit.created_at * 1000, null, '#thera-sig-' + exit.id + ' td.age span.age-clock', "test");

		if( wh != null )
		{
			var endDate = parseInt(exit.created_at)+(3600*wh.lifetime);
			this.eolClocks[exit.id] = new siggy2.Timer(exit.created_at * 1000, endDate* 1000, '#thera-sig-' + exit.id + ' td.age p.eol-clock', "test");
		}

		$('#thera-sig-' + exit.id + ' td.wormhole-type').qtip({
			content: {
				text: desc_tooltip
			},
			position: {
				target: 'mouse',
				adjust: { x: 5, y: 5 },
				viewport: $(window)
			}
		});
	}

	$('#thera-exits-table').trigger('update');
}
