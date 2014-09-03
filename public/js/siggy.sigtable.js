/**
* @constructor
*/
function sigtable( options )
{
	this.sigData = {};
	this.sigClocks = {};
	this.siggyMain = null;
	this.systemID = 0;
	this.systemClass = 0;
	this.editingSig = false;

	this.defaults = {
		showSigSizeCol: false,
		baseUrl:''
	};

	this.settings = $.extend(this.defaults, options);

}

sigtable.prototype.initialize = function()
{
	var $this = this;
	if( this.settings.showSigSizeCol )
	{
		var tableSorterHeaders = {
			0: {
				sorter: false
			},
			5: {
				sorter: false
			},
			7: {
				sorter: false
			}
		};
	}
	else
	{
		var tableSorterHeaders = {
			0: {
				sorter: false
			},
			4: {
				sorter: false
			},
			6: {
				sorter: false
			}
		};
	}

	$('#sig-table').tablesorter(
	{
		headers: tableSorterHeaders
	});

	$('#sig-table').bind('sortEnd', function() {
		$this.colorizeSigRows();
	});


	$('#checkbox-show-anomalies').attr('checked', this.siggyMain.displayStates.showAnomalies);
	$('#checkbox-show-anomalies').change( function()
	{
		$this.changeAnomState($(this).is(':checked'));
	});
}

sigtable.prototype.clear = function()
{
	this.sigData = {};
	
	for(var i in this.sigClocks)
	{
		this.sigClocks[i].destroy();
		delete this.sigClocks[i];
	}
	
    $('td.moreinfo img').qtip('destroy');
    $('td.age span').qtip('destroy');

	$("#sig-table tbody").empty();
	this.editingSig = false;
}

sigtable.prototype.changeAnomState = function(visible)
{
	this.siggyMain.displayStates.showAnomalies = visible;
	this.siggyMain.saveDisplayState();

	if( visible )
	{
		$('#sig-table tbody tr').show();
	}
	else
	{
		$('#sig-table tbody tr.type-combat').hide();
	}
	this.colorizeSigRows();
}

sigtable.prototype.colorizeSigRows = function()
{
	var i = 0;
	$('#sig-table tbody tr').each( function() {
		$( this ).removeClass('alt');
		if( $(this).is(':visible') )
		{
			if( i % 2 != 0 )
				$( this ).addClass('alt');
			i++;
		}
	});
}

sigtable.prototype.convertType = function (type)
{
	//unknown null case, either way this should surpress it
	if (type == 'wh')
		return "WH";
	else if (type == 'grav')
		return "Ore";
	else if (type == 'ladar')
		return "Gas";
	else if (type == 'radar')
		return "Data";
	else if (type == 'mag')
		return "Relic";
	else if (type == 'combat')
		return "Combat";
	else
		return "";
}

sigtable.prototype.convertSiteID = function (whClass, type, siteID)
{
	if( siteID == 0 )
		return "";
	if (type == 'wh')
		return whLookup[whClass][siteID];
	else if (type == 'mag')
		return magsLookup[whClass][siteID];
	else if (type == 'radar')
		return radarsLookup[whClass][siteID];
	else if (type == 'ladar')
		return ladarsLookup[siteID];
	else if (type == 'grav')
		return gravsLookup[siteID];
	else
		return "";
}

sigtable.prototype.updateSigs = function (sigData, flashSigs)
{
	for (var i in this.sigData)
	{
		if (typeof(sigData[i]) !== undefined && typeof(sigData[i]) != "undefined" && sigData[i] !== null)
		{
			sigData[i].exists = true;
			if (!this.sigData[i].editing)
			{
				this.sigData[i] = sigData[i];
				this.updateSigRow(this.sigData[i], flashSigs);
			}
		}
		else
		{
			if (this.sigData[i].editing)
			{
				continue;
			}

			this.removeSigRow(this.sigData[i]);
			delete this.sigData[i];
		}
	}

	for (var i in sigData)
	{
		if (sigData[i].exists != true)
		{
			this.addSigRow(sigData[i],flashSigs);
			this.sigData[i] = sigData[i];
		}
	}

	if (!this.editingSig)
	{
		$('#sig-table').trigger('update');
	}
}

sigtable.prototype.removeSig = function (sigID)
{
	this.removeSigRow(
	{
		sigID: sigID
	});
	$('#sig-table').trigger('update');

	$.post(this.settings.baseUrl + 'sig/remove', {
		systemID: this.systemID,
		sigID: sigID
	});
}

sigtable.prototype.editTypeSelectChange = function (sigID)
{
	var newType = $("#sig-" + sigID + " td.type select").val();
	if (this.sigData[sigID].type != newType)
	{
		this.siggyMain.updateSiteSelect( '#sig-' + sigID + ' td.desc select', this.systemClass, newType, 0 );
	}
}

sigtable.prototype.removeSigRow = function (sigData)
{
	if(this.sigClocks[sigData.sigID] != undefined )
	{
		this.sigClocks[sigData.sigID].destroy();
		delete this.sigClocks[sigData.sigID];
	}

	$('#sig-' + sigData.sigID + ' td.moreinfo img').qtip('destroy');
	$('#sig-' + sigData.sigID + ' td.age span').qtip('destroy');

	$('#sig-' + sigData.sigID).remove();
	this.colorizeSigRows();
}

sigtable.prototype.addSigRow = function (sigData, flashSig)
{
	var that = this;

	var row = $('<tr>').attr('id', 'sig-' + sigData.sigID);
	row.addClass('type-'+sigData.type);

	var descTD = $('<td>').addClass('desc');

	descTD.text(this.convertSiteID(this.systemClass, sigData.type, sigData.siteID));
	descTD.append($('<p>').text(sigData.description));

	var creationInfo = '<b>Added by:</b> '+sigData.creator;
	if( sigData.lastUpdater != '' && typeof(sigData.lastUpdater) != "undefined" )
	{
		creationInfo += '<br /><b>Updated by:</b> '+sigData.lastUpdater;
		creationInfo += '<br /><b>Updated at:</b> '+siggymain.displayTimeStamp(sigData.updated);
	}

	var editIcon = $('<i>').addClass('icon icon-pencil icon-large')
							.click(function (e)
									{
										that.editSigForm(sigData.sigID);
									});

	var editTD = $('<td>').addClass('center-text edit').append(editIcon);


	var typeTD = $('<td>').addClass('center-text type')
						.text(this.convertType(sigData.type));

	var infoIcon = $('<i>').addClass('icon icon-info-sign icon-large icon-yellow');
	var infoTD = $('<td>').addClass('center-text moreinfo')
				.append(infoIcon)
				.append($("<div>").addClass('tooltip').attr('id', 'creation-info-' + sigData.sigID).html(creationInfo));

	var ageTDTooltip = $("<div>").addClass('tooltip').attr('id', 'age-timestamp-' + sigData.sigID).text(siggymain.displayTimeStamp(sigData.created));

	var ageTD = $('<td>').addClass('center-text age').append($("<span>").text("--")).append(ageTDTooltip);

	var removeIcon = $('<i>').addClass('icon icon-remove-sign icon-large icon-red');
	var removeTD = $('<td>').addClass('center-text remove')
							.append(removeIcon)
							.click(function (e)
									{
										that.removeSig(sigData.sigID)
									});
	row.append(editTD)
		.append($('<td>').addClass('center-text sig').text(sigData.sig));

	if( this.settings.showSigSizeCol )
	{
		row.append( $('<td>').addClass('center-text size').text(sigData.sigSize) );
	}
	row.append(typeTD)
		.append(descTD)
		.append(infoTD)
		.append(ageTD)
		.append(removeTD);

	$("#sig-table tbody").append( row );

	this.sigClocks[sigData.sigID] = new CountUp(sigData.created * 1000, '#sig-' + sigData.sigID + ' td.age span', "test");

	$('#sig-' + sigData.sigID + ' td.moreinfo i').qtip({
		content: {
			text: $('#creation-info-' + sigData.sigID) // Use the "div" element next to this for the content
		}
	});
	$('#sig-' + sigData.sigID + ' td.age span').qtip({
		content: {
			text: $('#age-timestamp-' + sigData.sigID) // Use the "div" element next to this for the content
		}
	});
	this.colorizeSigRows();

	if( flashSig )
	{
		$('#sig-' + sigData.sigID).fadeOutFlash("#A46D00", 20000);
	}

	if( !this.siggyMain.displayStates.showAnomalies && sigData.type == 'combat')
	{
		row.hide();
	}
}

sigtable.prototype.editSigForm = function (sigID)
{
	if (this.editingSig)
	{
		return;
	}

	this.sigData[sigID].editing = true;
	this.editingSig = true;

	var controlEle = $("#sig-" + sigID + " td.edit");
	controlEle.text('');

	var that = this;
	controlEle.append($('<img>').attr('src', this.settings.baseUrl + 'public/images/accept.png').click(function (e)
	{
		that.editSig(sigID)
	}));

	var sigEle = $("#sig-" + sigID + " td.sig");
	sigEle.text('');

	var sigInput = $('<input>').val(this.sigData[sigID].sig)
							.attr('maxlength', 3)
							.keypress( function(e) { if(e.which == 13){ that.editSig(sigID)  } } );
	sigEle.append(sigInput);

	if( this.settings.showSigSizeCol )
	{
			var sizeEle = $("#sig-" + sigID + " td.size");
			sizeEle.text('');

			sizeEle.append(this.generateOrderedSelect( [
														['', '--'],
														['1', '1'],
														['2.2','2.2'],
														['2.5','2.5'],
														['4','4'],
														['5', '5'],
														['6.67','6.67'],
														['10','10']
														], this.sigData[sigID].sigSize));
	}

	var typeEle = $("#sig-" + sigID + " td.type");
	typeEle.text('');

	typeEle.append(this.generateSelect({
											none: '--',
											wh: 'WH',
											ladar: 'Gas',
											radar: 'Data',
											mag: 'Relic',
											combat: 'Combat',
											grav: 'Ore'
										}, this.sigData[sigID].type)
										.change(function ()
												{
													that.editTypeSelectChange(sigID)
												}));

	var descEle = $('#sig-' + sigID + ' td.desc');
	descEle.text('');
	descEle.append(this.generateSiteSelect(this.systemClass, this.sigData[sigID].type, this.sigData[sigID].siteID)).append($('<br />'))
		.append( $('<input>').val(this.sigData[sigID].description)
								.keypress( function(e) { if(e.which == 13){ that.editSig(sigID)  } } )
								.css('width', '100%')
					);

	sigInput.focus();
}

sigtable.prototype.editTypeSelectChange = function (sigID)
{
	var newType = $("#sig-" + sigID + " td.type select").val();

	this.siggyMain.updateSiteSelect( '#sig-' + sigID + ' td.desc select', this.systemClass, newType, 0 );
}

sigtable.prototype.editSig = function (sigID)
{
	var sigEle = $("#sig-" + sigID + " td.sig input");
	if( this.settings.showSigSizeCol )
	{
			var sizeEle = $("#sig-" + sigID + " td.size select");

	}
	var typeEle = $("#sig-" + sigID + " td.type select");
	var descEle = $("#sig-" + sigID + " td.desc input");
	var siteEle = $("#sig-" + sigID + " td.desc select");

	if (sigEle.val().length != 3)
	{
		return false;
	}

	var sigUpdate = {};
	this.sigData[sigID].sig = sigEle.val().toUpperCase();
	this.sigData[sigID].type = typeEle.val();
	this.sigData[sigID].siteID = siteEle.val();
	this.sigData[sigID].description = descEle.val();

	var postData = {
		sigID: sigID,
		sig: this.sigData[sigID].sig,
		type: this.sigData[sigID].type,
		desc: this.sigData[sigID].description,
		siteID: this.sigData[sigID].siteID,
		systemID: this.systemID
	};

	if( this.settings.showSigSizeCol )
	{
		this.sigData[sigID].sigSize = sizeEle.val();
		postData.sigSize = this.sigData[sigID].sigSize;
	}

	var that = this;
	$.post(this.settings.baseUrl + 'sig/edit', postData, function ( data )
	{
		that.editingSig = false;
		that.sigData[sigID].editing = false;

	},"json").fail( function(xhr, textStatus, errorThrown) { alert(xhr.responseText) } );

	sigEle.remove();
	if( this.settings.showSigSizeCol )
	{
		sizeEle.remove();
	}
	typeEle.remove();
	descEle.remove();
	siteEle.remove();

	this.updateSigRow(this.sigData[sigID]);

	var controlEle = $("#sig-" + sigID + " td.edit");
	controlEle.text('');
	controlEle.append($('<i>').addClass('icon icon-pencil icon-large')
							.click(function (e)
									{
										that.editSigForm(sigID)
									})
								);

}

sigtable.prototype.generateSiteSelect = function (whClass, type, siteID)
{
	if (type == "wh") return this.generateSelect(whLookup[whClass], siteID);
	else if (type == "ladar") return this.generateSelect(ladarsLookup, siteID);
	else if (type == "mag") return this.generateSelect(magsLookup[whClass], siteID);
	else if (type == "grav") return this.generateSelect(gravsLookup, siteID);
	else if (type == "radar") return this.generateSelect(radarsLookup[whClass], siteID);
	else return this.generateSelect({
										0: '--'
									}, 0);
}


sigtable.prototype.generateOrderedSelect = function (options, select)
{
	var newSelect = $('<select>');

	for (var i=0; i < options.length; i++ )
	{
		newSelect.append($('<option>').attr('value', options[i][0]).text(options[i][1]));
	}

	newSelect.val(select);

	return newSelect;
}

sigtable.prototype.generateSelect = function (options, select)
{
	var newSelect = $('<select>');

	for (var i in options)
	{
		newSelect.append($('<option>').attr('value', i).text(options[i]));
	}

	newSelect.val(select);

	return newSelect;
}

sigtable.prototype.updateSigRow = function (sigData, flashSig)
{
	var baseID = '#sig-' + sigData.sigID;
	var creationInfo = '<b>Added by:</b> '+sigData.creator;
	if( sigData.lastUpdater != '' && typeof(sigData.lastUpdater) != "undefined" )
	{
		creationInfo += '<br /><b>Updated by:</b> '+sigData.lastUpdater;
		creationInfo += '<br /><b>Updated at:</b> '+siggymain.displayTimeStamp(sigData.updated);
	}

	$(baseID + ' td.sig').text(sigData.sig);
	$(baseID + ' td.type').text(this.convertType(sigData.type));

	if( this.settings.showSigSizeCol )
	{
			$(baseID + ' td.size').text(sigData.sigSize);
	}

	//stupidity part but ah well
	$(baseID + ' td.desc').text(this.convertSiteID(this.systemClass, sigData.type, sigData.siteID));
	$(baseID + ' td.desc p').remove();
	$(baseID + ' td.desc').append($('<p>').text(sigData.description));
	$('creation-info-' + sigData.sigID).html(creationInfo);

	if( !this.siggyMain.displayStates.showAnomalies && sigData.type == 'combat')
	{
		$(baseID).hide();
	}

	//if( flashSig )
	///{
		//$('#sig-' + sigData.sigID).fadeOutFlash("#A46D00", 10000);
	//}
}
