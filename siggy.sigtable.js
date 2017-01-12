/*
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

/**
* @constructor
*/
siggy2.SigTable = function( core, map, options )
{
	this.sigData = {};
	this.sigClocks = {};
	this.eolClocks = {};
	this.siggyMain = core;
	this.systemID = 0;
	this.systemClass = 0;
	this.editingSig = false;

	this.defaults = {
		showSigSizeCol: false,
		baseUrl:''
	};

	this.map = map;

	this.sigFilters = this.siggyMain.displayStates.sigFilters;

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


	$( '#sig-filter .dropdown-menu a' ).each(function(){
		var $target = $( this ),
			val = $target.attr( 'data-value' ),
			$inp = $target.find( 'input' ),
			idx;
		if ( typeof($this.sigFilters[val]) != 'undefined' )
		{
			setTimeout( function() { $inp.prop( 'checked', $this.sigFilters[val] ) }, 0);
		}
	});

	$( '#sig-filter .dropdown-menu a' ).on( 'click', function( event ) {

		var $target = $( event.currentTarget ),
			val = $target.attr( 'data-value' ),
			$inp = $target.find( 'input' ),
			idx;

		if ( typeof($this.sigFilters[val]) != 'undefined' && $this.sigFilters[val])
		{
			$this.sigFilters[val] = false;
			setTimeout( function() { $inp.prop( 'checked', false ) }, 0);
		}
		else
		{
			$this.sigFilters[val] = true;
			setTimeout( function() { $inp.prop( 'checked', true ) }, 0);
		}

		$( event.target ).blur();

		$this.updateSigFiltering();
		$this.updateSigTotal();

		$this.siggyMain.saveDisplayState();

		return false;
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

	$('#mass-add-sig-box form').on('submit', function(e)
	{
		e.preventDefault();

		var data = $('#mass-add-sig-box form').serializeObject();
		$this.massAddHandler($this.systemID, data);

		massAddBlob.val('');

		$('#mass-add-sig-box input[name=delete_nonexistent_sigs]').prop('checked', false);

		$.unblockUI();
	});

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
			$this.massAddHandler($this.systemID,{blob: $('#sig-add-box textarea[name=mass_sigs]').val()});
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

	$.ajax({
			type: 'post',
			url: $this.settings.baseUrl + 'sig/add',
			data: JSON.stringify(postData),
			contentType: 'application/json',
			success: function (newSig)
					{
						for (var i in newSig)
						{
							$this.addSigRow(newSig[i]);
						}
						$.extend($this.sigData, newSig);
						$('#sig-table').trigger('update');
					},
			dataType: 'json'
		});

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

	data.systemID = systemID;

	$.post( $this.settings.baseUrl + 'sig/mass_add', data,
	function (newSig)
	{
		for (var i in newSig)
		{
			$this.addSigRow(newSig[i]);
		}

		$.extend($this.sigData, newSig);
		$('#sig-table').trigger('update');

		//trigger the table to update...hackishly
		if(data.delete_nonexistent_sigs)
		{
			$(document).trigger('siggy.updateRequested', true );
		}
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

siggy2.SigTable.prototype.updateSigFiltering = function()
{
	for (var type in this.sigFilters)
	{
		var visible = this.sigFilters[type];

		if( visible )
		{
			$('#sig-table tbody tr.type-'+type).show();
		}
		else
		{
			$('#sig-table tbody tr.type-'+type).hide();
		}
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

siggy2.SigTable.prototype.whHashToDestination = function (hash)
{
	if(typeof(this.map.wormholes[hash]) == "undefined")
	{
		return "ERROR";
	}

	var mapWh = this.map.wormholes[hash];

	var system = null;
	if(mapWh.to_system_id != this.systemID)
	{
		system = siggy2.StaticData.getSystemByID(mapWh.to_system_id);
	}
	else if(mapWh.from_system_id != this.systemID)
	{
		system = siggy2.StaticData.getSystemByID(mapWh.from_system_id);
	}

	return this.systemToDestinationString(system);
}

siggy2.SigTable.prototype.systemToDestinationString = function (system)
{
	var displayName = "";
	if( typeof( this.map.systems[system.id] ) != "undefined" )
		displayName = this.map.systems[ system.id ].displayName == '' ? system.name : this.map.systems[ system.id ].displayName;

	return _('to {0} ({1}) - {2}').format(displayName, system.region_name, siggy2.Helpers.systemClassMediumText(system.class));
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

	this.updateSigFiltering();
	this.updateSigTotal();
}

siggy2.SigTable.prototype.updateSigTotal = function()
{
	$('#number-sigs').text(	$('#sig-table tr.sig:visible').length );
	$('#total-sigs').text(	$('#sig-table tr.sig').length );
}

siggy2.SigTable.prototype.removeSig = function (id)
{
	var sigData = this.sigData[ id ];

	if( sigData.editing )
	{
		return;
	}

	this.removeSigRow(
	{
		id: id
	});
	$('#sig-table').trigger('update');
	this.updateSigTotal();

	$.post(this.settings.baseUrl + 'sig/remove', {
		systemID: this.systemID,
		id: id
	});
}

siggy2.SigTable.prototype.setupSiteSelectForNewSystem = function(systemClass)
{
	$('#sig-add-box select[name=type]').val(0);
	this.updateSiteSelect('#sig-add-box select[name=site]',systemClass, 0, 0);
}

siggy2.SigTable.prototype.updateSiteSelect = function( ele, whClass, type, siteID )
{
	var elem = $( ele );
	/* use common select generator method and just swap contents */
	var newSel = this.generateSiteSelect( whClass, type, siteID );

	elem.empty().html(newSel.html());

	return newSel;
}

siggy2.SigTable.prototype.removeSigRow = function (sigData)
{
	if(this.sigClocks[sigData.id] != undefined )
	{
		this.sigClocks[sigData.id].destroy();
		delete this.sigClocks[sigData.id];
	}

	$('#sig-' + sigData.id + ' td.moreinfo img').qtip('destroy');
	$('#sig-' + sigData.id + ' td.age span').qtip('destroy');
	$('#sig-' + sigData.id + ' td.desc').qtip('destroy');

	$('#sig-' + sigData.id).remove();
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

	Handlebars.registerHelper('whHashToDestination', function(hash) {
		return $this.whHashToDestination(hash);
	});
}

siggy2.SigTable.prototype.addSigRow = function (sigData, flashSig)
{
	var $this = this;
	sigData.showSigSizeCol = this.settings.showSigSizeCol;
	sigData.sysClass = this.systemClass;

	sigData.showWormhole = false;
	if(sigData.type == 'wh' && typeof(sigData.chainmap_wormholes) != "undefined")
	{
		var cid = $this.siggyMain.activities.siggy.chainMapID;
		if(typeof(sigData.chainmap_wormholes[cid]) != "undefined")
		{
			sigData.showWormhole = true;

			sigData.chainmap_wormhole = sigData.chainmap_wormholes[cid];
		}
	}

	sigData.sysClass = this.systemClass;

	var row = this.templateSigRow(sigData);
	$("#sig-table tbody").append( row );

	this.sigRowMagic(sigData);

	if( flashSig )
	{
		$('#sig-' + sigData.id).fadeOutFlash("#A46D00", 20000);
	}
}

siggy2.SigTable.prototype.sigRowMagic = function(sigData)
{
	if( typeof(this.sigClocks[sigData.id]) != 'undefined' )
		this.sigClocks[sigData.id].destroy();

	delete this.sigClocks[sigData.id];

	if( typeof(this.eolClocks[sigData.id]) != 'undefined' )
		this.eolClocks[sigData.id].destroy();

	delete this.eolClocks[sigData.id];
	this.sigClocks[sigData.id] = new siggy2.Timer(sigData.created_at, null, '#sig-' + sigData.id + ' td.age span.age-clock', "test");

	var wh = null;
	if( sigData.type == 'wh' )
	{
		wh = siggy2.StaticData.getWormholeByID(sigData.siteID);

		if( wh != null )
		{
			var endDate = moment.utc(sigData.created_at);
			endDate.add(wh.lifetime,'hours');
			this.eolClocks[sigData.id] = new siggy2.Timer(sigData.created_at, endDate.format(), '#sig-' + sigData.id + ' td.age p.eol-clock', "test");
		}
	}

	$('#sig-' + sigData.id + ' td.moreinfo i').qtip('destroy');
	$('#sig-' + sigData.id + ' td.moreinfo i').qtip({
		content: {
			text: $('#creation-info-' + sigData.id) // Use the "div" element next to this for the content
		}
	});

	$('#sig-' + sigData.id + ' td.age span').qtip('destroy');
	$('#sig-' + sigData.id + ' td.age span').qtip({
		content: {
			text: $('#age-timestamp-' + sigData.id) // Use the "div" element next to this for the content
		}
	});

	$('#sig-' + sigData.id + ' td.desc').qtip('destroy');

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
		$('#sig-' + sigData.id + ' td.desc').qtip({
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

siggy2.SigTable.prototype.editSigForm = function (id)
{
	if (this.editingSig)
	{
		return;
	}

	var sigData = this.sigData[id];

	/*disable the tooltip or it will be annoying */
	$('#sig-' + id + ' td.desc').qtip('disable');

	sigData.editing = true;
	this.editingSig = true;

	var controlEle = $("#sig-" + id + " td.edit");
	controlEle.empty();

	var $this = this;
	controlEle.append($('<img>').attr('src', this.settings.baseUrl + 'images/accept.png').click(function (e)
	{
		$this.editSig(id)
	}));

	var sigEle = $("#sig-" + id + " td.sig");
	sigEle.empty();

	var sigInput = $('<input>').val(sigData.sig)
							.attr('maxlength', 3)
							.keypress( function(e) { if(e.which == 13){ $this.editSig(id)  } } );
	sigEle.append(sigInput);

	if( this.settings.showSigSizeCol )
	{
		var sizeEle = $("#sig-" + id + " td.size");
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
													], sigData.sigSize));
	}

	var typeEle = $("#sig-" + id + " td.type");
	typeEle.empty();

	typeEle.append(this.generateSelect({
											none: '--',
											wh: 'WH',
											gas: 'Gas',
											data: 'Data',
											relic: 'Relic',
											anomaly: 'Combat',
											ore: 'Ore'
										}, sigData.type)
										.change(function ()
												{
													$this.editTypeSelectChange(id)
												}));


	var descEle = $('#sig-' + id + ' td.desc');
	descEle.empty();

	var siteSelect = this.generateSiteSelect(this.systemClass, sigData.type, sigData.siteID);
	descEle.append(siteSelect);

	siteSelect.change( function() {
		$this.editSiteChanged(id);
	});

	if( sigData.type == 'wh' )
	{
		siteSelect.css('width','50%');
		var whSelect = this.generateMappedWormholeSelect(sigData);
		descEle.append(whSelect)
		whSelect.css('width','50%');
	}

	descEle.append($('<br />'))

	descEle.append( $('<input>').val(sigData.description)
								.keypress( function(e) { if(e.which == 13){ $this.editSig(id)  } } )
								.css('width', '100%')
					);


	sigInput.focus();
}

siggy2.SigTable.prototype.generateMappedWormholeSelect = function( sigData )
{
	var cid = this.siggyMain.activities.siggy.chainMapID;
	var selected = 'none';
	if(typeof(sigData.chainmap_wormholes) != "undefined" &&
		typeof(sigData.chainmap_wormholes[cid]) != "undefined")
	{
		selected = sigData.chainmap_wormholes[cid];
	}

	var siteEle = $("#sig-" + sigData.id + " td.desc select[name=site]");
	var wormholeType = parseInt(siteEle.val());

	var whs = { none : '--' };
	for(var i in this.map.wormholes)
	{
		var mapWh = this.map.wormholes[i];

		//figure out which system
		mapWh.from_system_id = parseInt(mapWh.from_system_id);
		mapWh.to_system_id = parseInt(mapWh.to_system_id);

		if(mapWh.from_system_id != this.systemID &&
			mapWh.to_system_id != this.systemID)
		{
			continue;
		}

		var system = null;
		if( mapWh.from_system_id != this.systemID )
		{
			system = siggy2.StaticData.getSystemByID(mapWh.from_system_id);
		}
		else if( mapWh.to_system_id != this.systemID )
		{
			system = siggy2.StaticData.getSystemByID(mapWh.to_system_id);
		}

		if( wormholeType != 0 ) // 0 is unstable wormhole, allows all
		{
			if(wormholeType > 6)
			{
				var wormholeData = siggy2.StaticData.getWormholeByID(wormholeType);

				if( system.class != wormholeData.dest_class )
					continue;
			}
			else
			{
				if( wormholeType == 1 )	//unknown
				{
					if( !( system.class >= 1 && system.class <= 3) )
					{
						continue;
					}
				}
				else if( wormholeType == 2 ) //dangerous
				{
					if( !( system.class >= 4 && system.class <= 5) )
					{
						continue;
					}
				}
				else if( wormholeType == 3 ) //deadly
				{
					if( !( system.class == 6) )
					{
						continue;
					}
				}
				else if( wormholeType == 4 ) //null
				{
					if( !( system.class == 9) )
					{
						continue;
					}
				}
				else if( wormholeType == 5 ) //low
				{
					if( !( system.class == 8) )
					{
						continue;
					}
				}
				else if( wormholeType == 6 ) //high
				{
					if( !( system.class == 7) )
					{
						continue;
					}
				}
			}
		}

		whs[mapWh.hash] = this.systemToDestinationString(system);
	}

	return this.generateSelect(whs, selected).attr('name', 'chainmap-wh');;
}

siggy2.SigTable.prototype.editTypeSelectChange = function (id)
{
	var newType = $("#sig-" + id + " td.type select").val();

	var $this = this;
	var siteSelect = this.updateSiteSelect( '#sig-' + id + ' td.desc select', this.systemClass, newType, 0 );
	siteSelect.change( function() {
		$this.editSiteChanged(id);
	});

	$("#sig-" + id + " td.desc select[name=chainmap-wh]").remove();

	if(newType == 'wh')
	{
		$("#sig-" + id + " td.desc select").after(this.generateMappedWormholeSelect(this.sigData[id]).css('width','50%'));
	}
}

siggy2.SigTable.prototype.editSig = function (id)
{
	var $this = this;
	var sigEle = $("#sig-" + id + " td.sig input");

	var sizeEle = null;
	if( this.settings.showSigSizeCol )
	{
		sizeEle = $("#sig-" + id + " td.size select");
	}

	var typeEle = $("#sig-" + id + " td.type select");
	var descEle = $("#sig-" + id + " td.desc input");
	var siteEle = $("#sig-" + id + " td.desc select");
	var whChainmapEle = $("#sig-" + id + " td.desc select[name=chainmap-wh]");

	if (sigEle.val().length != 3)
	{
		return false;
	}

	var sigUpdate = {};
	var sigObj = this.sigData[id];
	sigObj.sig = sigEle.val().toUpperCase();
	sigObj.type = typeEle.val();
	sigObj.siteID = siteEle.val();
	sigObj.description = descEle.val();

	var cmWh = {};
	if(sigObj.type =='wh')
	{
		var hash = whChainmapEle.val();

		if(hash == null)
		{
			hash = "none";
		}

		cmWh = {
			hash: hash,
			chainmap_id: $this.siggyMain.activities.siggy.chainMapID
		}

		sigObj.chainmap_wormholes = {};

		if(cmWh.hash != 'none')
		{
			sigObj.chainmap_wormholes[cmWh.chainmap_id] = cmWh.hash;
		}
	}

	var postData = {
		id: id,
		sig: sigObj.sig,
		type: sigObj.type,
		desc: sigObj.description,
		siteID: sigObj.siteID,
		systemID: this.systemID,
		chainmap_wormhole: cmWh
	};

	if( this.settings.showSigSizeCol )
	{
		sigObj.sigSize = sizeEle.val();
		postData.sigSize = sigObj.sigSize;
	}

	$.ajax({
			type: 'post',
			url: $this.settings.baseUrl + 'sig/edit',
			data: JSON.stringify(postData),
			contentType: 'application/json',
			dataType: 'json'
		})
		.fail(function(){
			alert('Error saving sig');
		})
		.always(function(){
			$this.editingSig = false;
			sigObj.editing = false;
		});

	sigEle.remove();
	if( this.settings.showSigSizeCol )
	{
		sizeEle.remove();
	}
	typeEle.remove();
	descEle.remove();
	siteEle.remove();

	this.updateSigRow(sigObj);

	var controlEle = $("#sig-" + id + " td.edit");
	controlEle.text('');
	controlEle.append($('<i>').addClass('icon icon-pencil icon-large')
							.click(function (e)
									{
										$this.editSigForm(id)
									})
								);

}

siggy2.SigTable.prototype.editSiteChanged = function (id)
{
	var newType = $("#sig-" + id + " td.type select").val();

	if(newType == 'wh')
	{
		$("#sig-" + id + " td.desc select[name=chainmap-wh]").remove();

		$("#sig-" + id + " td.desc select[name=site]").after(this.generateMappedWormholeSelect(this.sigData[id]).css('width','50%'));
	}
}

siggy2.SigTable.prototype.generateSiteSelect = function (whClass, type, siteID)
{
	if (type == 'wh')
	{
		var t = this.generateSelect(siggy2.StaticData.getWormholesForList(whClass), siteID).attr('name', 'site');

		return t;
	}
	else if (type == 'gas' || type == 'relic' || type == 'ore' || type == 'data' || type == 'anomaly')
	{
		return this.generateSelect(siggy2.StaticData.getSiteList(type,whClass), siteID).attr('name', 'site');
	}
	else
	{
		var t = this.generateSelect({
										0: '--'
									}, 0);
		return t.attr('name', 'site');
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

siggy2.SigTable.prototype.generateSelect = function (options, selected, extraClass)
{
	var newSelect = $('<select>').addClass('siggy-input');

	if(typeof(extraClass) != "undefined")
	{
		newSelect.addClass(extraClass);
	}

	for (var i in options)
	{
		newSelect.append($('<option>').attr('value', i).text(_(options[i])));
	}

	newSelect.val(selected);

	return newSelect;
}

siggy2.SigTable.prototype.updateSigRow = function (sigData, flashSig)
{
	var $this = this;
	var baseID = '#sig-' + sigData.id;

	sigData.showSigSizeCol = this.settings.showSigSizeCol;
	sigData.sysClass = this.systemClass;

	sigData.showWormhole = false;
	if(sigData.type == 'wh' && typeof(sigData.chainmap_wormholes) != "undefined")
	{
		var cid = $this.siggyMain.activities.siggy.chainMapID;
		if(typeof(sigData.chainmap_wormholes[cid]) != "undefined")
		{
			sigData.showWormhole = true;

			sigData.chainmap_wormhole = sigData.chainmap_wormholes[cid];
		}
	}

	var row = this.templateSigRow(sigData);
	$(baseID).replaceWith(row);


	this.sigRowMagic(sigData);
}
