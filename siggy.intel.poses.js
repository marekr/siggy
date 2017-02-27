/*
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

siggy2.Intel = siggy2.Intel || {};

siggy2.Intel.Poses = function(core, options)
{
	this.core = core;
	this.defaults = {
		baseUrl: ''
	};

	this.settings = $.extend(true, {}, this.defaults, options);

	this.templateForm = Handlebars.compile( $('#template-dialog-pos').html() );
	this.templateTableRow = Handlebars.compile( $('#template-pos-table-row').html() );

	/* POSes */
	this.poses = {};

	/* temp hack */
	this.systemID = 0;

	
	this.formDefaults = {
		location_planet: '',
		location_moon: '',
		owner: '',
		type_id: 1,
		size: 'large',
		online: 1,
		notes: '',
		system_id: 0
	};

	this.formConstraints = {
		location_planet: {
			 presence: true
		},
		location_moon: {
			 presence: true
		},
		owner: {
			 presence: true
		},
		type_id: {
			presence: true,
			numericality: true
		},
		online: {
			presence: true,
			numericality: true
		},
		size: {
			presence: true
		},
		notes: {
			presence: {allowEmpty: true}
		}
	};
}

siggy2.Intel.Poses.prototype.initialize = function()
{
	var $this = this;
	$('#system-intel-poses tbody').empty();

	$('#system-intel-add-pos').click( function() {
		$this.addForm();
		return false;
	} );
	

	$('#system-intel-poses').on('click','.button-pos-edit', function(e) {
		var id = $(this).data('id');
		$this.editForm(id);
	});
	
	$('#system-intel-poses').on('click','.button-pos-remove', function(e) {
		var id = $(this).data('id');
		
		$this.remove(id);
	});
}

siggy2.Intel.Poses.prototype.getPOSStatus = function( online )
{
	online = parseInt(online);
	if( online )
	{
		return "Online";
	}
	else
	{
		return "Offline";
	}
}

siggy2.Intel.Poses.prototype.getPOSStatusClass = function( online )
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

siggy2.Intel.Poses.prototype.updatePOSList = function( data )
{
	var $this = this;

	var body = $('#system-intel-poses tbody');
	body.empty();

	var online = 0;
	var offline = 0;
	var summary = '';

	var owner_names = [];

	if( typeof data != "undefined" && Object.size(data) > 0 )
	{
		for(var i in data)
		{
			var pos = data[i];

			pos.status_class = $this.getPOSStatusClass(pos.online);
			pos.status = $this.getPOSStatus(pos.online);

			var row = this.templateTableRow(pos);
			body.append(row);

			if( parseInt(pos.online) == 1 )
			{
				owner_names.push(pos.owner);
				online++;
			}
			else
			{
				offline++;
			}
			$this.poses[pos.id] = pos;
		}

		owner_names = array_unique(owner_names);
		var owner_string = "<b>Residents:</b> "+implode(",",owner_names);

		summary = "<b>Total:</b> " + online + " online towers, " + offline + " offline towers" + "<br />" + owner_string;
	}
	else
	{
		$this.poses = {};
		summary = "No POS data added for this system";
	}

	$("#pos-summary").html( summary );
}

siggy2.Intel.Poses.prototype.addForm = function()
{
	this.setupForm('add');
}

siggy2.Intel.Poses.prototype.saveFormCallback = function(dialog)
{
	var $this = this;
	var formData = $('#dialog-pos form').serializeObject();

	var errors = validate(formData, this.formConstraints);

	var data = {
		model: formData,
		posTypes: siggy2.StaticData.getPosTypeDropdown(),
		posSizes: siggy2.StaticData.getPosSizes(),
		posStatuses: siggy2.StaticData.getPosStatuses(),
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
		action = $this.settings.baseUrl + 'pos/edit';
	}
	else
	{
		action = $this.settings.baseUrl + 'pos/add';
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
				siggy2.Dialogs.alertServerError("saving the structure");
			}
		});
}

siggy2.Intel.Poses.prototype.setupForm = function(mode, id)
{
	var $this = this;

	var data = {
		model: $this.formDefaults,
		posTypes: siggy2.StaticData.getPosTypeDropdown(),
		posSizes: siggy2.StaticData.getPosSizes(),
		posStatuses: siggy2.StaticData.getPosStatuses(),
		errors: {}
	};

	if(mode == 'edit')
	{
		if(siggy2.isDefined(this.poses[id]))
		{
			data.model = this.poses[id];
		}
	}

	var dlg = siggy2.Dialogs.dialog({
							title: "POS",
							content: $this.templateForm(data),
							id: "dialog-pos",
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

siggy2.Intel.Poses.prototype.editForm = function(id)
{
	this.setupForm('edit', id);
}

siggy2.Intel.Poses.prototype.remove = function(id)
{
	var $this = this;

	siggy2.Dialogs.confirm({
		message: "Are you sure you want to delete the POS?",
		title: "Confirm deletion",
		yesCallback: function() {
			$.post($this.settings.baseUrl + 'pos/remove', JSON.stringify({id: id}))
				.done(function()
				{
					$('#pos-'+posID).remove();

					$this.forceUpdate = true;
					$this.core.updateNow();
				})
				.fail(function(jqXHR)
				{
					if(jqXHR.status >= 500)
					{
						siggy2.Dialogs.alertServerError("removing the POS");
					}
				});
		}
	});
}
