/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import $ from 'jquery';
import * as Handlebars from '../vendor/handlebars';
import Activity from './Activity';
import { Siggy as SiggyCore } from '../Siggy';


interface DScanStartArgs {
	dscanId?: string
}

export class DScan extends Activity {

	public key:string = 'dscan';
	public title:string = 'dscan';

	private table = null;

	private dscanId: string = '';

	constructor(core: SiggyCore)
	{
		super(core);
		var $this = this;


	}

	public start(args: DScanStartArgs): void
	{
		if(args.dscanId != null) {
			this.dscanId = args.dscanId;
		}

		$('#activity-' + this.key).show();
		this.update();
	}

	public stop(): void
	{
		$('#activity-' + this.key).hide();
	}

	public update()
	{
		var $this = this;
		$.ajax({
				url: this.core.settings.baseUrl + 'dscan/json/'+this.dscanId,
				dataType: 'json',
				cache: false,
				async: true,
				method: 'get',
				success: function (data)
				{
					$this.updateTable(data);
				}
			});
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