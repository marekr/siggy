/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import $ from 'jquery';
import * as Handlebars from '../vendor/handlebars';
import Activity from './Activity';
import { Dialogs } from '../Dialogs';
import { Maps } from '../Maps';
import { Siggy as SiggyCore } from '../Siggy';

export class Chainmap extends Activity {

	public key:string = 'chainmap';
	public title:string = 'Chainmap';

	private _updateTimeout = null;
	private updateRate: number = 10000;

	private chainMapID: number = 0;
	
	private table = null;
	private templateTableRow = null;

	constructor(core: SiggyCore) {
		super(core);
		this.core = core;
		var $this = this;


		this.templateTableRow = Handlebars.compile( $("#template-chainmap-table-row").html() );

		this.table = $('#chainmap-connections-table tbody');

		var tableSorterHeaders = {
			0: {
				sortInitialOrder: 'asc'
			}
		};

		$('#chainmap-connections-table').tablesorter(
		{
			headers: tableSorterHeaders
		});

		$('#chainmap-connections-table').on('click','.chainmap-connection-delete', function(e) {
			var $row = this;
			Dialogs.confirm(
			{
				title: "Confirm deletion",
				message: "Are you sure you want to delete the connection?",
				yesCallback: function() {
					var type = $($row).data('type')+"s";

					var hashes = {count: 1};
					hashes[type] = [ $($row).data('hash') ];

					$this.core.activities.scan.map.processConnectionDelete(hashes, $this.chainMapID);

					$($row).parent().parent().remove();
				}
			});
		});

		$('#chainmap-table-selected').change( function() {
			var id = $(this).val();

			if( $this.chainMapID != id)
			{
				$this.chainMapID = id;
				$this.update();
			}
		});
	}

	public start(args): void
	{
		if( typeof(args) != 'undefined' )
		{
			if( typeof(args.chainMapID) != 'undefined' )
			{
				this.chainMapID = args.chainMapID;
				this.update();
			}
		}

		var sel = Maps.getSelectDropdown(this.chainMapID, "(current map)");
		$('#chainmap-table-selected').html(sel.html());
		$('#chainmap-table-selected').val(sel.val());

		$('#activity-' + this.key).show();
	}

	public stop(): void
	{
		clearTimeout(this._updateTimeout);
		$('#activity-' + this.key).hide();
	}

	public updateTable( data )
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
		$('#chainmap-connections-table').trigger('update');
	}

	public update()
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
}