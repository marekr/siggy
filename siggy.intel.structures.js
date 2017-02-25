/*
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

siggy2.Intel = siggy2.Intel || {};

siggy2.Intel.Structures = function(core, options)
{
	this.core = core;
	this.defaults = {
		baseUrl: ''
	};

	this.settings = $.extend(true, {}, this.defaults, options);

	/* POSes */
	this.structures = {};

	/* temp hack */
	this.systemID = 0;

	this.templateForm = Handlebars.compile( $('#template-dialog-structure').html() );
	this.templateTableRow = Handlebars.compile( $('#template-structure-table-row').html() );


	this.formDefaults = {
		corporation_name: '',
		type_id: 0,
		notes: ''
	};

	this.formConstraints = {
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
}

siggy2.Intel.Structures.prototype.initialize = function()
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
}

siggy2.Intel.Structures.prototype.dockableClass = function( online )
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

siggy2.Intel.Structures.prototype.update = function( data )
{
	var $this = this;

	var body = $('#system-intel-structures tbody');
	body.empty();
	$this.structures = {};

	if( siggy2.isDefined(data) && Object.size(data) > 0 )
	{
		for(var i in data)
		{
			var structure = data[i];
			var row = this.templateTableRow(structure);
			body.append(row);

			$this.structures[structure.id] = structure;
		}
	}
	else
	{
		$this.structures = {};
	}
}

siggy2.Intel.Structures.prototype.addForm = function()
{
	this.setupForm('add');
}

siggy2.Intel.Structures.prototype.editForm = function(id)
{
	this.setupForm('edit', id);
}

siggy2.Intel.Structures.prototype.saveFormCallback = function(dialog)
{
	var $this = this;
	var formData = $('#dialog-structure form').serializeObject();

	var errors = validate(formData, this.formConstraints);

	var data = {
		model: formData,
		structureTypes: siggy2.StaticData.getStructureTypeDropdown(),
		errors: errors
	};
	
	if(siggy2.isDefined(errors))
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

			$.unblockUI();
		})
		.fail(function(jqXHR) {
			if(jqXHR.status >= 500)
			{			
				siggy2.Dialogs.alertServerError("saving the structure");
			}
		});
}

siggy2.Intel.Structures.prototype.setupForm = function(mode, id)
{
	var $this = this;

	var data = {
		model: $this.formDefaults,
		structureTypes: siggy2.StaticData.getStructureTypeDropdown(),
		errors: {}
	};

	if(mode == 'edit')
	{
		if(siggy2.isDefined(this.structures[id]))
		{
			data.model = this.structures[id];
		}
	}

	var dlg = siggy2.Dialogs.dialog({
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
									style: 'dangler',
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

siggy2.Intel.Structures.prototype.remove = function(id)
{
	var $this = this;

	siggy2.Dialogs.confirm({
		message: "Are you sure you want to delete the structure?",
		title: "Confirm deletion",
		yesCallback: function() {
			$.post($this.settings.baseUrl + 'structure/remove', JSON.stringify({id: id}))
				.done(function ()
				{
					$('#structure-'+id).remove();

					$this.forceUpdate = true;
					$this.core.updateNow();
				})
				.fail(function(jqXHR)
				{
					if(jqXHR.status >= 500)
					{
						siggy2.Dialogs.alertServerError("removing the structure");
					}
				});
		}
	});
}
