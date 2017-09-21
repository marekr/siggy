/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import $ from 'jquery';
import * as Handlebars from '../vendor/handlebars';
import Activity from './Activity';
import { Siggy as SiggyCore } from '../Siggy';
import { DScan as DScanModel, DScanRecordGroup as DScanRecordGroupModel } from '../Models';


interface DScanStartArgs {
	dscanId?: string
}

export class DScan extends Activity {

	public key:string = 'dscan';
	public title:string = 'dscan';

	private table = null;

	private dscanId: string = '';

	private dscan: DScanModel = null;

	private everythingList = null;
	private shipsList = null;
	private structuresList = null;

	private templateEverythingRow = null;

	constructor(core: SiggyCore) {
		super(core);
		var $this = this;

		this.everythingList = $('#activity-dscan-everything-list');
		this.shipsList = $('#activity-dscan-ships-list');
		this.structuresList = $('#activity-dscan-structures-list');
		this.templateEverythingRow = Handlebars.compile( $("#template-dscan-everything-row").html() );
	}

	public start(args: DScanStartArgs): void {
		if(args.dscanId != null) {
			this.dscanId = args.dscanId;
		}

		$('#activity-' + this.key).show();
		this.update();
	}

	public stop(): void {
		$('#activity-' + this.key).hide();
	}

	public update() {
		var $this = this;
		$.ajax({
				url: this.core.settings.baseUrl + 'dscan/json/'+this.dscanId,
				dataType: 'json',
				cache: false,
				async: true,
				method: 'get',
				success: function (data: DScanModel)
				{
					$this.dscan = data;
					$this.renderDscan();
				}
			});
	}

	public renderDscan() {
		let total = 0;
		let ships = 0;
		let structures = 0;

		this.everythingList.empty();
		this.shipsList.empty();
		this.structuresList.empty();
		for (var key in this.dscan.groups) {
			var group = this.dscan.groups[key];
			
			let row = this.templateEverythingRow({ table_type:'everything', group: group });

			this.everythingList.append(row);

			total += group.records.length;

			if(group.is_ship) {
				let row = this.templateEverythingRow({ table_type:'ship', group: group });
				this.shipsList.append(row);
				ships += group.records.length;
			}

			if(group.is_structure) {
				let row = this.templateEverythingRow({ table_type:'structure', group: group });
				this.structuresList.append(row);
				structures += group.records.length;
			}
		}

		$('#dscan-total-everything').text(total);
		$('#dscan-total-ships').text(ships);
		$('#dscan-total-structures').text(structures);
	}
}