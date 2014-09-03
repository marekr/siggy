//when opened with system in url
if (typeof(CCPEVE) != "undefined")
{
	CCPEVE.requestTrust('http://siggy.borkedlabs.com/*');
}

$( function()
{
	$('input, textarea').placeholder();
});

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
		sessionID: '',
		map: {
		  jumpTrackerEnabled: true
	    },
		sigtable: {
			showSigSizeCol: false
		},
		intel: {
			dscan: {
			},
			poses: {
			}
		}
	};

	this.settings = $.extend(this.defaults, options);

	this.defaultDisplayStates = {
		statsOpen: false,
		sigsAddOpen: true,
		showAnomalies: true
	}

	this.displayStates = this.defaultDisplayStates;

	this.systemName = this.settings.initialSystemName;
    this.setSystemID(this.settings.initialSystemID);

	this.sigtable = new sigtable(this.settings.sigtable);
	this.sigtable.siggyMain = this;
	this.sigtable.settings.baseUrl = this.settings.baseUrl;
	
	this.inteldscan = new inteldscan(this.settings.intel.dscan);
	this.inteldscan.siggyMain = this;
	this.inteldscan.settings.baseUrl = this.settings.baseUrl;
	
	this.intelposes = new intelposes(this.settings.intel.poses);
	this.intelposes.siggyMain = this;
	this.intelposes.settings.baseUrl = this.settings.baseUrl;
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

	this.sigtable.initialize();

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

	this.inteldscan.initialize();
	this.intelposes.initialize();

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

					if( that.chainMapID == 0 )
					{
						$('#chain-map-container').hide();
						$('#main-body').hide();
						$('#no-chain-map-warning').show();
					}
					else
					{
						$('#chain-map-container').show();
						$('#main-body').show();
						$('#no-chain-map-warning').hide();
					}

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
						that.sigtable.updateSigs(data.sigData, flashSigs);
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
	this.sigtable.systemID = systemID;
	this.sigtable.clear();


	$('#sig-add-box select[name=type]').val(0);
	this.updateSiteSelect('#sig-add-box select[name=site]',this.systemClass, 0, 0);

	if( this.updateNow() )
	{
		$(document).trigger('siggy.switchSystem', systemID );
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

	this.sigtable.systemID = systemData.id;
	this.sigtable.systemClass = systemData.sysClass;
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

	this.intelposes.updatePOSList( systemData.poses );
	this.inteldscan.updateDScan( systemData.dscans );
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

siggymain.prototype.massAddHandler = function(systemID, data)
{
	var $this = this;

	var postData = {
		systemID: systemID,
		blob: data
	};

	$.post( $this.settings.baseUrl + 'domassSigs', postData, function (newSig)
	{
		for (var i in newSig)
		{
			$this.sigtable.addSigRow(newSig[i]);
		}

		$.extend($this.sigtable.sigData, newSig);
		$('#sig-table').trigger('update');
	}, 'json');
}

siggymain.prototype.setupAddBox = function ()
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

		if( $this.settings.sigtable.showSigSizeCol )
		{
			var sizeEle = $('#sig-add-box select[name=size]');
			postData.sigSize = sizeEle.val();
		}

		$.post($this.settings.baseUrl + 'dosigAdd', postData, function (newSig)
		{
			for (var i in newSig)
			{
				$this.sigtable.addSigRow(newSig[i]);
			}
			$.extend($this.sigtable.sigData, newSig);
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
	$.post(this.settings.baseUrl + 'dosaveSystemOptions', {
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

		$.post($this.settings.baseUrl + 'siggy/notes_save', {
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
