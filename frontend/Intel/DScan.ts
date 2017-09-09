/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import $ from 'jquery';
import * as Handlebars from '../vendor/handlebars';
import { Dialogs } from '../Dialogs';
import Helpers from '../Helpers';
import validate from 'validate.js';
import { Siggy as SiggyCore } from '../Siggy';

export default class DScan
{
	private core: SiggyCore = null;
	private defaults = {
		baseUrl: ''
	};

	private dscans = {};
	private templateForm = null;
	private templateTableRow = null;

	/* temp hack */
	public systemID = 0;

	public settings: any;

	private readonly formConstraints = {
		title: {
			presence: true
		},
		blob: {
			presence: true
		}
	};

	private readonly formDefaults = {
		title: '',
		blob: ''
	};

	constructor(core: SiggyCore, options) {
	
		this.settings = $.extend(this.defaults, options);
		this.core = core;
		
		this.templateForm = Handlebars.compile( $('#template-dialog-dscan').html() );
		this.templateTableRow = Handlebars.compile( $('#template-dscan-table-row').html() );
	}
		
	public initialize()
	{
		var $this = this;

		$('#system-intel-add-dscan').click( function() {
			$this.addForm();

			return false;
		} );
		
		$('#system-intel-dscans').on('click','.button-dscan-view ', function(e) {
			let id = $(this).data('id');

			let url = $this.settings.baseUrl + 'dscan/view/'+id;
			var win = window.open(url, '_blank');
			win.focus();
		});
		
		$('#system-intel-dscans').on('click','.button-dscan-remove', function(e) {
			var id = $(this).data('id');
			
			$this.remove(id);
		});
	}

	public addForm()
	{
		this.setupForm('add');
	}

	public setupForm(mode, id?: string)
	{
		var $this = this;

		var data = {
			model: $this.formDefaults,
			errors: {}
		};

		var dlg = Dialogs.dialog({
								title: "DScan",
								content: $this.templateForm(data),
								id: "dialog-dscan",
								buttons:{
									submit: {
										text: "Save",
										style: 'primary',
										callback: function(dialog) {
											$this.saveFormCallback(dialog);
										}
									},
									cancel: {
										text: "Cancel",
										style: 'danger',
										callback: function(dialog) {
											dialog.hide();
										}
									},
								}
							});
				
		$(dlg).data('form-mode', mode);
		$(dlg).data('model-id', id);
		
		dlg.show();
	}

	
	public saveFormCallback(dialog)
	{
		var $this = this;
		var formData = $('#dialog-dscan form').serializeObject();

		var errors = validate(formData, this.formConstraints);

		var data = {
			model: formData,
			errors: errors
		};
		
		if(Helpers.isDefined(errors))
		{
			dialog.replaceContent($this.templateForm(data));
			return;
		}

		var mode = $(dialog).data('form-mode');

		var action = '';
		if( mode == 'edit' )
		{
			var id = $(dialog).data('model-id');
			formData.id = id;
			action = $this.settings.baseUrl + 'dscan/edit';
		}
		else
		{
			action = $this.settings.baseUrl + 'dscan/add';
		}

		formData.system_id = this.systemID;

		$.post(action, JSON.stringify(formData))
			.done(function(respData) {
				$(document).trigger('siggy.updateRequested', true );
				dialog.hide();
			})
			.fail(function(jqXHR) {
				if(jqXHR.status >= 500) {			
					Dialogs.alertServerError("saving the dscan");
				}
			});
	}

	public updateDScan( data )
	{
		var $this = this;

		var body = $('#system-intel-dscans tbody');
		body.empty();

		if( typeof data != "undefined" && Object.size(data) > 0 )
		{
			for(var i in data)
			{
				var dscan = data[i];
				var row = this.templateTableRow(dscan);

				body.append(row);
			}

			$this.dscans = data;
		}
		else
		{
			$this.dscans = {};
		}
	}

	public remove(id: string)
	{
		var $this = this;
		Dialogs.confirm({
			message: "Confirm deletion",
			title: "Are you sure you want to delete the dscan entry?",
			yesCallback: function() {
				$.post($this.settings.baseUrl + 'dscan/remove', {id: id}, function ()
				{
					$('#dscan-'+id).remove();

					$(document).trigger('siggy.updateRequested', true );
				})
				.fail(function(jqXHR)
				{
					if(jqXHR.status >= 500)
					{
						Dialogs.alertServerError("removing the dscan");
					}
				});
			}
		});
	}

}

