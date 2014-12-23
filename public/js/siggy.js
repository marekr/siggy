/*
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */


$( function()
{
	$('input, textarea').placeholder();
});

var siggy2 = siggy2 || {};

/**
* @constructor
*/
siggy2.Core = function( options )
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


	this.groupCacheTime = 0;

	this.defaults = {
		baseUrl: '',
		initialSystemID: 0,
		initialSystemName: '',
		sessionID: '',
		charsettings: {
			themeID: 0,
			combineScanIntel: false,
			themeList: {},
			zoom: 1.0,
			language: 'en'
		},
		map: {
			jumpTrackerEnabled: true
	    },
		sigtable: {
			showSigSizeCol: false
		},
		globalnotes: {
		},
		intel: {
			dscan: {
			},
			poses: {
			}
		},
		igb: true
	};

	this.settings = $.extend({}, this.defaults, options);

	this.defaultDisplayStates = {
		statsOpen: false,
		sigsAddOpen: true,
		showAnomalies: true,
		posesOpen: true,
		dscanOpen: true,
		map: {
			open: true,
			height: 400
		}
	}

	this.displayStates = this.defaultDisplayStates;

	this.sigtable = new siggy2.SigTable(this.settings.sigtable);
	this.sigtable.siggyMain = this;
	this.sigtable.settings.baseUrl = this.settings.baseUrl;

	this.inteldscan = new inteldscan(this.settings.intel.dscan);
	this.inteldscan.siggyMain = this;
	this.inteldscan.settings.baseUrl = this.settings.baseUrl;

	this.intelposes = new intelposes(this.settings.intel.poses);
	this.intelposes.siggyMain = this;
	this.intelposes.settings.baseUrl = this.settings.baseUrl;

	this.globalnotes = new globalnotes(this.settings.globalnotes);
	this.globalnotes.siggyMain = this;
	this.globalnotes.settings.baseUrl = this.settings.baseUrl;

	this.charactersettings = new charactersettings(this.settings.charsettings);
	this.charactersettings.siggyMain = this;
	this.charactersettings.settings.baseUrl = this.settings.baseUrl;

	this.hotkeyhelper = new hotkeyhelper();
	this.hotkeyhelper.siggyMain = this;
	this.hotkeyhelper.initialize();

	this.systemName = this.settings.initialSystemName;
    this.setSystemID(this.settings.initialSystemID);
	
	this.templateEffectTooltip = Handlebars.compile( $("#template-effect-tooltip").html() );
}


siggy2.Core.prototype.initialize = function ()
{
	var that = this;
	this.setupFatalErrorHandler();

	$(document).ajaxStart( function() {
		$(this).show();
	});

	$(document).ajaxStop( function() {
		$(this).hide();
	} );
	
	siggy2.StaticData.load(this.settings.baseUrl);

	// Display states cookie
	var displayStatesCookie = getCookie('display_states');
	var dispStates = '';
	if( displayStatesCookie != null )
	{
		dispStates = $.parseJSON(displayStatesCookie);
	}

	this.displayStates = $.extend({}, this.defaultDisplayStates, dispStates);
	this.displayStates.map = $.extend({}, this.defaultDisplayStates.map, dispStates.map);

	// Initialize map
	this.map = new siggy2.Map(this.settings.map);
	this.map.baseUrl = this.settings.baseUrl;
	this.map.siggymain = this;
	this.map.initialize();

	this.charactersettings.initialize();
	this.inteldscan.initialize();
	this.intelposes.initialize();
	this.globalnotes.initialize();

	this.forceUpdate = true;
	this.update();
	$(document).trigger('siggy.switchSystem', this.systemID );

	this.sigtable.initialize();

	this.setupAddBox();

	$('#system-options-save').click(function ()
	{
		var data = {
			label: $('#system-options input[name=label]').val(),
			activity: $('#system-options select[name=activity]').val()
		};

		that.saveSystemOptions(that.systemID, data);
	});

	$('#system-options-reset').click(function ()
	{
		$('#system-options input[name=label]').val('');
		$('#system-options select[name=activity]').val(0);

		var data = {
			label: '',
			activity: 0
		};

		that.saveSystemOptions(that.systemID, data);
	});

	$('#bear-C1').click(function() { that.setBearTab(1); return false; });
	$('#bear-C2').click(function() { that.setBearTab(2); return false; });
	$('#bear-C3').click(function() { that.setBearTab(3); return false; });
	$('#bear-C4').click(function() { that.setBearTab(4); return false; });
	$('#bear-C5').click(function() { that.setBearTab(5); return false; });
	$('#bear-C6').click(function() { that.setBearTab(6); return false; });


	this.initializeCollaspibles();
	this.initializeTabs();


	this.initializeExitFinder();

	this.initializeHubJumpContextMenu();
}

siggy2.Core.prototype.initializeHubJumpContextMenu = function()
{
	$(document).contextMenu({
		selector: '.basic-system-context',
		callback: function(key, options) {
			var sysID = $(this).data("sysID");
			var sysName  = $(this).data("sysName");
			if( key == "setdest" )
			{
				if( typeof(CCPEVE) != "undefined" )
				{
					CCPEVE.setDestination(sysID);
				}
			}
			else if( key == "showinfo" )
			{
				if( typeof(CCPEVE) != "undefined" )
				{
						CCPEVE.showInfo(5, sysID);
				}
				else
				{
						window.open('http://evemaps.dotlan.net/system/'+sysName , '_blank');
				}
			}
		},
		items: {
			"setdest": {name: "Set Destination"},
			"sep1": "---------",
			"showinfo": {name: "Show Info"}
		}
	});
}

siggy2.Core.prototype.initializeCollaspibles = function()
{
	var $this = this;

	this.setupCollaspible('#system-stats', 'statsOpen', function() {$this.renderStats();});
	this.setupCollaspible('#sig-add-box', 'sigsAddOpen');
	this.setupCollaspible('#dscan-box', 'dscanOpen');
	this.setupCollaspible('#pos-box', 'posesOpen');
}

siggy2.Core.prototype.ecSetExpandedArrow = function(ele)
{
	$(ele).children('.expand-collapse-indicator').removeClass('fa-caret-down').addClass('fa-caret-up');
}

siggy2.Core.prototype.ecSetCollaspedArrow = function(ele)
{
	$(ele).children('.expand-collapse-indicator').removeClass('fa-caret-up').addClass('fa-caret-down');
}

siggy2.Core.prototype.setupCollaspible = function(baseID, displayState, onShow)
{
	var $this = this;
	var content = $(baseID + ' > div');
	var h2 = $(baseID +' h2');

	if( $this.displayStates[displayState] )
	{
		$this.ecSetExpandedArrow(h2);
		content.show();
	}
	else
	{
		this.ecSetCollaspedArrow(h2);
		content.hide();
	}

	h2.click( function() {
		if( content.is(":visible") )
		{
			content.hide();
			$this.ecSetCollaspedArrow(this);
			
			
			$this.displayStates[displayState] = false;
			$this.saveDisplayState();
		}
		else
		{
			content.show();
			$this.ecSetExpandedArrow(this);
			
			if( typeof(onShow) == 'function' )
				onShow.call();
			$this.displayStates[displayState] = true;
			$this.saveDisplayState();
		}
	});

}

siggy2.Core.prototype.saveDisplayState = function()
{
	setCookie('display_states', JSON.stringify(this.displayStates), 365);
}


siggy2.Core.prototype.getCurrentTime = function ()
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
siggy2.Helpers = siggy2.Helpers || {};
siggy2.Helpers.displayTimeStamp = function (unixTimestamp)
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

siggy2.Core.prototype.update = function ()
{
	var request = {
		systemID: this.systemID,
		lastUpdate: this.lastUpdate,
		group_cache_time: this.groupCacheTime,
		systemName: this.systemName,
		freezeSystem: this.freezeSystem,
		acsid: this.acsid,
		acsname: this.acsname,
		mapOpen: this.displayStates.map.open,
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
						that.globalnotes.update(data);
					}
					this.groupCacheTime = data.group_cache_time;


					if( that.displayStates.map.open  )
					{
						if( parseInt(data.mapUpdate) == 1  )
						{
							//use temp vars or else chrome chokes badly with async requests
							var timestamp = data.chainMap.lastUpdate;
							var systems = data.chainMap.systems;
							var whs = data.chainMap.wormholes;
							var stargates = data.chainMap.stargates;
							var jumpbridges = data.chainMap.jumpbridges;
							var cynos = data.chainMap.cynos;
							that.map.update(timestamp, systems, whs,stargates,jumpbridges,cynos);
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

siggy2.Core.prototype.registerSwitchHandler = function (item, systemID, systemName)
{
	var that = this;
	item.click(function ()
	{
		//$.blockUI({ message: '<h1 style="font-size:1.2em;"><strong>Loading...</strong></h1>' });
		that.switchSystem(systemID, systemName);

	});
}

siggy2.Core.prototype.updateNow = function()
{
	clearTimeout(this._updateTimeout);
	return this.update();
}

siggy2.Core.prototype.switchSystem = function(systemID, systemName)
{
	this.setSystemID(systemID);
	this.systemName = systemName;
	this.forceUpdate = true;
	this.freeze();
	clearTimeout(this._updateTimeout);

	this.sigtable.clear();


	$('#sig-add-box select[name=type]').val(0);
	this.sigtable.updateSiteSelect('#sig-add-box select[name=site]',this.systemClass, 0, 0);

	if( this.updateNow() )
	{
		$(document).trigger('siggy.switchSystem', systemID );
	}
}

siggy2.Core.prototype.updateSystemInfo = function (systemData)
{
	//general info
	$('#region').text(systemData.regionName + " / " + systemData.constellationName);
	$('#constellation').text(systemData.constellationName);
	$('#planetsmoons').text(systemData.planets + "/" + systemData.moons + "/" + systemData.belts);
	$('#truesec').text(systemData.truesec.substr(0,8));
	$('#radius').text(systemData.radius + ' '+ _('AU'));

	//HUB JUMPS
	var hubJumpsStr = '';
	$('#hub-jumps').empty();

	for(var index in systemData.hubJumps)
	{
		var hub = systemData.hubJumps[index];

		var hubDiv = $("<div>").addClass('hub-jump').addClass('basic-system-context')
							   .text(hub.destination_name + " (" + hub.num_jumps + " "+_('Jumps')+")")
							   .data("sysID", hub.system_id)
							   .data("sysName", hub.destination_name);

		$('#hub-jumps').append(hubDiv);
	}


    //EFFECT STUFF
	//effect info
    $('#system-effect > p').qtip('destroy');
	$('#system-effect').empty();


	var effectTitle = $("<p>").text(systemData.effectTitle);
	var effect = $('#system-effect').append(effectTitle);

	if( systemData.effectTitle != 'None' )
	{
		var effData = [];
		if( systemData.effectTitle == 'Black Hole' )
		{
			effData = blackHoleEffects[systemData.sysClass];
		}
		else if(systemData.effectTitle == 'Wolf-Rayet Star')
		{
			effData = wolfRayetEffects[systemData.sysClass];
		}
		else if(systemData.effectTitle == 'Red Giant')
		{
			effData = redGiantEffects[systemData.sysClass];
		}
		else if(systemData.effectTitle == 'Cataclysmic Variable')
		{
			effData = catacylsmicEffects[systemData.sysClass];
		}
		else if(systemData.effectTitle == 'Magnetar')
		{
			effData = magnetarEffects[systemData.sysClass];
		}
		else if(systemData.effectTitle == 'Pulsar')
		{
			effData = pulsarEffects[systemData.sysClass];
		}

		if( typeof(effData) == 'undefined' )
		{
			effData = [];
		}
		
		var tooltip = this.templateEffectTooltip({
												  sysClass: systemData.sysClass, 
												  effects: effData
												  });
		
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
			var theStatic = siggy2.StaticData.getWormholeByID(systemData.staticData[i].id);
			var destBlurb = '';
			theStatic.dest_class = parseInt(theStatic.dest_class);

			if (theStatic.dest_class <= 6 || theStatic.dest_class >= 10)
			{
				destBlurb = " (to C" + theStatic.dest_class + ")";
			}
			else if (theStatic.dest_class == 7)
			{
				destBlurb = " (to Highsec)";
			}
			else if (theStatic.dest_class == 8)
			{
				destBlurb = " (to Lowsec)";
			}
			else
			{
				destBlurb = " (to Nullsec)";
			}

			var staticBit = $("<p>").text(theStatic.name + destBlurb);
			
			theStatic.destBlurb = destBlurb;
			var staticTooltip = siggy2.StaticData.templateWormholeInfoTooltip(theStatic);

			$('#static-info').append(staticBit);

			staticBit.qtip({
				content: {
					text: staticTooltip
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

	this.intelposes.updatePOSList( systemData.poses );
	this.inteldscan.updateDScan( systemData.dscans );
}

siggy2.Core.prototype.renderStats = function()
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

siggy2.Core.prototype.setBearTab = function( bearClass )
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



siggy2.Core.prototype.updateSystemOptionsForm = function (systemData)
{
	$('#system-options table th').text('System Options for '+systemData.name);
	$('#system-options input[name=label]').val(systemData.displayName);
	$('#system-options select[name=activity]').val(systemData.activity);
}


siggy2.Core.prototype.setSystemID = function (systemID)
{
	this.systemID = systemID;
	this.sigtable.systemID = systemID;
}

siggy2.Core.prototype.setSystemClass = function (systemClass)
{
	this.systemClass = systemClass;
	this.sigtable.systemClass = systemClass;
	if( systemClass <= 6 )
	{
		this.setBearTab(systemClass);
	}
	else
	{
		this.setBearTab(1);
	}
}

siggy2.Core.prototype.massAddHandler = function(systemID, data)
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
			$this.sigtable.addSigRow(newSig[i]);
		}

		$.extend($this.sigtable.sigData, newSig);
		$('#sig-table').trigger('update');
	}, 'json');
}

siggy2.Core.prototype.setupAddBox = function ()
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

		$.post($this.settings.baseUrl + 'sig/add', postData, function (newSig)
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

		$this.sigtable.updateSiteSelect( '#sig-add-box select[name=site]', $this.systemClass, newType, 0);
	}).keypress(this.addBoxEnterHandler);

	if( this.settings.showSigSizeCol )
	{
		$('#sig-add-box select[name=size]').keypress(this.addBoxEnterHandler);
	}

	$( document ).on('keypress', '#sig-add-box select[name=site]', this.addBoxEnterHandler);
}

siggy2.Core.prototype.addBoxEnterHandler = function(e)
{
	if(e.which == 13)
	{
		$('button[name=add]').focus().click();
	}
}

siggy2.Core.prototype.displayFatalError = function(message)
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

siggy2.Core.prototype.setupFatalErrorHandler = function()
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

siggy2.Core.prototype.saveSystemOptions = function(systemID, newData)
{
	var $this = this;

	newData.systemID = systemID;

	$.post(this.settings.baseUrl + 'siggy/save_system', newData,
	function (data)
	{
		if ($this.systemList[systemID])
		{
			if( typeof(newData.label) != 'undefined' )
			{
				$this.systemList[systemID].displayName = newData.label;
			}

			if( typeof(newData.activity) != 'undefined' )
			{
				$this.systemList[systemID].activity = newData.activity;
			}
		}

		$this.forceUpdate = true;
		$this.updateNow();
	});
}

siggy2.Core.prototype.initializeTabs = function()
{
	var $this = this;

    $('#system-advanced ul.tabs li a').click(function()
    {
        $this.changeTab( $(this).attr('href') );
        return false;
    });

    this.changeTab( '#sigs' );
}

siggy2.Core.prototype.changeTab = function( selectedTab )
{
    var $this = this;
    $('#system-advanced ul.tabs li a').each(function()
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

siggy2.Core.prototype.freeze = function()
{
	this.freezeSystem = 1;
	$('#freezeOpt').hide();
	$('#unfreezeOpt').show();
}

siggy2.Core.prototype.unfreeze = function()
{
	this.freezeSystem = 0;
	$('#unfreezeOpt').hide();
	$('#freezeOpt').show();
}

siggy2.Core.prototype.populateExitData = function(data)
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
			item.addClass('basic-system-context');
		}
	}
	else
	{
		var item = $("<li>");
		item.text("Invalid system or no exits");
		$('#exit-finder-list').append(item);
	}
}

siggy2.Core.prototype.initializeExitFinder = function()
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



siggy2.Core.prototype.openBox = function(ele)
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

siggy2.Core.prototype.confirmDialog = function(message, yesCallback, noCallback)
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


siggy2.Core.prototype.updateChainMaps = function(data)
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
					extra = " <i class='fa fa-caret-down'></i>";
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

siggy2.Core.prototype.handleChainMapSelect = function(id)
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
