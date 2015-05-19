/*
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

function inteldscan(options)
{
	this.siggyMain = null;
	this.defaults = {
		baseUrl: ''
	};

	this.settings = $.extend(this.defaults, options);

	this.dscans = {};
	/* temp hack */
	this.systemID = 0;
}

inteldscan.prototype.initialize = function()
{
	var $this = this;

	$('#system-intel-add-dscan').click( function() {
		//$this.openPOSForm();
		$this.siggyMain.openBox('#dscan-form');
		$this.setupDScanForm('add');
		return false;
	} );
}

inteldscan.prototype.setupDScanForm = function(mode, posID)
{
	var $this = this;

	var title = $("#dscan-form input[name=dscan_title]");
	var scan = $("#dscan-form textarea[name=blob]");

	var data = {};
	var action = '';
	if( mode == 'edit' )
	{
		//data = this.poses[posID];
		action = $this.settings.baseUrl + 'dscan/edit';
	}
	else
	{
		data = {
					dscan_title: '',
					blob: ''
				};
		action = $this.settings.baseUrl + 'dscan/add';
	}

	title.val( data.dscan_title );
	scan.val( data.blob );

	$('#dscan-form button[name=submit]').off('click');
	$('#dscan-form button[name=submit]').click( function() {
		var data = {
			dscan_title: title.val(),
			blob: scan.val(),
			system_id: $this.systemID
		};

		if( data.dscan_title != "" && data.blob != "" )
		{
			$.post(action, data, function ()
			{
                $(document).trigger('siggy.updateRequested', true );
				$.unblockUI();
			});
		}

		return false;
	} );
}

inteldscan.prototype.updateDScan = function( data )
{
	var $this = this;

	var body = $('#system-intel-dscans tbody');
	body.empty();

	if( typeof data != "undefined" && Object.size(data) > 0 )
	{
		for(var i in data)
		{
			var dscan_id = data[i].dscan_id;
			var row = $("<tr>").attr('id', 'dscan-'+dscan_id);

			row.append( $("<td>").text( data[i].dscan_title ) );
			row.append( $("<td>").text(siggy2.Helpers.displayTimeStamp(data[i].dscan_date)) );
			row.append( $("<td>").text( data[i].dscan_added_by ) );

			(function(dscan_id){
				var view = $("<a>").addClass("btn btn-default btn-xs")
								   .text("View")
								   .attr("href",$this.settings.baseUrl + 'dscan/view/'+dscan_id)
								   .attr("target","_blank");

				var remove = $("<a>").addClass("btn btn-default btn-xs")
									 .text("Remove")
									 .click( function() {
											$this.removeDScan( dscan_id );
									}
				);
				row.append(
							$("<td>").append(view)
									 .append(remove)
						  )
				.addClass('center-text');
			})(dscan_id);

			body.append(row);
		}

		$this.dscans = data;
	}
	else
	{
		$this.dscans = {};
	}
}

inteldscan.prototype.removeDScan = function(dscanID)
{
	this.siggyMain.confirmDialog("Are you sure you want to delete the dscan entry?", function() {
		$.post(this.settings.baseUrl + 'dscan/remove', {dscan_id: dscanID}, function ()
		{
			$('#dscan-'+dscanID).remove();
		});
	});
}
