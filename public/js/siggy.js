//when opened with system in url
if (typeof(CCPEVE) != "undefined")
{
	CCPEVE.requestTrust('http://siggy.borkedlabs.com/*');
}

/**
* @constructor
*/
function siggymain( options )
{
	this.fatalError = false;
	this.ajaxErrors = 0;

	this.systemID = 0;
	this.systemClass = 9;
	this.systemName = '';
	this.systemStats = [];
	this.freezeSystem = 0;
	this.lastUpdate = 0;
	this.sigData = {};
	this.editingSig = false;
	this.sigClocks = {};
	this.systemList = {};
	this.forceUpdate = true;
	this._updateTimeout = null;
	this.publicMode = false;
	this.map = null;
	this.acsid = 0;
	this.acsname = '';

	this.chainMapID = 0;

	//gnotes
	this.globalNotesEle = null;
	this._blinkNotesInterval = null;
	this.globalNotes = '';
	this.editingGlobalNotes = false;

	this.groupCacheTime = 0;

	this.defaults = {
		baseUrl: '',
		initialSystemID: 0,
		initialSystemName: '',
		showSigSizeCol: false,
		sessionID: '',
		map: {
		  jumpTrackerEnabled: true
		}
	};

	this.settings = $.extend(this.defaults, options);

	this.defaultDisplayStates = {
		statsOpen: false,
		sigsAddOpen: true
	}

	this.displayStates = this.defaultDisplayStates;

	this.systemName = this.settings.initialSystemName;
    this.setSystemID(this.settings.initialSystemID);

	/* POSes */
	this.poses = {};
}


siggymain.prototype.initialize = function ()
{
	var that = this;
	this.setupFatalErrorHandler();

	$(document).ajaxStart( function() {
		$(this).show();
	});

	$(document).ajaxStop( function() {
		$(this).hide();
	} );

	// Display states cookie
	var displayStatesCookie = getCookie('display_states');
	var dispStates = '';
	if( displayStatesCookie != null )
	{
		dispStates = $.parseJSON(displayStatesCookie);
	}
	this.displayStates = $.extend(this.defaultDisplayStates, dispStates);

	// Initialize map
	this.map = new siggyMap(this.settings.map);
	this.map.baseUrl = this.settings.baseUrl;
	this.map.siggymain = this;
	this.map.initialize();

	this.initializeGNotes();

	this.forceUpdate = true;
	this.update();
	$(document).trigger('siggy.switchSystem', this.systemID );

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
		that.colorizeSigRows();
	});

	this.setupAddBox();

	$('#system-options-save').click(function ()
	{
		var label = $('#system-options input[name=label]').val();
		var activity = $('#system-options select[name=activity]').val();

		that.saveSystemOptions(that.systemID, label, activity);
	});

	$('#system-options-reset').click(function ()
	{
		$('#system-options input[name=label]').val('');
		$('#system-options select[name=activity]').val(0);

		$.post(that.settings.baseUrl + 'dosaveSystemOptions', {
			systemID: that.systemID,
			label: '',
			inUse: 0,
			activity: 0
		}, function (data)
		{
			if (that.systemList[that.systemID])
			{
				that.systemList[that.systemID].displayName = '';
				that.systemList[that.systemID].inUse = 0;
				that.systemList[that.systemID].activity = 0;
			}
		});
	});

	$('#bear-C1').click(function() { that.setBearTab(1); return false; });
	$('#bear-C2').click(function() { that.setBearTab(2); return false; });
	$('#bear-C3').click(function() { that.setBearTab(3); return false; });
	$('#bear-C4').click(function() { that.setBearTab(4); return false; });
	$('#bear-C5').click(function() { that.setBearTab(5); return false; });
	$('#bear-C6').click(function() { that.setBearTab(6); return false; });

	this.initializeCollaspibles();
	this.initializeTabs();

	this.initializeDScan();
	this.initializePOSes();

	this.initializeExitFinder();
}

siggymain.prototype.initializeCollaspibles = function()
{
	var $this = this;
	var systemStatsContent = $('#system-stats > div');
	var sigAddContent = $('#sig-add-box > div');

	if( $this.displayStates.statsOpen )
	{
		systemStatsContent.show();
	}
	else
	{
		systemStatsContent.hide();
	}

	if( $this.displayStates.sigsAddOpen )
	{
		sigAddContent.show();
	}
	else
	{
		sigAddContent.hide();
	}

	$('#system-stats h2').click( function() {
		if( systemStatsContent.is(":visible") )
		{
			systemStatsContent.hide();
			$this.displayStates.statsOpen = false;
			$this.saveDisplayState();
		}
		else
		{
			systemStatsContent.show();
			$this.renderStats();
			$this.displayStates.statsOpen = true;
			$this.saveDisplayState();
		}
	});

	$('#sig-add-box h2').click( function() {
		if( sigAddContent.is(":visible") )
		{
			sigAddContent.hide();
			$this.displayStates.sigsAddOpen = false;
			$this.saveDisplayState();
		}
		else
		{
			sigAddContent.show();
			$this.displayStates.sigsAddOpen = true;
			$this.saveDisplayState();
		}
	});
}

siggymain.prototype.saveDisplayState = function()
{
	setCookie('display_states', JSON.stringify(this.displayStates), 365);
}


siggymain.prototype.getCurrentTime = function ()
{
	var date = new Date();
	var time = pad(date.getUTCHours(), 2) + ':' + pad(date.getUTCMinutes(), 2) + ':' + pad(date.getUTCSeconds(), 2);

	delete date;

	return time;
}

/**
 * Renders a ISO8601 date + time from a unix timestamp. Except instead of the T
 * in between we have a space.
 */
siggymain.displayTimeStamp = function (unixTimestamp)
{
	var date = new Date(unixTimestamp * 1000);

	var day = pad(date.getUTCDate(), 2);
	var month = pad(date.getUTCMonth() + 1, 2);
	var year = date.getUTCFullYear().toString();

	var hours = pad(date.getUTCHours(), 2);
	var minutes = pad(date.getUTCMinutes(), 2);
	var seconds = pad(date.getUTCSeconds(), 2);

	var time = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;

	delete date;

	return time;
}

siggymain.prototype.update = function ()
{
	var request = {
		systemID: this.systemID,
		lastUpdate: this.lastUpdate,
		group_cache_time: this.groupCacheTime,
		systemName: this.systemName,
		freezeSystem: this.freezeSystem,
		acsid: this.acsid,
		acsname: this.acsname,
		mapOpen: this.map.isMapOpen(),
		mapLastUpdate: this.map.lastUpdate,
		forceUpdate: this.forceUpdate
	};

	var that = this;
	$.ajax({
		url: this.settings.baseUrl + 'update',
		data: request,
		dataType: 'json',
        cache: false,
        async: true,
		method: 'post',
		beforeSend : function(xhr, opts){
			if(that.fatalError == true) //just an example
			{
				xhr.abort();
			}
		},
		success: function (data)
			{
                //try
                //{
					if( data.redirect != undefined )
					{
						window.location = that.settings.baseUrl + data.redirect;
						return;
					}

					that.chainMapID = parseInt(data.chainmap_id);

					if( parseInt( data.acsid ) != 0 )
					{
						that.acsid = data.acsid;
					}
					if( data.acsname != '' )
					{
						that.acsname = data.acsname;
						$('#acsname b').text(that.acsname);
					}
					if (data.systemUpdate)
					{
						that.updateSystemInfo(data.systemData);
						that.updateSystemOptionsForm(data.systemData);
					}
					if (data.sigUpdate)
					{
						var flashSigs = ( data.systemUpdate ? false : true );
						that.updateSigs(data.sigData, flashSigs);
					}
					if (data.systemListUpdate)
					{
						that.systemList = data.systemList;
					}

					if(data.chainmaps_update)
					{
						that.updateChainMaps(data.chainmaps);
					}

					if (data.globalNotesUpdate)
					{
						if (!that.editingGlobalNotes)
						{

							if( getCookie('notesUpdate') != null )
							{
								var nlu = parseInt(getCookie('notesUpdate'));
							}
							else
							{
								var nlu = that.group_cache_time;
							}

							if( !that.globalNotesEle.is(':visible') && data.group_cache_time > nlu && nlu != 0 )
							{
								that.blinkNotes();
							}

							that.groupCacheTime = data.group_cache_time;

							setCookie('notesUpdate', data.group_cache_time, 365);

							that.globalNotes = data.globalNotes;
							$('#global-notes-content').html(that.globalNotes.replace(/\n/g, '<br />'));
							$('#global-notes-time').text( siggymain.displayTimeStamp(that.groupCacheTime) );
						}
					}

					if( that.map.isMapOpen()  )
					{
						if( parseInt(data.mapUpdate) == 1  )
						{
							//use temp vars or else chrome chokes badly with async requests
							var timestamp = data.chainMap.lastUpdate;
							var systems = data.chainMap.systems;
							var whs = data.chainMap.wormholes;
							that.map.update(timestamp, systems, whs);
						}
						if( typeof(data.chainMap) != 'undefined' && typeof(data.chainMap.actives) != '' )
						{
							var actives =  data.chainMap.actives;
							that.map.updateActives(data.chainMap.actives);
						}
					}

					that.lastUpdate = data.lastUpdate;
					//  $.unblockUI();

					delete data;
                //}
                //catch(err)
               // {
                //    console.log(err.message);
                //}
			}
		});



	this.forceUpdate = false;
	$('span.updateTime').text(this.getCurrentTime());

	this._updateTimeout = setTimeout(function (thisObj)
	{
		thisObj.update(0)
	}, 10000, this);

	return true;
}

siggymain.prototype.registerSwitchHandler = function (item, systemID, systemName)
{
	var that = this;
	item.click(function ()
	{
		//$.blockUI({ message: '<h1 style="font-size:1.2em;"><strong>Loading...</strong></h1>' });
		that.switchSystem(systemID, systemName);

	});
}

siggymain.prototype.updateNow = function()
{
	clearTimeout(this._updateTimeout);
	return this.update();
}

siggymain.prototype.switchSystem = function(systemID, systemName)
{
	this.setSystemID(systemID);
	this.systemName = systemName;
	this.forceUpdate = true;
	this.freeze();
	clearTimeout(this._updateTimeout);
	for(var i in this.sigClocks)
	{
		this.sigClocks[i].destroy();
		delete clock;
	}

    $('td.moreinfo img').qtip('destroy');
    $('td.age span').qtip('destroy');

	$("#sig-table tbody").empty();
	this.editingSig = false;
	this.sigData = {};

	$('#sig-add-box select[name=type]').val(0);
	this.updateSiteSelect('#sig-add-box select[name=site]',this.systemClass, 0, 0);

	if( this.updateNow() )
	{
		$(document).trigger('siggy.switchSystem', systemID );
	}
}

siggymain.prototype.updateSigs = function (sigData, flashSigs)
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

siggymain.prototype.updateSystemInfo = function (systemData)
{
	//general info
	$('#region').text(systemData.regionName + " / " + systemData.constellationName);
	$('#constellation').text(systemData.constellationName);
	$('#planetsmoons').text(systemData.planets + "/" + systemData.moons + "/" + systemData.belts);
	$('#truesec').text(systemData.truesec.substr(0,8));
	$('#radius').text(systemData.radius + ' AU');

	//HUB JUMPS
	var hubJumpsStr = '';
	$('div.hub-jump').destroyContextMenu();
	$('#hub-jumps').empty();
	for(var index in systemData.hubJumps)
	{
		var hub = systemData.hubJumps[index];

		var hubDiv = $("<div>").addClass('hub-jump')
							   .text(hub.destination_name + " (" + hub.num_jumps + " jumps)")
							   .data("sysID", hub.system_id)
							   .data("sysName", hub.destination_name);

		hubDiv.contextMenu( { menu: 'system-simple-context' },
			function(action, el, pos) {
				var sysID = $(el[0]).data("sysID");
				var sysName  = $(el[0]).data("sysName");
				if( action == "setdest" )
				{
					if( typeof(CCPEVE) != "undefined" )
					{
						CCPEVE.setDestination(sysID);
					}
				}
				else if( action == "showinfo" )
				{
					if( typeof(CCPEVE) != "undefined" )
					{
							CCPEVE.showInfo(5, sysID );
					}
					else
					{
							window.open('http://evemaps.dotlan.net/system/'+sysName , '_blank');
					}
				}
		});

		$('#hub-jumps').append(hubDiv);
	}

    //EFFECT STUFF
	//effect info
    $('#system-effect > p').qtip('destroy');
	$('#system-effect').empty();


	var effectTitle = $("<p>").text(systemData.effectTitle);
	var effect = $('#system-effect').append(effectTitle);
	var effectInfo = '';

	if( systemData.effectTitle != 'None' )
	{
		effectInfo += '<b>Class '+systemData.sysClass+' Effects</b><br /><br />';

		if( systemData.effectTitle == 'Black Hole' )
		{
			var effData = blackHoleEffects[systemData.sysClass];
		}
		else if(systemData.effectTitle == 'Wolf-Rayet Star')
		{
			var effData = wolfRayetEffects[systemData.sysClass];
		}
		else if(systemData.effectTitle == 'Red Giant')
		{
			var effData = redGiantEffects[systemData.sysClass];
		}
		else if(systemData.effectTitle == 'Cataclysmic Variable')
		{
			var effData = catacylsmicEffects[systemData.sysClass];
		}
		else if(systemData.effectTitle == 'Magnetar')
		{
			var effData = magnetarEffects[systemData.sysClass];
		}
		else if(systemData.effectTitle == 'Pulsar')
		{
			var effData = pulsarEffects[systemData.sysClass];
		}

		for( var i = 0; i < effData.length; i++ )
		{
			effectInfo += '<b>'+effData[i][0]+':&nbsp;</b>'+effData[i][1]+'<br />';
		}

		var tooltip = $("<div>").attr('id', 'system-effects')
								.addClass('tooltip')
								.html(effectInfo);
		effect.append(tooltip);

		effectTitle.qtip({
			content: {
				text: $("#system-effects") // Use the "div" element next to this for the content
			},
			position: {
				target: 'mouse',
				adjust: { x: 5, y: 5 },
				viewport: $(window)
			}
		});
	}

	//
	$('#static-info').empty();
	var staticCount = Object.size(systemData.staticData);
	if( staticCount > 0 )
	{
		var counter = 0;
		for (var i in systemData.staticData)
		{
			var theStatic = systemData.staticData[i];
			var destBlurb = '';
			theStatic.staticDestClass = Number(theStatic.staticDestClass);

			if (theStatic.staticDestClass <= 6)
			{
				destBlurb = " (to C" + theStatic.staticDestClass + ")";
			}
			else if (theStatic.staticDestClass == 7)
			{
				destBlurb = " (to Highsec)";
			}
			else if (theStatic.staticDestClass == 8)
			{
				destBlurb = " (to Lowsec)";
			}
			else
			{
				destBlurb = " (to Nullsec)";
			}

			var staticBit = $("<p>").text(theStatic.staticName + destBlurb);
			var staticInfo = "<b>" + theStatic.staticName  + destBlurb + "</b><br />" + "Max Mass: " + theStatic.staticMass + " billion<br />" + "Max Jumpable Mass: " + theStatic.staticJumpMass + " million<br />" + "Max Lifetime: " + theStatic.staticLifetime + " hrs<br />" + "Signature Size: " + theStatic.staticSigSize + " <br />";

			var staticTooltip = $("<div>").attr('id', 'static-info-' + theStatic.staticID)
										  .addClass('tooltip')
										  .html( staticInfo );

			$('#static-info').append(staticBit)
							 .append(staticTooltip);

			staticBit.qtip({
				content: {
					text: $('#static-info-' + theStatic.staticID) // Use the "div" element next to this for the content
				},
				position: {
					target: 'mouse',
					adjust: { x: 5, y: 5 },
					viewport: $(window)
				}
			});

			counter++;
		}
	}

	var sysName = systemData.name;

	if ( systemData.displayName != '' )
	{
		sysName += " (" + systemData.displayName + ")";
	}

	systemData.sysClass = parseInt(systemData.sysClass);

	if ( systemData.sysClass <= 6 )
	{
		sysName += " - [C" + systemData.sysClass + "]";
	}
	else if( systemData.sysClass <= 8 )
	{
		sysName += " - ["+systemData.sec+"]";
	}
	else
	{
		sysName += " - [0.0]";
	}
	$('#system-name').text(sysName);


	$('a.site-dotlan').attr('href', 'http://evemaps.dotlan.net/system/'+systemData.name);
	$('a.site-wormholes').attr('href', 'http://wh.pasta.gg/'+systemData.name);
	$('a.site-evekill').attr('href','http://eve-kill.net/?a=system_detail&sys_name='+systemData.name);

	this.setSystemID(systemData.id);
	this.setSystemClass(systemData.sysClass);
	this.systemName = systemData.name;

	$('#currentsystem b').text(this.systemName);

	if( systemData.stats.length > 0 )
	{
		this.systemStats = systemData.stats;
        this.renderStats();
	}
	else
	{
		this.systemStats = [];
	}

	this.updatePOSList( systemData.poses );
	this.updateDScan( systemData.dscans );
}

siggymain.prototype.renderStats = function()
{
		var options = {
			lines: { show: true },
			points: { show: false },
			xaxis: { mode: 'time',minTickSize: [1, 'hour'], ticks: 13, labelAngle: 45, color: '#fff' },
			yaxis: {color: '#fff', tickDecimals: 0}
		};


		var jumps = [];
		var sjumps = [];
		var kills = [];
		var npcKills = [];
		for( var i = 0; i < this.systemStats.length; i++ )
		{
			jumps.push([parseInt(this.systemStats[i][0]), parseInt(this.systemStats[i][1]) ] );
			sjumps.push([parseInt(this.systemStats[i][0]), parseInt(this.systemStats[i][4]) ] );
			kills.push([parseInt(this.systemStats[i][0]), parseInt(this.systemStats[i][2]) ] );
			npcKills.push([parseInt(this.systemStats[i][0]), parseInt(this.systemStats[i][3]) ] );
		}


		$.plot( $('#jumps'),  [
		{
			data: jumps,
			lines: { show: true, fill: true }
		},
		{
			data: sjumps,
			lines: { show: true, fill: true }
		 }], options);

		$.plot( $('#shipKills'),  [
		{
			data: kills,
			lines: { show: true, fill: true }
		}],options);
		$.plot( $('#npcKills'),  [
		{
			data: npcKills,
			lines: { show: true, fill: true }
		}],options);
}

siggymain.prototype.setBearTab = function( bearClass )
{
		$('#bear-class-links a').each(function(index)
		{
			if( $(this).text() == 'C'+bearClass )
			{
				$(this).addClass('active');
			}
			else
			{
				$(this).removeClass('active');
			}
		});
		$('#bear-info-sets div').each(function(index)
		{
			if( $(this).attr('id') == 'bear-class-'+bearClass )
			{
				$(this).show();
			}
			else
			{
				$(this).hide();
			}
		});
}



siggymain.prototype.updateSystemOptionsForm = function (systemData)
{
	$('#system-options table th').text('System Options for '+systemData.name);
	$('#system-options input[name=label]').val(systemData.displayName);
	$('#system-options select[name=activity]').val(systemData.activity);
}


siggymain.prototype.updateSigRow = function (sigData, flashSig)
{
	var creationInfo = '<b>Added by:</b> '+sigData.creator;
	if( sigData.lastUpdater != '' && typeof(sigData.lastUpdater) != "undefined" )
	{
		creationInfo += '<br /><b>Updated by:</b> '+sigData.lastUpdater;
		creationInfo += '<br /><b>Updated at:</b> '+siggymain.displayTimeStamp(sigData.updated);
	}

	$('#sig-' + sigData.sigID + ' td.sig').text(sigData.sig);
	$('#sig-' + sigData.sigID + ' td.type').text(this.convertType(sigData.type));

	if( this.settings.showSigSizeCol )
	{
			$('#sig-' + sigData.sigID + ' td.size').text(sigData.sigSize);
	}

	//stupidity part but ah well
	$('#sig-' + sigData.sigID + ' td.desc').text(this.convertSiteID(this.systemClass, sigData.type, sigData.siteID));
	$('#sig-' + sigData.sigID + ' td.desc p').remove();
	$('#sig-' + sigData.sigID + ' td.desc').append($('<p>').text(sigData.description));
	$('creation-info-' + sigData.sigID).html(creationInfo);


	//if( flashSig )
	///{
		//$('#sig-' + sigData.sigID).fadeOutFlash("#A46D00", 10000);
	//}
}

siggymain.prototype.removeSigRow = function (sigData)
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

siggymain.prototype.addSigRow = function (sigData, flashSig)
{
	var that = this;

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
	var row = $('<tr>').attr('id', 'sig-' + sigData.sigID)
						.append(editTD)
						.append($('<td>').addClass('center-text sig').text(sigData.sig));

	if( this.settings.showSigSizeCol )
	{
		row.append( $('<td>').addClass('center-text size').text(sigData.sigSize) );
	}

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
}


siggymain.prototype.editSigForm = function (sigID)
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

siggymain.prototype.editTypeSelectChange = function (sigID)
{
	var newType = $("#sig-" + sigID + " td.type select").val();
	if (this.sigData[sigID].type != newType)
	{
		this.updateSiteSelect( '#sig-' + sigID + ' td.desc select', this.systemClass, newType, 0 );
	}
}

siggymain.prototype.editSig = function (sigID)
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
	$.post(this.settings.baseUrl + 'dosigEdit', postData, function ( data )
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

siggymain.prototype.updateSiteSelect = function( ele, whClass, type, siteID )
{
	var elem = $( ele );
	elem.empty();

	var options = [];
	switch( type )
	{
		case 'wh':
			options = whLookup[whClass];
			break;
		case 'ladar':
			options = ladarsLookup;
			break;
		case 'mag':
			options = magsLookup[whClass];
			break;
		case 'grav':
			options = gravsLookup;
			break;
		case 'radar':
			options = radarsLookup[whClass];
			break;
		default:
			options = { 0: '--'};
			break;
	}

	for (var i in options)
	{
		elem.append($('<option>').attr('value', i).text(options[i]));
	}

	elem.val(siteID);
}

siggymain.prototype.generateSiteSelect = function (whClass, type, siteID)
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


siggymain.prototype.generateOrderedSelect = function (options, select)
{
	var newSelect = $('<select>');

	for (var i=0; i < options.length; i++ )
	{
		newSelect.append($('<option>').attr('value', options[i][0]).text(options[i][1]));
	}

	newSelect.val(select);

	return newSelect;
}

siggymain.prototype.generateSelect = function (options, select)
{
	var newSelect = $('<select>');

	for (var i in options)
	{
		newSelect.append($('<option>').attr('value', i).text(options[i]));
	}

	newSelect.val(select);

	return newSelect;
}

siggymain.prototype.removeSig = function (sigID)
{
	this.removeSigRow(
	{
		sigID: sigID
	});
	$('#sig-table').trigger('update');

	$.post(this.settings.baseUrl + 'dosigRemove', {
		systemID: this.systemID,
		sigID: sigID
	});
}

siggymain.prototype.convertType = function (type)
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

siggymain.prototype.convertSiteID = function (whClass, type, siteID)
{
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

siggymain.prototype.setSystemID = function (systemID)
{
	this.systemID = systemID;
}

siggymain.prototype.setSystemClass = function (systemClass)
{
	this.systemClass = systemClass;
	if( systemClass <= 6 )
	{
		this.setBearTab(systemClass);
	}
	else
	{
		this.setBearTab(1);
	}
}

siggymain.prototype.colorizeSigRows = function()
{
	var i = 0;
	$('#sig-table tbody tr').each( function() {
		$( this ).removeClass('alt');
		if( i % 2 != 0 )
			$( this ).addClass('alt');
		i++;
	});
}

siggymain.prototype.setupAddBox = function ()
{
	var $this = this;
	var massAddBlob = $('#mass-add-sig-box textarea[name=blob]');
	massAddBlob.val('');
	$('#mass-add-sig-box button[name=add]').click( function()
	{
		var postData = {
			systemID: $this.systemID,
			blob: massAddBlob.val()
		};

		$.post( $this.settings.baseUrl + 'domassSigs', postData, function (newSig)
		{
			for (var i in newSig)
			{
				$this.addSigRow(newSig[i]);
			}

			$.extend($this.sigData, newSig);
			$('#sig-table').trigger('update');
		}, 'json');

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

	$('#sig-add-box form').submit(function ()
	{
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

		$.post($this.settings.baseUrl + 'dosigAdd', postData, function (newSig)
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
		siteEle.replaceWith($('<select>').attr('name', 'site'));

		sigEle.focus();

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

siggymain.prototype.addBoxEnterHandler = function(e)
{
	if(e.which == 13)
	{
		$('button[name=add]').focus().click();
	}
}

siggymain.prototype.displayFatalError = function(message)
{
	$('#fatal-error-message').html(message);

	$.blockUI({
		message: $('#fatal-error'),
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
			centerY: true
		},
		overlayCSS: {
			cursor: 'auto'
		},
		fadeIn:  0,
		fadeOut:  0
	});
}

siggymain.prototype.setupFatalErrorHandler = function()
{
	var that = this;

	$(document).ajaxError( function(ev, jqxhr, settings, exception) {
		that.ajaxErrors += 1;
		if( that.ajaxErrors >= 5 )
		{
			that.displayFatalError('Communication error. ');
			that.fatalError = true;
		}
	} );

	$(document).ajaxSuccess( function() {
		that.ajaxErrors = 0;
	} );

	$('#fatal-error-refresh').click( function() {
		location.reload(true);
	} );
}

siggymain.prototype.saveSystemOptions = function(systemID, label, activity)
{
	var $this = this;
	$.post(that.settings.baseUrl + 'dosaveSystemOptions', {
		systemID: systemID,
		label: label,
		activity: activity
	},
	function (data)
	{
		if ($this.systemList[systemID])
		{
			$this.systemList[systemID].displayName = label;
			$this.systemList[systemID].activity = activity;
		}

		$this.forceUpdate = true;
		$this.updateNow();
	});
}

siggymain.prototype.initializeTabs = function()
{
	var $this = this;

    $('#system-advanced ul.tabs li a').click(function()
    {
        $this.changeTab( $(this).attr('href') );
        return false;
    });

    this.changeTab( '#sigs' );
}

siggymain.prototype.changeTab = function( selectedTab )
{
    var $this = this;
    $.each( $('#system-advanced ul.tabs li a'), function()
    {
        var href = $(this).attr('href');

        if( href == selectedTab )
        {
            $(this).parent().addClass('active');
            $(href).show();
        }
        else
        {
            $(this).parent().removeClass('active');
            $(href).hide();
        }

        if( href == "#system-info" )
        {
			$this.renderStats();
        }

		setCookie('system-tab', href, 365);
    } );
}

siggymain.prototype.initializeGNotes = function()
{
	var $this = this;

	$('#settings-button').click(function ()
	{
		$.blockUI({
			message: $('#settings-dialog'),
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
	});

	$('#settings-cancel').click( function() {
		$.unblockUI();
	});

	$this.globalNotesEle = $('#global-notes');
	$('#global-notes-button').click(function ()
	{
		if ( $this.globalNotesEle.is(":visible") )
		{
			$this.globalNotesEle.hide();
			$('#global-notes-button').html('Notes &#x25BC;');
		}
		else
		{
			$this.globalNotesEle.show();
			$('#global-notes-button').html('Notes &#x25B2;');
			$this.stopBlinkingNotes();
		}
	});

	$('#global-notes-edit').click(function ()
	{
		$(this).hide();
		$('#global-notes-content').hide();
		$('#global-notes-edit-box').val($this.globalNotes).show();
		$('#global-notes-save').show();
		$('#global-notes-cancel').show();
	});

	$('#global-notes-save').click(function ()
	{
		$this.globalNotes = $('#global-notes-edit-box').val();

		$.post(that.settings.baseUrl + 'siggy/notes_save', {
			notes: $this.globalNotes
		}, function (data)
		{
			$this.editingGlobalNotes = false;
			$this.lastGlobalNotesUpdate = data;
			setCookie('notesUpdate', $this.lastGlobalNotesUpdate, 365);
			$('#global-notes-time').text(siggymain.displayTimeStamp($this.lastGlobalNotesUpdate));
		});

		$('#global-notes-content').html($this.globalNotes.replace(/\n/g, '<br />')).show();
		$('#global-notes-edit-box').hide();
		$('#global-notes-edit').show();
		$('#global-notes-cancel').hide();
		$(this).hide();
	});


	$('#global-notes-cancel').click(function ()
	{
		$this.editingGlobalNotes = false;
		$('#thegnotes').show();
		$('#global-notes-edit-box').hide();
		$('#global-notes-edit').show();
		$('#global-notes-save').hide();
		$(this).hide();
	});
}

siggymain.prototype.blinkNotes = function()
{
	if( this._blinkNotesInterval != null )
	{
		return;
	}

	$('#globalNotesButton').flash("#A46D00", 3000);

	this._blinkNotesInterval = setInterval( function() {
			$('#globalNotesButton').flash("#A46D00", 3000);
	}, 4000 );
}

siggymain.prototype.stopBlinkingNotes = function()
{
	if( this._blinkNotesInterval != null )
	{
		clearInterval(this._blinkNotesInterval);
		this._blinkNotesInterval = null;
	}
}

siggymain.prototype.freeze = function()
{
	this.freezeSystem = 1;
	$('#freezeOpt').hide();
	$('#unfreezeOpt').show();
}

siggymain.prototype.unfreeze = function()
{
	this.freezeSystem = 0;
	$('#unfreezeOpt').hide();
	$('#freezeOpt').show();
}

siggymain.prototype.populateExitData = function(data)
{
	if( typeof(data.result) != "undefined" )
	{
		for(var i in data.result)
		{
			var item = $("<li>");
			item.html("<span class='faux-link'>"+data.result[i].system_name + "</span> - " + data.result[i].number_jumps + " jumps");
			$('#exit-finder-list').append(item);

			item.data("sysID", data.result[i].system_id);
			item.data("sysName",data.result[i].system_name);

			item.contextMenu( { menu: 'system-simple-context', leftMouse: true },
				function(action, el, pos) {
					var sysID = $(el[0]).data("sysID");
					var sysName  = $(el[0]).data("sysName");
					if( action == "setdest" )
					{
						if( typeof(CCPEVE) != "undefined" )
						{
							CCPEVE.setDestination(sysID);
						}
					}
					else if( action == "showinfo" )
					{
						if( typeof(CCPEVE) != "undefined" )
						{
								CCPEVE.showInfo(5, sysID );
						}
						else
						{
								window.open('http://evemaps.dotlan.net/system/'+sysName , '_blank');
						}
					}
			});
		}
	}
	else
	{
		var item = $("<li>");
		item.text("Invalid system or no exits");
		$('#exit-finder-list').append(item);
	}
}

siggymain.prototype.initializeExitFinder = function()
{
	var $this = this;

	$("#exit-finder-button").click( function() {
		$this.openBox('#exit-finder');
		$("#exit-finder-results-wrap").hide();
		return false;
	} );

	$('#exit-finder button[name=current_location]').click( function() {
		$("#exit-finder-loading").show();
		$("#exit-finder-results-wrap").hide();
		$.post($this.settings.baseUrl + 'chainmap/find_nearest_exits', {current_system: 1}, function (data)
		{
			$("#exit-finder-loading").hide();
			$('#exit-finder-list').empty();
			$this.populateExitData(data);
			$("#exit-finder-results-wrap").show();
		});
		return false;
	});

	var submitHandler = function() {
		var target = $("#exit-finder input[name=target_system]").val();
		$("#exit-finder-loading").show();
		$("#exit-finder-results-wrap").hide();
		$.post($this.settings.baseUrl + 'chainmap/find_nearest_exits', {target: target}, function (data)
		{
			$("#exit-finder-loading").hide();
			$('#exit-finder-list').empty();
			$this.populateExitData(data);
			$("#exit-finder-results-wrap").show();
		});
		return false;
	};

	$('#exit-finder form').submit(submitHandler);

	$('#exit-finder button[name=cancel]').click( function() {
		$.unblockUI();
		return false;
	} );
}


siggymain.prototype.initializeDScan = function()
{
	var $this = this;

	$('#system-intel-add-dscan').click( function() {
		//$this.openPOSForm();
		$this.openBox('#dscan-form');
		$this.setupDScanForm('add');
		return false;
	} );

	$('#dscan-form button[name=cancel]').click( function() {
		$.unblockUI();
		return false;
	} );
}

siggymain.prototype.openBox = function(ele)
{
	var $this = this;

	$.blockUI({
		message: $(ele),
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
}

siggymain.prototype.setupDScanForm = function(mode, posID)
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
				$this.forceUpdate = true;
				$.unblockUI();
			});
		}

		return false;
	} );
}

siggymain.prototype.updateDScan = function( data )
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
			row.append( $("<td>").text(siggymain.displayTimeStamp(data[i].dscan_date)) );
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

siggymain.prototype.removeDScan = function(dscanID)
{
	this.confirmDialog("Are you sure you want to delete the dscan entry?", function() {
		$.post(this.settings.baseUrl + 'dscan/remove', {dscan_id: dscanID}, function ()
		{
			$('#dscan-'+dscanID).remove();
		});
	});
}

siggymain.prototype.confirmDialog = function(message, yesCallback, noCallback)
{
	var $this = this;

	$this.openBox("#confirm-dialog");

	$("#confirm-dialog-message").text(message);
	$("#confirm-dialog-yes").unbind('click').click( function() {
		$.unblockUI();
		if (typeof(yesCallback) != "undefined" ) {
			yesCallback.call($this);
		}
	});
	$("#confirm-dialog-no").unbind('click').click( function() {
		$.unblockUI();
		if (noCallback && $.isFunction(noCallback)) {
			noCallback.call($this);
		}
	});
}


siggymain.prototype.initializePOSes = function()
{
	var $this = this;
	$('#system-intel-poses tbody').empty();

	$('#system-intel-add-pos').click( function() {
		$this.openBox('#pos-form');
		$this.addPOS();
		return false;
	} );

	$('#pos-form button[name=cancel]').click( function() {
		$.unblockUI();
		return false;
	} );
}

siggymain.prototype.getPOSStatus = function( online )
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

siggymain.prototype.updatePOSList = function( data )
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
						$this.openBox('#pos-form');
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

siggymain.prototype.addPOS = function()
{
	this.setupPOSForm('add');
}

siggymain.prototype.setupPOSForm = function(mode, posID)
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
					pos_size: 'small',
					pos_status: 0,
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
				$this.forceUpdate = true;
				$this.updateNow();

				$.unblockUI();
			});
		}

		return false;
	});
}

siggymain.prototype.editPOS = function(posID)
{
	this.setupPOSForm('edit', posID);
}

siggymain.prototype.removePOS = function(posID)
{
	var $this = this;
	this.confirmDialog("Are you sure you want to delete the POS?", function() {
		$.post(this.settings.baseUrl + 'pos/remove', {pos_id: posID}, function ()
		{
			$('#pos-'+posID).remove();

			$this.forceUpdate = true;
			$this.updateNow();
		});
	});
}

siggymain.prototype.updateChainMaps = function(data)
{
	var list = $('#chainmap-dropdown');
	var $this = this;

	//delete old
	list.empty();
	if( typeof data != "undefined" && Object.size(data) > 0 )
	{
		for(var i in data)
		{
			var chainmap = data[i];
			if( chainmap.chainmap_id == $this.chainMapID )
			{
				var extra = '';
				if(Object.size(data) > 1 )
				{
					$('#chain-map-title').removeClass('disabled');
					extra = " &#x25BC;";
				}
				else
				{
					$('#chain-map-title').addClass('disabled');
				}
				$('#chain-map-title').html(chainmap.chainmap_name + extra);
			}
			else
			{
				var a = $('<a>');
				var li = $('<li>').append(a);

				a.text(chainmap.chainmap_name);

				(function(id) {
					a.click(function(){$this.handleChainMapSelect(id)});
				})(chainmap.chainmap_id);

				list.append(li);
			}
		}
	}
}

siggymain.prototype.handleChainMapSelect = function(id)
{
	var $this = this;

	$.post(this.settings.baseUrl + 'chainmap/switch', {chainmap_id: id}, function ()
	{
		//clear group cache time or we dont update properly
		$this.groupCacheTime = 0;
		$this.forceUpdate = true;
		$this.updateNow();
	});
}
