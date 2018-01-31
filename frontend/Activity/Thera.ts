/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import $ from 'jquery';
import * as moment from 'moment';
import * as Handlebars from '../vendor/handlebars';
import Activity from './Activity';
import { StaticData } from '../StaticData';
import Timer from '../Timer';
import { Maps } from '../Maps';
import { Siggy as SiggyCore } from '../Siggy';
import { Dialogs } from '../Dialogs';

export class Thera extends Activity {

	public key:string = 'thera';
	public title:string = 'Thera';

	private _updateTimeout = null;
	private sigClocks = {};
	private eolClocks = {};
	private updateRate: number = 30000;

	private templateRow = null;
	private table = null;

	constructor(core: SiggyCore) {
		super(core);
		var $this = this;

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

	public setupDialogImport()
	{
		var $this = this;
		$('#dialog-import-thera button[type=submit]').click( function(e) {
			e.stopPropagation();

			var data = {
				'clean': $('#dialog-import-thera input[name=clean]').val(),
				'chainmap': $('#dialog-import-thera select[name=chainmap]').val()
			};

			$.post($this.core.settings.baseUrl + 'thera/import_to_chainmap', JSON.stringify(data))
			.done(function(respData) {
				$.unblockUI();
			})
			.fail(function(jqXHR) {
				Dialogs.alertServerError("importing wormholes to chain map");
			});

			return false;
		});
	}

	public dialogImport()
	{
		this.core.openBox('#dialog-import-thera');

		var sel = Maps.getSelectDropdown(Maps.selected, "(current map)");
		$('#dialog-import-thera select[name=chainmap]').html(sel.html());
		$('#dialog-import-thera select[name=chainmap]').val(sel.val());
	}

	public start(args): void
	{
		$('#activity-' + this.key).show();
		this.update();
	}

	public load(args): void
	{
	}
	
	public stop(): void
	{
		clearTimeout(this._updateTimeout);
		$('#activity-' + this.key).hide();
	}

	public update()
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

	public updateTable( exits )
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

			var wh = StaticData.getWormholeByID(exit.wormhole_type);

			let desc_tooltip = '';
			if( wh != null )
			{
				desc_tooltip = StaticData.templateWormholeInfoTooltip(wh);
				exit.wormhole_name = wh.name;
			}

			var row = this.templateRow( exit );

			this.table.append(row);

			this.sigClocks[exit.id] = new Timer(exit.created_at * 1000, null, '#thera-sig-' + exit.id + ' td.age span.age-clock');

			if( wh != null )
			{
				var endDate = parseInt(exit.created_at)+(3600*wh.lifetime);
				this.eolClocks[exit.id] = new Timer(exit.created_at * 1000, endDate* 1000, '#thera-sig-' + exit.id + ' td.age p.eol-clock');
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
}