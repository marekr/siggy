/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import $ from 'jquery';

import * as moment from 'moment';
import * as Handlebars from '../vendor/handlebars';
import Activity from './Activity';
import { StaticData } from '../StaticData';
import Helpers from '../Helpers';
import { NotifierType } from '../Notifications';
import { Siggy as SiggyCore } from '../Siggy';


export class Notifications extends Activity {
	
	public key: string = 'notifications';
	public title: string = 'Notifications';
	private _updateTimeout = null;
	private _updateTimeoutNotifiers = null;
	private updateRate = 60000;


	private notifierFormState = 'none';
	private notifierID = 0;
	private notifierType = '';
	private notificationHistoryPage = 1;

	private historyTable = null;
	private notifierTable = null;
	private templateHistoryRow = null;
	private templateNotifierRow  = null;
	private notifierFormSystemMapped  = null;
	private notifierFormResidentFound  = null;
	private notifierFormSiteFound  = null;

	constructor(core: SiggyCore) {
		super(core);
		var $this = this;

		this.historyTable = $('#notifications-history-table tbody');
		this.notifierTable = $('#notifications-notifier-table tbody');

		$('.notifications-history-pagination').pagination({
			pages: 2,
			cssStyle: 'dark-theme',
			onPageClick: function(pageNumber, event)
			{
				$this.notificationHistoryPage = pageNumber;
				$this.update();
			}
		});


		this.templateHistoryRow = Handlebars.compile( $("#template-notification-history-table-row").html() );
		this.templateNotifierRow = Handlebars.compile( $("#template-notification-notifier-table-row").html() )
		this.notifierFormSystemMapped = Handlebars.compile( $("#template-notification-mapped-system").html() );
		this.notifierFormResidentFound = Handlebars.compile( $("#template-notification-system-resident-found").html() );
		this.notifierFormSiteFound = Handlebars.compile( $("#template-notification-site-found").html() );

		var typeOptions = [
			{
				selector: '#notifier_add_system_mapped',
				key: NotifierType.SystemMapped
			},
			{
				selector: '#notifier_add_system_resident_found',
				key: NotifierType.SystemResidentFound
			},
			{
				selector: '#notifier_add_site_found',
				key: NotifierType.SiteFound
			}
		]

		for(var i in typeOptions)
		{
			(function(t){
				$(t.selector).click( function()
				{
					$this.notifierFormState = 'add';
					$this.openNotifierForm(t.key);
				} );
			})(typeOptions[i]);
		}


		$('#notifier-form form').on('submit', function(e)
		{
			e.preventDefault();
			$this.notifierSubmit();
		});


		$('#notifications-notifier-table').on('click','.notifier-delete', function(e) {
			//$(this).data('id')
			$this.notifierDelete($(this).data('id'));
		});
	}

	public start(args): void
	{
		$('#activity-' + this.key).show();
		this.update();
		this.updateNotifiers();
	}

	public load(args): void
	{
	}
	
	public stop(): void
	{
		clearTimeout(this._updateTimeout);
		clearTimeout(this._updateTimeoutNotifiers);
		$('#activity-' + this.key).hide();
	}

	public notifierSubmit()
	{
		var $this = this;
		var data = $('#notifier-form form').serializeObject();

		var url = '';
		if( this.notifierFormState == 'add' )
		{
			url = this.core.settings.baseUrl + 'notifications/notifiers_add';
		}
		else if( this.notifierFormState == 'edit' )
		{
			url = this.core.settings.baseUrl + 'notifications/notifiers_edit';

			data.id = 0;
		}
		else
		{
			return;
		}

		$.post(url, data,
		function (ret)
		{
			$this.updateNotifiers();
			$.unblockUI();
		});
	}

	public notifierDelete(id: number)
	{
		var $this = this;
		var data = {
			id: id
		}

		$.post(this.core.settings.baseUrl + 'notifications/notifiers_delete', data,
		function (ret)
		{
			$this.updateNotifiers();
		});
	}

	public getNotifierTitle(notifier: NotifierType)
	{
		switch( notifier )
		{
			case NotifierType.SystemMapped:
				return 'System Mapped';
			case NotifierType.SystemResidentFound:
				return 'Resident Found';
			case NotifierType.SiteFound:
				return 'Site found';
		}
	}

	public getNotifierTemplate(notifier: string)
	{
		switch( notifier )
		{
			case NotifierType.SystemMapped:
				return this.notifierFormSystemMapped;
			case NotifierType.SystemResidentFound:
				return this.notifierFormResidentFound;
			case NotifierType.SiteFound:
				return this.notifierFormSiteFound;
		}
	}


	public openNotifierForm(notifier: NotifierType)
	{
		$('.notifier-system-typeahead').typeahead('destroy');
		$('#notifier-form div.form-content').empty();

		var $this = this;
		var data: any = {
			errors: {},
			scopes: [
				{
					value: 'personal',
					text: 'Personal'
				},
				{
					value: 'group',
					text: 'Group'
				}
			],
			notifier: {
				type: notifier,
				num_jumps: '0',
				resident_name: '',
				system_name: ''
			}
		};

		if( notifier == NotifierType.SiteFound )
		{
			data.sites = StaticData.getFullSiteListHandleBarDropdown();
		}

		$this.core.openBox('#notifier-form');

		var content = this.getNotifierTemplate(notifier);

		$('#notifier-form div.form-content').html(content(data));

		var title = window._('Add {0} Notifier').format($this.getNotifierTitle(notifier));
		$('#notifier-form div.box-header').html(title);


		Helpers.setupSystemTypeAhead('.notifier-system-typeahead');
	}

	public update()
	{
		var $this = this;
		clearTimeout(this._updateTimeout);

		$.ajax({
				url: this.core.settings.baseUrl + 'notifications/all',
				dataType: 'json',
				cache: false,
				async: true,
				method: 'get',
				data: {page: $this.notificationHistoryPage},
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

	public updateTable( data )
	{
		var $this = this;
		this.historyTable.empty();

		for( var i in data.items )
		{
			var item = data.items[i];
			var row = this.templateHistoryRow( item );

			this.historyTable.append(row);
		}

		$('.notifications-history-pagination').pagination('setPagesCount', data.total_pages);
		$('.notifications-history-pagination').pagination('redraw');
	}


	public updateNotifiers()
	{
		var $this = this;
		clearTimeout(this._updateTimeoutNotifiers);

		$.ajax({
				url: this.core.settings.baseUrl + 'notifications/notifiers',
				dataType: 'json',
				cache: false,
				async: true,
				method: 'get',
				success: function (data)
				{
					$this.updateNotifiersTable(data);

					$this._updateTimeoutNotifiers = setTimeout(function(thisObj)
					{
						thisObj.updateNotifiers()
					}, $this.updateRate, $this);
				}
			});
	}

	public updateNotifiersTable( items )
	{
		var $this = this;
		this.notifierTable.empty();

		for( var i in items )
		{
			var item = items[i];
			var row = this.templateNotifierRow( item );

			this.notifierTable.append(row);
		}
	}
}