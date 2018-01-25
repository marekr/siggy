/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import $ from 'jquery';
import * as Handlebars from '../vendor/handlebars';
import Activity from './Activity';
import { Siggy as SiggyCore } from '../Siggy';

export class Notes extends Activity {

	public key:string = 'notes';
	public title:string = 'Notes';
	
	private _updateTimeout = null;
	private updateRate = 60000;

	private templateRow = null;
	private table = null;

	constructor(core: SiggyCore)
	{
		super(core);
		var $this = this;

		this.templateRow = Handlebars.compile( $("#template-scanned-system-table-row").html() );

		var tableSorterHeaders = {
			0: {
				sortInitialOrder: 'asc'
			}
		};

		$('#scanned-systems-table').trigger("sorton", [ [[0,0]] ]);
		
		$('#scanned-systems-table').on('click','.scanned-system-view', function(e) {
			$this.core.loadActivity('siggy', {systemID: $(this).data('id')});
		});
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
		/*
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
			*/
	}

	public updateTable( systems )
	{
		/*
		var $this = this;

		$('#scanned-systems-table tbody').empty();

		for( var i in systems )
		{
			var system = systems[i];
			var row = this.templateRow( system );

			this.table.append(row);
		}

		$('#scanned-systems-table').trigger('update');*/
	}
}