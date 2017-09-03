/*
* @license Proprietary
* @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
*/

import $ from 'jquery';

import * as moment from 'moment';
import * as Handlebars from '../vendor/handlebars';
import Chart from 'chart.js';
import Activity from './Activity';
import SigTable from '../SigTable';
import { StaticData, blackHoleEffects, wolfRayetEffects, redGiantEffects, catacylsmicEffects, magnetarEffects, pulsarEffects } from '../StaticData';
import DScan from '../Intel/DScan';
import POSes from '../Intel/POSes';
import Structures from '../Intel/Structures';
import Helpers from '../Helpers';
import Map from '../Map';
import { Maps } from '../Maps';

const chartColors = {
	red: 'rgb(255, 99, 132)',
	orange: 'rgb(255, 159, 64)',
	yellow: 'rgb(255, 205, 86)',
	green: 'rgb(75, 192, 192)',
	blue: 'rgb(54, 162, 235)',
	purple: 'rgb(153, 102, 255)',
	grey: 'rgb(201, 203, 207)'
};

export class Siggy extends Activity {

	private forceUpdate = true;
	private systemID = 0;
	private systemClass = 9;
	private systemName = '';
	private systemStats: any;
	private map: Map = null;
	private freezeSystem = false;
	private lastUpdate = 0;
	private updateInProgress = false;

	private chainMapID = 0;

	private _updateTimeout = null;

	public key = 'siggy';

	private sigtable: SigTable = null;
	private inteldscan: DScan = null;
	private intelposes: POSes = null;
	private intelstructures: Structures = null;

	private templateEffectTooltip = null;
	private statsChartConfig: any;
	private statsChart : Chart = null;

	private groupCacheTime: number = 0;

	constructor(core) {
		super(core);

		var $this = this;

		this.setSystemID(this.core.settings.initialSystemID);

		if( this.core.settings.freezeSystem )
		{
			this.freeze();
		}

		this.templateEffectTooltip = Handlebars.compile( $("#template-effect-tooltip").html() );

		$(document).on('siggy.map.systemSelected', function(e, systemID) {
			$this.freeze();
			$this.switchSystem(systemID);
		} );


		$(document).on('siggy.updateRequested', function(e, force) {
			$this.forceUpdate = force;
			$this.updateNow();
		} );

		$(document).on('siggy.locationChanged', function(e, oldID, newID ) {
			if( !$this.freezeSystem )
			{
				// zero our last update to ensure a force update suceeds,
				// i.e. a failed update requested is undesired as we'll end up desynced
				$this.lastUpdate = 0;
				$this.switchSystem(newID);
			}
		});

		$(document).on('siggy.mapsAvaliableUpdate', function(e) {
			$this.updateChainMaps(Maps.available);
		});

		this.initModules();
		this.setupFormSystemOptions();

		$('#bear-C1').click(function() { $this.setBearTab(1); return false; });
		$('#bear-C2').click(function() { $this.setBearTab(2); return false; });
		$('#bear-C3').click(function() { $this.setBearTab(3); return false; });
		$('#bear-C4').click(function() { $this.setBearTab(4); return false; });
		$('#bear-C5').click(function() { $this.setBearTab(5); return false; });
		$('#bear-C6').click(function() { $this.setBearTab(6); return false; });

		this.initializeTabs();
		this.initializeCollaspibles();

		this.configureStatsChart();
	}

	public configureStatsChart()
	{
		var timeFormat = 'HH:mm';
		var color = Chart.helpers.color;

		var legendFontColor = '#fff';
		var axesFontColor = '#fff';
		var gridLinesColor = '#adadad';
		var ticksFontColor = '#fff';
		var labelFontColor = '#fff';

		this.statsChartConfig = {
			type: 'line',
			data: {
				datasets: [{
					label: "Jumps",
					backgroundColor: color(chartColors.green).alpha(0.7).rgbString(),
					borderColor: chartColors.green,
					fill: true,
					yAxisID: 'y-axis-jumps',
					data: [],
				},{
					label: "NPC Kills",
					backgroundColor: color(chartColors.blue).alpha(0.7).rgbString(),
					borderColor: chartColors.blue,
					fill: true,
					yAxisID: 'y-axis-kills',
					data: [],
				},{
					label: "Ship Kills",
					backgroundColor: color(chartColors.grey).alpha(0.7).rgbString(),
					borderColor: chartColors.grey,
					fill: true,
					yAxisID: 'y-axis-kills',
					data: [],
				},{
					label: "Pod Kills",
					backgroundColor: color(chartColors.red).alpha(0.7).rgbString(),
					borderColor: chartColors.red,
					fill: true,
					yAxisID: 'y-axis-kills',
					data: [],
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				title:{
					text: "Stats"
				},
				legend: {
					labels: {
						fontColor: legendFontColor
					}
				},
				scales: {
					xAxes: [{
						type: "time",
						display: true,
						time: {
							unit: 'hour',
							displayFormats: {
								hour: 'HH:mm'
							}             
						},
						scaleLabel: {
							display: true,
							labelString: 'Time',
							fontColor: labelFontColor
						},
						gridLines:{
							color: gridLinesColor
						},
						ticks: {
							fontColor: ticksFontColor
						}
					}, ],
					yAxes: [{
						id: 'y-axis-jumps',
						stacked: true,
						scaleLabel: {
							display: true,
							labelString: 'Jumps',
							fontColor: labelFontColor
						},
						gridLines:{
							color: gridLinesColor,
							drawOnChartArea: false
						},
						ticks: {
							fontColor: ticksFontColor
						}
					},{
						id: 'y-axis-kills',
						stacked: true,
						scaleLabel: {
							display: true,
							labelString: 'Kills',
							fontColor: labelFontColor
						},
						gridLines:{
							color: gridLinesColor,
							drawOnChartArea: true
						},
						ticks: {
							fontColor: ticksFontColor
						}
					}],
				},
			}
		};


		var ctx = (<HTMLCanvasElement>document.getElementById("stats-canvas")).getContext("2d");
		this.statsChart = new Chart(ctx, this.statsChartConfig);
	}


	public initializeCollaspibles()
	{
		var $this = this;

		this.core.setupCollaspible('#system-stats', 'statsOpen', function() {$this.renderStats();});
		this.core.setupCollaspible('#sig-add-box', 'sigsAddOpen');
		this.core.setupCollaspible('#dscan-box', 'dscanOpen');
		this.core.setupCollaspible('#pos-box', 'posesOpen');
		this.core.setupCollaspible('#structure-box', 'structuresOpen');
	}

	public setSystemID(systemID: number)
	{
		this.systemID = systemID;
		if( this.sigtable != null )
			this.sigtable.systemID = systemID;
		if( this.intelposes != null )
			this.intelposes.systemID = systemID;
		if( this.inteldscan != null )
			this.inteldscan.systemID = systemID;
		if( this.intelstructures != null )
			this.intelstructures.systemID = systemID;
	}

	public setupFormSystemOptions()
	{
		var $this = this;
		$('#system-options-save').click(function ()
		{
			var data = {
				label: $('#system-options input[name=label]').val(),
				activity: $('#system-options select[name=activity]').val()
			};

			$this.map.saveSystemOptions($this.systemID, data);
		});

		$('#system-options-reset').click(function ()
		{
			$('#system-options input[name=label]').val('');
			$('#system-options select[name=activity]').val(0);

			var data = {
				label: '',
				activity: 0
			};

			$this.map.saveSystemOptions($this.systemID, data);
		});
	}

	public updateNow()
	{
		clearTimeout(this._updateTimeout);
		return this.update();
	}

	public initModules()
	{
		this.inteldscan = new DScan(this.core.settings.intel.dscan);
		this.inteldscan.siggyMain = this.core;
		this.inteldscan.settings.baseUrl = this.core.settings.baseUrl;

		this.intelposes = new POSes(this.core, {baseUrl: this.core.settings.baseUrl});
		this.intelposes.settings.baseUrl = this.core.settings.baseUrl;

		this.intelstructures = new Structures(this.core, {baseUrl: this.core.settings.baseUrl});
		this.intelstructures.initialize();

		// Initialize map
		this.map = new Map(this.core, this.core.settings.map);
		this.map.baseUrl = this.core.settings.baseUrl;

		this.sigtable = new SigTable(this.core, this.map, this.core.settings.sigtable);
		this.sigtable.settings.baseUrl = this.core.settings.baseUrl;

		this.map.initialize();
		this.sigtable.initialize();

		$(document).trigger('siggy.systemSwitched', this.systemID );

		this.inteldscan.initialize();
		this.intelposes.initialize();
	}

	public freeze()
	{
		this.freezeSystem = true;
	}

	public unfreeze()
	{
		this.freezeSystem = false;
	}

	public switchSystem(systemID: number)
	{
		this.setSystemID(systemID);
		this.forceUpdate = true;
		clearTimeout(this._updateTimeout);

		this.sigtable.clear();

		this.sigtable.setupSiteSelectForNewSystem(this.systemClass);

		if( this.updateNow() )
		{
			$(document).trigger('siggy.systemSwitched', systemID );
		}
	}

	public start(args): void
	{
		if( typeof(args) != 'undefined' )
		{
			if( typeof(args.systemID) != 'undefined' )
			{
				this.freeze();
				this.switchSystem(args.systemID);
			}
		}

		$('#activity-' + this.key).show();
		this.update();
		this.map.draw();
	}

	public stop(): void
	{
		clearTimeout(this._updateTimeout);
		$('#activity-' + this.key).hide();
	}

	public update()
	{
		if(this.core.inactive)
		{
			return;
		}

		if( !this.freezeSystem && this.core.settings.igb )
		{
			this.systemID = this.core.location.id;
		}

		if( typeof(this.systemID) == 'undefined' || this.systemID == 0 )
			return;
		
		if( this.updateInProgress )
			return;

		this.updateInProgress = true;

		var request = {
			systemID: this.systemID,
			lastUpdate: this.lastUpdate,
			mapOpen: this.core.displayStates.map.open,
			mapLastUpdate: this.map.lastUpdate,
			forceUpdate: this.forceUpdate
		};

		var $this = this;

		$.ajax({
			url: $this.core.settings.baseUrl + 'siggy/siggy',
			data: request,
			dataType: 'json',
			cache: false,
			async: true,
			method: 'post',
			timeout: 10000,
			beforeSend : function(xhr, opts){
				if($this.core.fatalError == true)
				{
					xhr.abort();
				}
			},
			success: function (data)
			{

				if( data.redirect != undefined )
				{
					window.location.href = $this.core.settings.baseUrl + data.redirect;
					return;
				}

				var mapID = parseInt(data.chainmap_id);

				if(mapID != $this.chainMapID)
				{
					Maps.selected = $this.chainMapID = mapID;
					$this.updateChainMaps(Maps.available);
				}

				if( $this.chainMapID == 0 )
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

				if (data.systemUpdate)
				{
					$this.updateSystemInfo(data.systemData);
					$this.updateSystemOptionsForm(data.systemData);
				}

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
					if( Helpers.isDefined(data.chainMap) && Helpers.isDefined(data.chainMap.actives) )
					{
						var actives =  data.chainMap.actives;
						$this.map.updateActives(data.chainMap.actives);
					}
				}

				if (data.sigUpdate)
				{
					var flashSigs = ( data.systemUpdate ? false : true );
					$this.sigtable.updateSigs(data.sigData, flashSigs);
				}

				$this.lastUpdate = data.lastUpdate;
			}
		}).always(function(){
			$this.forceUpdate = false;
			$this.updateInProgress = false;
			$this.queueUpdate();
			$('span.updateTime').text($this.core.getCurrentTime());
		});


		return true;
	}


	public queueUpdate()
	{
		this._updateTimeout = setTimeout(function (thisObj)
		{
			thisObj.update(0)
		}, 10000, this);
	}

	public updateChainMaps(data)
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
				if( chainmap.id == $this.chainMapID )
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
					$('#chain-map-title').html(chainmap.name + extra);
				}
				else
				{
					var a = $('<a>');
					var li = $('<li>').append(a);

					a.text(chainmap.name);

					(function(id) {
						a.click(function(){$this.handleChainMapSelect(id)});
					})(chainmap.id);

					list.append(li);
				}
			}
		}
	}


	public handleChainMapSelect(id)
	{
		var $this = this;

		$.post(this.core.settings.baseUrl + 'chainmap/switch', {chainmap_id: id}, function ()
		{
			//clear group cache time or we dont update properly
			$this.groupCacheTime = 0;
			$(document).trigger('siggy.updateRequested', true );
		});
	}

	public updateSystemOptionsForm(systemData)
	{
		$('#system-options table th').text('System Options for '+systemData.name);
		$('#system-options input[name=label]').val(Helpers.unescape_html_entities(systemData.displayName));
		$('#system-options select[name=activity]').val(systemData.activity);
	}

	public setSystemClass(systemClass)
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

	public updateSystemInfo(systemData)
	{
		//general info
		$('#region').text(systemData.regionName + " / " + systemData.constellationName);
		$('#constellation').text(systemData.constellationName);
		$('#planetsmoons').text(systemData.planets + "/" + systemData.moons + "/" + systemData.belts);
		$('#truesec').text(systemData.truesec);
		$('#radius').text(systemData.radius + ' '+ window._('AU'));

		//HUB JUMPS
		var hubJumpsStr = '';
		$('#hub-jumps').empty();

		for(var index in systemData.hubJumps)
		{
			var hub = systemData.hubJumps[index];

			var hubDiv = $("<div>").addClass('hub-jump').addClass('basic-system-context')
			.text(hub.destination_name + " (" + hub.num_jumps + " "+window._('Jumps')+")")
			.data("system-id", hub.system_id)
			.data("system-name", hub.destination_name);

			$('#hub-jumps').append(hubDiv);
		}


		//EFFECT STUFF
		//effect info
		$('#system-effect > p').qtip('destroy');
		$('#system-effect').empty();

		
		if(systemData.effectTitle != null)
		{
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
		}

		$('#static-info').empty();
		if( Object.size(systemData.staticData) > 0 )
		{
			for (var i in systemData.staticData)
			{
				var theStatic = StaticData.getWormholeByID(systemData.staticData[i].id);
				if(theStatic != null)
				{
					var destBlurb = " (to "+StaticData.systemClassToString(theStatic.dest_class)+")";

					var staticBit = $("<p>").text(theStatic.name + destBlurb);

					theStatic.destBlurb = destBlurb;
					var staticTooltip = StaticData.templateWormholeInfoTooltip(theStatic);

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
				}
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
		$('#system-name').html(sysName);


		$('a.site-dotlan').attr('href', 'http://evemaps.dotlan.net/system/'+systemData.name);
		$('a.site-wormholes').attr('href', 'http://wh.pasta.gg/'+systemData.name);
		$('a.site-evekill').attr('href','http://eve-kill.net/?a=system_detail&sys_name='+systemData.name);
		$('a.site-zkillboard').attr('href','https://zkillboard.com/system/'+systemData.id);

		this.setSystemID(systemData.id);
		this.setSystemClass(systemData.sysClass);
		this.systemName = systemData.name;

		if( typeof(systemData.stats) != 'undefined' )
		{
			this.systemStats = systemData.stats;
			this.renderStats();
		}
		
		this.intelposes.updatePOSList( systemData.poses );
		this.inteldscan.updateDScan( systemData.dscans );
		this.intelstructures.update( systemData.structures );
	}


	public renderStats()
	{
		var jumps = [];
		var shipKills = [];
		var podKills = [];
		var npcKills = [];
		for( var i = 0; i < this.systemStats.jumps.length; i++ )
		{
			var entry = this.systemStats.jumps[i];
			jumps.push({
				x: entry.date_start, 
				y: entry.ship_jumps
			});
		}
		
		for( var i = 0; i < this.systemStats.kills.length; i++ )
		{
			var entry = this.systemStats.kills[i];
			npcKills.push({
				x: entry.date_start, 
				y: entry.npc_kills
			});
			
			shipKills.push({
				x: entry.date_start, 
				y: entry.ship_kills
			});
			
			podKills.push({
				x: entry.date_start, 
				y: entry.pod_kills
			});
		}
		
		this.statsChartConfig.data.datasets[0].data = jumps;
		this.statsChartConfig.data.datasets[1].data = npcKills;
		this.statsChartConfig.data.datasets[2].data = shipKills;
		this.statsChartConfig.data.datasets[3].data = podKills;
		
		this.statsChartConfig.options.scales.xAxes[0].time.min = moment.utc().subtract(1, 'day');
		this.statsChartConfig.options.scales.xAxes[0].time.max = moment.utc();
		
		this.statsChart.update();
	}

	public setBearTab( bearClass )
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


	public initializeTabs()
	{
		var $this = this;

		$('#system-advanced a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

			var href = $(e.target).attr('href');

			if( href == "#system-info" )
			{
				$this.renderStats();
			}

			//setCookie('system-tab', href, 365);
		});
	}
}
