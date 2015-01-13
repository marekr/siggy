/*
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

/**
* @constructor
*/
siggy2.SigTable = function( options )
{
	this.sigData = {};
	this.sigClocks = {};
	this.eolClocks = {};
	this.siggyMain = null;
	this.systemID = 0;
	this.systemClass = 0;
	this.editingSig = false;

	this.defaults = {
		showSigSizeCol: false,
		baseUrl:''
	};

	this.settings = $.extend(this.defaults, options);

	this.templateSigRow = Handlebars.compile( $("#template-sig-table-row").html() );
	this.setupHandlebars();
}

siggy2.SigTable.prototype.initialize = function()
{
	var $this = this;
	if( this.settings.showSigSizeCol )
	{
		var tableSorterHeaders = {
			0: {
				sorter: false
			},
			1: {
				sortInitialOrder: 'desc'
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


	$( document ).on('click', 'td.edit i', function (e)
										{
											$this.editSigForm($(this).parent().parent().data('sig-id'));
										});

	$( document ).on('click', 'td.remove i', function (e)
										{
											$this.removeSig($(this).parent().parent().data('sig-id'));
										});

	$('#sig-table').tablesorter(
	{
		headers: tableSorterHeaders
	});

	$('#sig-table').trigger("sorton", [ [[1,0]] ]);

	$('#sig-table').bind('sortEnd', function() {
	});


	$('#checkbox-show-anomalies').prop('checked', this.siggyMain.displayStates.showAnomalies);
	$('#checkbox-show-anomalies').change( function()
	{
		$this.changeAnomState($(this).is(':checked'));
		$this.updateSigTotal();
	});

	$this.initializeHotkeys();

	this.setupAddDialog();
}

siggy2.SigTable.prototype.initializeHotkeys = function()
{
	var $this = this;

	$(document).bind('keydown', 'ctrl+q', function(){
		$(document).scrollTop( $('#sig-table').offset().top-100 );
	});

	$(document).bind('keydown', 'ctrl+b', function(){
		$(document).scrollTop( $("#sig-add-box textarea[name=mass_sigs]").offset().top-100);
		$("#sig-add-box textarea[name=mass_sigs]").focus();
	});

	this.siggyMain.hotkeyhelper.registerHotkey('Ctrl+Q', 'Jump to signatue table');
	this.siggyMain.hotkeyhelper.registerHotkey('Ctrl+B', 'Focus on signature adder');
}


siggy2.SigTable.prototype.setupAddDialog = function ()
{
	var $this = this;
	var massAddBlob = $('#mass-add-sig-box textarea[name=blob]');
	massAddBlob.val('');
	$('#mass-add-sig-box button[name=add]').click( function()
	{
		$this.massAddHandler($this.systemID,massAddBlob.val());
		massAddBlob.val('');

		$.unblockUI();
		return false;
	} );

	$('#mass-add-sig-box button[name=cancel]').click( function()
	{
		$.unblockUI();
		return false;
	} );

	$('#mass-add-sigs').click(function ()
	{
		$.blockUI({
			message: $('#mass-add-sig-box'),
			css: {
				border: 'none',
				padding: '15px',
				background: 'transparent',
				color: 'inherit',
				cursor: 'auto',
				textAlign: 'left',
				top: '20%',
				width: 'auto',
				centerX: true,
				centerY: false
			},
			overlayCSS: {
				cursor: 'auto'
			},
			fadeIn:  0,
			fadeOut:  0
		});
		$('.blockOverlay').attr('title','Click to unblock').click($.unblockUI);
		return false;
	});

	//override potential form memory
	$('#sig-add-box select[name=type]').val('none');


	var massSigEnter = function(e)
	{
		//enter key
		if(e.which == 13)
		{
			$this.massAddHandler($this.systemID,$('#sig-add-box textarea[name=mass_sigs]').val());
			$('#sig-add-box textarea[name=mass_sigs]').val('');
		}
	}

	$( document ).on('keypress', '#sig-add-box textarea[name=mass_sigs]', massSigEnter);
	$('#sig-add-box textarea[name=mass_sigs]').click(function() {
		//need this to fix event bubble on the collaspible
		return false;
	});

	$('#sig-add-box form').submit(function ()
	{
		$this.sigAddHandler();
		return false;
	});


	$('#sig-add-box select[name=type]').change(function ()
	{
		newType = $(this).val();

		$this.updateSiteSelect( '#sig-add-box select[name=site]', $this.systemClass, newType, 0);
	}).keypress(this.addBoxEnterHandler);

	if( this.settings.showSigSizeCol )
	{
		$('#sig-add-box select[name=size]').keypress(this.addBoxEnterHandler);
	}

	$( document ).on('keypress', '#sig-add-box select[name=site]', this.addBoxEnterHandler);
}

siggy2.SigTable.prototype.sigAddHandler = function()
{
	var $this = this;
	var sigEle = $('#sig-add-box input[name=sig]');
	var typeEle = $('#sig-add-box select[name=type]');
	var descEle = $('#sig-add-box input[name=desc]');
	var siteEle = $('#sig-add-box select[name=site]');

	if (sigEle.val().length != 3)
	{
		return false;
	}

	//idiot proof for ccp
	var type = 'none';
	if( typeEle.val() != null )
	{
		type = typeEle.val();
	}

	var postData = {
		systemID: $this.systemID,
		sig: sigEle.val(),
		type: type,
		desc: descEle.val(),
		siteID: siteEle.val()
	};

	if( $this.settings.showSigSizeCol )
	{
		var sizeEle = $('#sig-add-box select[name=size]');
		postData.sigSize = sizeEle.val();
	}

	$.post($this.settings.baseUrl + 'sig/add', postData, function (newSig)
	{
		for (var i in newSig)
		{
			$this.addSigRow(newSig[i]);
		}
		$.extend($this.sigData, newSig);
		$('#sig-table').trigger('update');

	}, 'json');

	sigEle.val('');
	if( $this.settings.showSigSizeCol )
	{
		sizeEle.val('');
	}
	typeEle.val('none');
	descEle.val('');
	siteEle.replaceWith($('<select>').attr('name', 'site').addClass('siggy-input'));

	sigEle.focus();
}

siggy2.SigTable.prototype.massAddHandler = function(systemID, data)
{
	var $this = this;

	var postData = {
		systemID: systemID,
		blob: data
	};

	$.post( $this.settings.baseUrl + 'sig/mass_add', postData, function (newSig)
	{
		for (var i in newSig)
		{
			$this.addSigRow(newSig[i]);
		}

		$.extend($this.sigData, newSig);
		$('#sig-table').trigger('update');
	}, 'json');
}

siggy2.SigTable.prototype.addBoxEnterHandler = function(e)
{
	if(e.which == 13)
	{
		$('button[name=add]').focus().click();
	}
}

siggy2.SigTable.prototype.clear = function()
{
	this.sigData = {};

	for(var i in this.sigClocks)
	{
		this.sigClocks[i].destroy();
		delete this.sigClocks[i];
	}

	for(var i in this.eolClocks)
	{
		this.eolClocks[i].destroy();
		delete this.eolClocks[i];
	}

    $('td.moreinfo img').qtip('destroy');
    $('td.age span').qtip('destroy');
    $('td.desc').qtip('destroy');

	$("#sig-table tbody").empty();
	this.editingSig = false;
}

siggy2.SigTable.prototype.refreshAnomState = function()
{
	this.changeAnomState(this.siggyMain.displayStates.showAnomalies);
}

siggy2.SigTable.prototype.changeAnomState = function(visible)
{
	this.siggyMain.displayStates.showAnomalies = visible;
	this.siggyMain.saveDisplayState();

	if( visible )
	{
		$('#sig-table tbody tr').show();
	}
	else
	{
		$('#sig-table tbody tr.type-anomaly').hide();
	}
}

siggy2.SigTable.prototype.convertType = function(type)
{
	//unknown null case, either way this should surpress it
	if (type == 'wh')
		return _("WH");
	else if (type == 'ore')
		return _("Ore");
	else if (type == 'gas')
		return _("Gas");
	else if (type == 'data')
		return _("Data");
	else if (type == 'relic')
		return _("Relic");
	else if (type == 'anomaly')
		return _("Combat");
	else
		return "";
}

siggy2.SigTable.prototype.convertSiteID = function (whClass, type, siteID)
{
	if( siteID == 0 )
		return "";
	if (type == 'wh')
		return siggy2.StaticData.getWormholeFancyNameByID(siteID);
	else
		return _(siggy2.StaticData.getSiteNameByID(siteID));
}

siggy2.SigTable.prototype.updateSigs = function (sigData, flashSigs)
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

	this.refreshAnomState();
	this.updateSigTotal();
}

siggy2.SigTable.prototype.updateSigTotal = function()
{
	$('#number-sigs').text(	$('#sig-table tr.sig:visible').length );
}

siggy2.SigTable.prototype.removeSig = function (sigID)
{
	this.removeSigRow(
	{
		sigID: sigID
	});
	$('#sig-table').trigger('update');
	this.updateSigTotal();

	$.post(this.settings.baseUrl + 'sig/remove', {
		systemID: this.systemID,
		sigID: sigID
	});
}

siggy2.SigTable.prototype.updateSiteSelect = function( ele, whClass, type, siteID )
{
	var elem = $( ele );
	elem.empty();

	/* use common select generator method and just swap contents */
	var newSel = this.generateSiteSelect( whClass, type, siteID );
	elem.empty().html(newSel.html());
}

siggy2.SigTable.prototype.removeSigRow = function (sigData)
{
	if(this.sigClocks[sigData.sigID] != undefined )
	{
		this.sigClocks[sigData.sigID].destroy();
		delete this.sigClocks[sigData.sigID];
	}

	$('#sig-' + sigData.sigID + ' td.moreinfo img').qtip('destroy');
	$('#sig-' + sigData.sigID + ' td.age span').qtip('destroy');
	$('#sig-' + sigData.sigID + ' td.desc').qtip('destroy');

	$('#sig-' + sigData.sigID).remove();
}

siggy2.SigTable.prototype.setupHandlebars = function()
{
	var $this = this;


	Handlebars.registerHelper('sigTypeToText', function(type) {
		return $this.convertType(type);
	});

	Handlebars.registerHelper('siteIDToText', function(sysClass, type, siteID) {
		return $this.convertSiteID(sysClass, type, siteID);
	});


}

siggy2.SigTable.prototype.addSigRow = function (sigData, flashSig)
{
	var $this = this;
	sigData.showSigSizeCol = this.settings.showSigSizeCol;
	sigData.sysClass = this.systemClass;

	var row = this.templateSigRow(sigData);
	$("#sig-table tbody").append( row );

	this.sigRowMagic(sigData);

	if( flashSig )
	{
		$('#sig-' + sigData.sigID).fadeOutFlash("#A46D00", 20000);
	}
}

siggy2.SigTable.prototype.sigRowMagic = function(sigData)
{
	if( typeof(this.sigClocks[sigData.sigID]) != 'undefined' )
		this.sigClocks[sigData.sigID].destroy();
	delete this.sigClocks[sigData.sigID];

	if( typeof(this.eolClocks[sigData.sigID]) != 'undefined' )
		this.eolClocks[sigData.sigID].destroy();

	delete this.eolClocks[sigData.sigID];
	this.sigClocks[sigData.sigID] = new siggy2.Timer(sigData.created * 1000, null, '#sig-' + sigData.sigID + ' td.age span.age-clock', "test");

	var wh = null;
	if( sigData.type == 'wh' )
	{
		wh = siggy2.StaticData.getWormholeByID(sigData.siteID);

		if( wh != null )
		{
			var endDate = parseInt(sigData.created)+(3600*wh.lifetime);
			this.eolClocks[sigData.sigID] = new siggy2.Timer(sigData.created * 1000, endDate* 1000, '#sig-' + sigData.sigID + ' td.age p.eol-clock', "test");
		}
	}

	$('#sig-' + sigData.sigID + ' td.moreinfo i').qtip('destroy');
	$('#sig-' + sigData.sigID + ' td.moreinfo i').qtip({
		content: {
			text: $('#creation-info-' + sigData.sigID) // Use the "div" element next to this for the content
		}
	});

	$('#sig-' + sigData.sigID + ' td.age span').qtip('destroy');
	$('#sig-' + sigData.sigID + ' td.age span').qtip({
		content: {
			text: $('#age-timestamp-' + sigData.sigID) // Use the "div" element next to this for the content
		}
	});

	$('#sig-' + sigData.sigID + ' td.desc').qtip('destroy');

	var desc_tooltip = '';
	if( sigData.type == 'wh' )
	{
		if( wh != null )
		{
			desc_tooltip = siggy2.StaticData.templateWormholeInfoTooltip(wh);
		}
	}
	else if( sigData.siteID != 0 )
	{
		var site = siggy2.StaticData.getSiteByID(sigData.siteID);


		if( site != null && site.description != "" )
		{
			desc_tooltip = siggy2.StaticData.templateSiteTooltip( site );
		}
	}

	if( desc_tooltip != '' )
	{
		$('#sig-' + sigData.sigID + ' td.desc').qtip({
			content: {
				text: desc_tooltip
			},
			position: {
				target: 'mouse',
				adjust: { x: 5, y: 5 },
				viewport: $(window)
			}
		});
	}
}

siggy2.SigTable.prototype.editSigForm = function (sigID)
{
	if (this.editingSig)
	{
		return;
	}

	/*disable the tooltip or it will be annoying */
	$('#sig-' + sigID + ' td.desc').qtip('disable');

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
											gas: 'Gas',
											data: 'Data',
											relic: 'Relic',
											anomaly: 'Combat',
											ore: 'Ore'
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

siggy2.SigTable.prototype.editTypeSelectChange = function (sigID)
{
	var newType = $("#sig-" + sigID + " td.type select").val();

	this.updateSiteSelect( '#sig-' + sigID + ' td.desc select', this.systemClass, newType, 0 );
}

siggy2.SigTable.prototype.editSig = function (sigID)
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

	},"json").fail( function(xhr, textStatus, errorThrown) { alert('Error') } );

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

siggy2.SigTable.prototype.generateSiteSelect = function (whClass, type, siteID)
{
	if (type == 'wh')
	{
		return this.generateSelect(siggy2.StaticData.getWormholesForList(whClass), siteID);
	}
	else if (type == 'gas' || type == 'relic' || type == 'ore' || type == 'data' || type == 'anomaly')
	{
		return this.generateSelect(siggy2.StaticData.getSiteList(type,whClass), siteID);
	}
	else
	{
		return this.generateSelect({
										0: '--'
									}, 0);
	}
}


siggy2.SigTable.prototype.generateOrderedSelect = function (options, select)
{
	var newSelect = $('<select>').addClass('siggy-input');

	for (var i=0; i < options.length; i++ )
	{
		newSelect.append($('<option>').attr('value', options[i][0]).text(options[i][1]));
	}

	newSelect.val(select);

	return newSelect;
}

siggy2.SigTable.prototype.generateSelect = function (options, select)
{
	var newSelect = $('<select>').addClass('siggy-input');

	for (var i in options)
	{
		newSelect.append($('<option>').attr('value', i).text(_(options[i])));
	}

	newSelect.val(select);

	return newSelect;
}

siggy2.SigTable.prototype.updateSigRow = function (sigData, flashSig)
{
	var baseID = '#sig-' + sigData.sigID;
	sigData.showSigSizeCol = this.settings.showSigSizeCol;
	sigData.sysClass = this.systemClass;

	var row = this.templateSigRow(sigData);
	$(baseID).replaceWith(row);


	this.sigRowMagic(sigData);
}
