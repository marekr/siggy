/*
* @license Proprietary
* @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
*/

siggy2.Activity = siggy2.Activity || {};

siggy2.Activity.Thera = function(core)
{
	var $this = this;

	this.key = 'thera';
	this._updateTimeout = null;
	this.core = core;
	this.sigClocks = {};
	this.eolClocks = {};
	this.updateRate = 30000;

	this.templateRow = Handlebars.compile( $("#template-thera-table-row").html() );

	this.table = $('#thera-exits-table tbody');


	var tableSorterHeaders = {
		0: {
			sortInitialOrder: 'asc'
		}
	};

	$('#thera-exits-table').tablesorter(
	{
		headers: tableSorterHeaders
	});

	$('#thera-exits-table').trigger("sorton", [ [[0,0]] ]);

	$('#activity-thera-import').click( function() {
		$this.dialogImport();
	});

	this.setupDialogImport();
}

siggy2.Activity.Thera.prototype.setupDialogImport = function()
{
	var $this = this;
	$('#dialog-import-thera button[type=submit]').click( function(e) {
		e.stopPropagation();

		var data = {
			'clean': $('#dialog-import-thera input[name=clean]').val(),
			'chainmap': $('#dialog-import-thera select[name=chainmap]').val()
		};

		$.post($this.core.settings.baseUrl + 'thera/import_to_chainmap', data,
		function (ret)
		{
			$.unblockUI();
		});

		return false;
	});
}

siggy2.Activity.Thera.prototype.dialogImport = function()
{
	this.core.openBox('#dialog-import-thera');

	var sel = siggy2.Maps.getSelectDropdown(siggy2.Maps.selected, "(current map)");
	$('#dialog-import-thera select[name=chainmap]').html(sel.html());
	$('#dialog-import-thera select[name=chainmap]').val(sel.val());
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
				}, $this.updateRate, $this);
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
				$this.sigClocks[id].destroy();
				delete $this.sigClocks[id];
			}

			if( typeof $this.eolClocks[id] != 'undefined' )
			{
				$this.eolClocks[id].destroy();
				delete $this.eolClocks[id];
			}

			$(this).remove();
		}
		else
		{
			exits[id].row_already_exists = true;

			$(this).children('td.jumps').text( exits[id].jumps );
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
