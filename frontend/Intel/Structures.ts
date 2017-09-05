/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */
 
import $ from 'jquery';
import * as Handlebars from '../vendor/handlebars';
import { Dialogs } from '../Dialogs';
import { StaticData } from '../StaticData';
import Helpers from '../Helpers';
import StructureVulnerability from '../Dialogs/StructureVulnerability';
import validate from 'validate.js';
import array_unique from 'locutus/php/array/array_unique';
import implode from 'locutus/php/strings/implode';
import { Siggy as SiggyCore } from '../Siggy';

export default class Structures
{
	private core: SiggyCore;
	private readonly defaults = {
		baseUrl: ''
	};

	public settings: any;
	public systemID: number = 0;


	private readonly formDefaults = {
		corporation_name: '',
		type_id: 0,
		notes: ''
	};

	private readonly formConstraints = {
		corporation_name: {
			presence: true
		},
		type_id: {
			presence: true,
			numericality: true
		},
		notes: {
			presence: {allowEmpty: true}
		}
	};

	private structures: any = {};

	private templateForm = null;
	private templateTableRow = null;

	constructor(core: SiggyCore, options) {
		this.core = core;

		this.settings = $.extend(true, {}, this.defaults, options);


		/* temp hack */
		this.systemID = 0;

		this.templateForm = Handlebars.compile( $('#template-dialog-structure').html() );
		this.templateTableRow = Handlebars.compile( $('#template-structure-table-row').html() );
	}

	public initialize ()
	{
		var $this = this;
		$('#system-intel-poses tbody').empty();

		$('#system-intel-add-structure').click( function() {
			$this.addForm();
			return false;
		} );

		$('#system-intel-structures').on('click','.button-structure-edit', function(e) {
			var id = $(this).data('id');

			$this.editForm(id);
		});
		
		$('#system-intel-structures').on('click','.button-structure-remove', function(e) {
			var id = $(this).data('id');
			
			$this.remove(id);
		});
		
		
		$('#system-intel-structures').on('click','.button-structure-vulnerability', function(e) {
			
			var id = $(this).data('id');

			var vulnDialog = new StructureVulnerability($this.core, $this.structures[id]);
			vulnDialog.show();
			
		});
	}

	public dockableClass( online )
	{
		online = parseInt(online);
		if( online )
		{
			return "pos-status-online";
		}
		else
		{
			return "pos-status-offline";
		}
	}

	public update( data )
	{
		var $this = this;

		var body = $('#system-intel-structures tbody');
		body.empty();
		$this.structures = {};

		var summary = '';
		var owner_names = [];

		if( Helpers.isDefined(data) && Object.size(data) > 0 )
		{
			for(var i in data)
			{
				var structure = data[i];
				var row = this.templateTableRow(structure);
				body.append(row);

				$this.structures[structure.id] = structure;
				owner_names.push(structure.corporation_name);
			}
			
			let owner_names2 = array_unique(owner_names);
			var owner_string = "<b>Residents:</b> "+implode(",",owner_names2);

			summary = owner_string;
		}
		else
		{
			$this.structures = {};
			summary = "No structures added for this system";
		}
		
		$("#structure-summary").html( summary );
	}

	public addForm()
	{
		this.setupForm('add');
	}

	public editForm(id)
	{
		this.setupForm('edit', id);
	}

	public saveFormCallback(dialog)
	{
		var $this = this;
		var formData = $('#dialog-structure form').serializeObject();

		var errors = validate(formData, this.formConstraints);

		var data = {
			model: formData,
			structureTypes: StaticData.getStructureTypeDropdown(),
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
			action = $this.settings.baseUrl + 'structure/edit';
		}
		else
		{
			action = $this.settings.baseUrl + 'structure/add';
		}

		formData.system_id = this.systemID;

		$.post(action, JSON.stringify(formData))
			.done(function(respData) {
				$(document).trigger('siggy.updateRequested', true );
				dialog.hide();
			})
			.fail(function(jqXHR) {
				if(jqXHR.status >= 500)
				{			
					Dialogs.alertServerError("saving the structure");
				}
			});
	}

	public setupForm(mode, id?)
	{
		var $this = this;

		var data = {
			model: $this.formDefaults,
			structureTypes: StaticData.getStructureTypeDropdown(),
			errors: {}
		};

		if(mode == 'edit')
		{
			if(Helpers.isDefined(this.structures[id]))
			{
				data.model = this.structures[id];
			}
		}

		var dlg = Dialogs.dialog({
								title: "Structure",
								content: $this.templateForm(data),
								id: "dialog-structure",
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

	public remove(id: number)
	{
		var $this = this;

		Dialogs.confirm({
			message: "Are you sure you want to delete the structure?",
			title: "Confirm deletion",
			yesCallback: function() {
				$.post($this.settings.baseUrl + 'structure/remove', JSON.stringify({id: id}))
					.done(function ()
					{
						$('#structure-'+id).remove();

						$(document).trigger('siggy.updateRequested', true );
					})
					.fail(function(jqXHR)
					{
						if(jqXHR.status >= 500)
						{
							Dialogs.alertServerError("removing the structure");
						}
					});
			}
		});
	}
}