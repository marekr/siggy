/*
* @license Proprietary
* @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
*/

siggy2.Activity = siggy2.Activity || {};

siggy2.Activity.siggy = function(core)
{
	var $this = this;

	this.core = core;

	this.forceUpdate = true;
	this.systemID = 0;
	this.systemClass = 9;
	this.systemName = '';
	this.systemStats = [];
	this.map = null;
	this.freezeSystem = 0;
	this.lastUpdate = 0;
	this.acsid = 0;

	this.chainMapID = 0;


	this.groupCacheTime = 0;

	this._updateTimeout = null;

	this.key = 'siggy';

	this.sigtable = null;
	this.systemName = this.core.settings.initialSystemName;
	this.setSystemID(this.core.settings.initialSystemID);

	this.templateEffectTooltip = Handlebars.compile( $("#template-effect-tooltip").html() );

	this.initModules();
	this.setupFormSystemOptions();

	$('#bear-C1').click(function() { that.setBearTab(1); return false; });
	$('#bear-C2').click(function() { that.setBearTab(2); return false; });
	$('#bear-C3').click(function() { that.setBearTab(3); return false; });
	$('#bear-C4').click(function() { that.setBearTab(4); return false; });
	$('#bear-C5').click(function() { that.setBearTab(5); return false; });
	$('#bear-C6').click(function() { that.setBearTab(6); return false; });



	this.initializeHubJumpContextMenu();
	this.initializeTabs();
}

siggy2.Activity.siggy.prototype.setSystemID = function (systemID)
{
	this.systemID = systemID;
	if( this.sigtable != null )
		this.sigtable.systemID = systemID;
}

siggy2.Activity.siggy.prototype.setupFormSystemOptions = function()
{
	var $this = this;
	$('#system-options-save').click(function ()
	{
		var data = {
			label: $('#system-options input[name=label]').val(),
			activity: $('#system-options select[name=activity]').val()
		};

		$this.saveSystemOptions(that.systemID, data);
	});

	$('#system-options-reset').click(function ()
	{
		$('#system-options input[name=label]').val('');
		$('#system-options select[name=activity]').val(0);

		var data = {
			label: '',
			activity: 0
		};

		$this.saveSystemOptions(that.systemID, data);
	});
}

siggy2.Activity.siggy.prototype.updateNow = function()
{
	clearTimeout(this._updateTimeout);
	return this.update();
}

siggy2.Activity.siggy.prototype.saveSystemOptions = function(systemID, newData)
{
	var $this = this;

	newData.systemID = systemID;

	$.post(this.core.settings.baseUrl + 'siggy/save_system', newData,
	function (data)
	{
		$this.forceUpdate = true;
		$this.updateNow();
	});
}

siggy2.Activity.siggy.prototype.initModules = function()
{
	this.sigtable = new siggy2.SigTable(this.core.settings.sigtable);
	this.sigtable.siggyMain = this.core;
	this.sigtable.settings.baseUrl = this.core.settings.baseUrl;

	this.inteldscan = new inteldscan(this.core.settings.intel.dscan);
	this.inteldscan.siggyMain = this.core;
	this.inteldscan.settings.baseUrl = this.core.settings.baseUrl;

	this.intelposes = new intelposes(this.core.settings.intel.poses);
	this.intelposes.siggyMain = this.core;
	this.intelposes.settings.baseUrl = this.core.settings.baseUrl;

	// Initialize map
	$(document).trigger('siggy.switchSystem', this.systemID );

	this.sigtable.initialize();
	this.map = new siggy2.Map(this.core.settings.map);
	this.map.baseUrl = this.core.settings.baseUrl;
	this.map.siggymain = this.core;
	this.map.initialize();

	this.inteldscan.initialize();
	this.intelposes.initialize();
}

siggy2.Activity.siggy.prototype.start = function()
{
	$('#activity-' + this.key).show();
	this.update();
}

siggy2.Activity.siggy.prototype.stop = function()
{
	clearTimeout(this._updateTimeout);
	$('#activity-' + this.key).hide();
}

siggy2.Activity.siggy.prototype.update = function()
{
	var request = {
		systemID: this.systemID,
		lastUpdate: this.lastUpdate,
		group_cache_time: this.groupCacheTime,
		systemName: this.systemName,
		freezeSystem: this.freezeSystem,
		acsid: this.acsid,
		mapOpen: this.core.displayStates.map.open,
		mapLastUpdate: this.map.lastUpdate,
		forceUpdate: this.forceUpdate
	};

	var $this = this;

	var that = this;
	$.ajax({
		url: $this.core.settings.baseUrl + 'update',
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
			if( data.redirect != undefined )
			{
				window.location = $this.core.settings.baseUrl + data.redirect;
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
				$this.acsid = data.acsid;
			}

			if (data.systemUpdate)
			{
				$this.updateSystemInfo(data.systemData);
				$this.updateSystemOptionsForm(data.systemData);
			}

			if (data.sigUpdate)
			{
				var flashSigs = ( data.systemUpdate ? false : true );
				$this.sigtable.updateSigs(data.sigData, flashSigs);
			}

			if(data.chainmaps_update)
			{
				$this.updateChainMaps(data.chainmaps);
			}
/*
			if (data.globalNotesUpdate)
			{
				$this.globalnotes.update(data);
			}*/
			$this.groupCacheTime = data.group_cache_time;


			if( $this.core.displayStates.map.open  )
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
					$this.map.update(timestamp, systems, whs,stargates,jumpbridges,cynos);
				}
				if( typeof(data.chainMap) != 'undefined' && typeof(data.chainMap.actives) != '' )
				{
					var actives =  data.chainMap.actives;
					$this.map.updateActives(data.chainMap.actives);
				}
			}

			$this.lastUpdate = data.lastUpdate;

			delete data;
		}
	});



	this.forceUpdate = false;
	$('span.updateTime').text(this.core.getCurrentTime());

	this._updateTimeout = setTimeout(function (thisObj)
	{
		thisObj.update(0)
	}, 10000, this);

	return true;
}

siggy2.Activity.siggy.prototype.updateChainMaps = function(data)
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



siggy2.Activity.siggy.prototype.updateSystemOptionsForm = function (systemData)
{
	$('#system-options table th').text('System Options for '+systemData.name);
	$('#system-options input[name=label]').val(systemData.displayName);
	$('#system-options select[name=activity]').val(systemData.activity);
}



siggy2.Activity.siggy.prototype.setSystemClass = function (systemClass)
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

siggy2.Activity.siggy.prototype.updateSystemInfo = function (systemData)
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

	$('#static-info').empty();
	if( Object.size(systemData.staticData) > 0 )
	{
		var counter = 0;
		for (var i in systemData.staticData)
		{
			var theStatic = siggy2.StaticData.getWormholeByID(systemData.staticData[i].id);
			var destBlurb = " (to "+siggy2.StaticData.systemClassToString(theStatic.dest_class)+")";

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

siggy2.Activity.siggy.prototype.renderStats = function()
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

siggy2.Activity.siggy.prototype.setBearTab = function( bearClass )
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


siggy2.Activity.siggy.prototype.initializeHubJumpContextMenu = function()
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


siggy2.Activity.siggy.prototype.initializeTabs = function()
{
	var $this = this;

	$('#system-advanced ul.tabs li a').click(function()
	{
		$this.changeTab( $(this).attr('href') );
		return false;
	});

	this.changeTab( '#sigs' );
}

siggy2.Activity.siggy.prototype.changeTab = function( selectedTab )
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
