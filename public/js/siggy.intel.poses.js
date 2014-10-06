function intelposes(options)
{
	this.siggyMain = null;
	this.defaults = {
		baseUrl: ''
	};

	this.settings = $.extend(this.defaults, options);
	
	/* POSes */
	this.poses = {};
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

	$('#pos-form button[name=cancel]').click( function() {
		$.unblockUI();
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
			var pos_id = data[i].pos_id;

			var row = $("<tr>").attr('id', 'pos-'+pos_id);

			row.append($("<td>").text( $this.getPOSStatus(data[i].pos_online) ) );
			row.append($("<td>").text( data[i].pos_location_planet + " - " + data[i].pos_location_moon ) );
			row.append($("<td>").text( data[i].pos_owner ) );
			row.append($("<td>").text( data[i].pos_type_name ) );
			row.append($("<td>").text( ucfirst(data[i].pos_size) ) );
			row.append($("<td>").text( siggymain.displayTimeStamp(data[i].pos_added_date)));
			row.append($("<td>").text( data[i].pos_notes ) );

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

			if( parseInt(data[i].pos_online) == 1 )
			{
				owner_names.push(data[i].pos_owner);
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
					pos_location_planet: '',
					pos_location_moon: '',
					pos_owner: '',
					pos_type: 1,
					pos_size: 'large',
					pos_online: 1,
					pos_notes: '',
					pos_system_id: 0
				};
		action = $this.settings.baseUrl + 'pos/add';
	}

	planet.val( data.pos_location_planet );
	moon.val( data.pos_location_moon );
	owner.val( data.pos_owner );
	type.val( data.pos_type );
	size.val( data.pos_size );
	status.val( data.pos_online );
	notes.val( data.pos_notes );

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
			pos_system_id: $this.siggyMain.systemID
		};

		if(mode == 'edit')
		{
			posData.pos_id = posID;
		}

		if( posData.pos_location_moon != "" && posData.pos_location_planet != "" )
		{
			$.post(action, posData, function ()
			{
				$this.siggyMain.forceUpdate = true;
				$this.siggyMain.updateNow();

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
