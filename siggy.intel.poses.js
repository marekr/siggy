/*
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

function intelposes(options)
{
	this.siggyMain = null;
	this.defaults = {
		baseUrl: ''
	};

	this.settings = $.extend(this.defaults, options);

	/* POSes */
	this.poses = {};

	/* temp hack */
	this.systemID = 0;
}

intelposes.prototype.initialize = function()
{
	var $this = this;
	$('#system-intel-poses tbody').empty();

	$('#system-intel-add-pos').click( function() {
		$this.siggyMain.openBox('#pos-form');
		$this.addPOS();
		return false;
	} );
}

intelposes.prototype.getPOSStatus = function( online )
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

intelposes.prototype.getPOSStatusClass = function( online )
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

intelposes.prototype.updatePOSList = function( data )
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
			var pos_id = data[i].id;

			var row = $("<tr>").attr('id', 'pos-'+pos_id);

			row.append($("<td>").addClass($this.getPOSStatusClass(data[i].online)).text( $this.getPOSStatus(data[i].online) ) );
			row.append($("<td>").text( data[i].location_planet + " - " + data[i].location_moon ) );
			row.append($("<td>").text( data[i].owner ) );
			row.append($("<td>").text( data[i].pos_type_name ) );
			row.append($("<td>").text( ucfirst(data[i].size) ) );
			row.append($("<td>").text( siggy2.Helpers.displayTimeStamp(data[i].added_date)));
			row.append($("<td>").text( data[i].notes ) );

			(function(pos_id){
				var edit = $("<a>").addClass("btn btn-default btn-xs").text("Edit").click( function() {
						$this.siggyMain.openBox('#pos-form');
						$this.editPOS( pos_id );
					}
				);
				var remove = $("<a>").addClass("btn btn-default btn-xs").text("Remove").click( function() {
						$this.removePOS( pos_id );
					}
				);

				row.append(
							$("<td>").append(edit)
									 .append(remove)
						 )
				   .addClass('center-text');
			})(pos_id);

			body.append(row);

			if( parseInt(data[i].online) == 1 )
			{
				owner_names.push(data[i].owner);
				online++;
			}
			else
			{
				offline++;
			}
			$this.poses[pos_id] = data[i];
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

intelposes.prototype.addPOS = function()
{
	this.setupPOSForm('add');
}

intelposes.prototype.setupPOSForm = function(mode, posID)
{
	var $this = this;

	var planet = $("#pos-form input[name=pos_location_planet]");
	var moon = $("#pos-form input[name=pos_location_moon]");
	var owner = $("#pos-form input[name=pos_owner]");
	var type = $("#pos-form select[name=pos_type]");
	var size = $("#pos-form select[name=pos_size]");
	var status = $("#pos-form select[name=pos_status]");
	var notes = $("#pos-form textarea[name=pos_notes]");

	var data = {};
	var action = '';
	if( mode == 'edit' )
	{
		data = this.poses[posID];
		action = $this.settings.baseUrl + 'pos/edit';
	}
	else
	{
		data = {
					location_planet: '',
					location_moon: '',
					owner: '',
					pos_type_id: 1,
					size: 'large',
					online: 1,
					notes: '',
					system_id: 0
				};
		action = $this.settings.baseUrl + 'pos/add';
	}

	planet.val( data.location_planet );
	moon.val( data.location_moon );
	owner.val( data.owner );
	type.val( data.pos_type_id );
	size.val( data.pos_size );
	status.val( data.online );
	notes.val( data.notes );

	$("#pos-form button[name=submit]").off('click');
	$("#pos-form button[name=submit]").click( function() {

		var posData = {
			pos_location_planet: planet.val(),
			pos_location_moon: moon.val(),
			pos_owner: owner.val(),
			pos_type: type.val(),
			pos_size: size.val(),
			pos_online: status.val(),
			pos_notes: notes.val(),
			pos_system_id: $this.systemID
		};

		if(mode == 'edit')
		{
			posData.pos_id = posID;
		}

		if( posData.pos_location_moon != "" && posData.pos_location_planet != "" )
		{
			$.post(action, posData, function ()
			{
                $(document).trigger('siggy.updateRequested', true );

				$.unblockUI();
			});
		}

		return false;
	});
}

intelposes.prototype.editPOS = function(posID)
{
	this.setupPOSForm('edit', posID);
}

intelposes.prototype.removePOS = function(posID)
{
	var $this = this;
	this.siggyMain.confirmDialog("Are you sure you want to delete the POS?", function() {
		$.post(this.settings.baseUrl + 'pos/remove', {pos_id: posID}, function ()
		{
			$('#pos-'+posID).remove();

			$this.forceUpdate = true;
			$this.siggyMain.updateNow();
		});
	});
}
