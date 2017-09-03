/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import $ from "jquery";

//global jquery extensions...which become usable once loaded here
import "jquery/jquery.contextMenu.js";
import "jquery/jquery-ui.js";
import "jquery/jquery.contextMenu.js";
import "jquery/jquery.idle.js";
import "jquery/jquery.blockUI.js";
import "jquery/jquery.hotkeys.js";
import "jquery/translate.js";
import "jquery/jquery.flash.js";
import "jquery/jquery.serializeObject.js";
import "jquery/jquery.simplePagination.js";
import "qtip2";
import "jquery/bootstrap.tab.js";
import "jquery/bootstrap.dropdown.js";

import "./Extensions/Object";
import "./Extensions/String";
import "./Extensions/Number";

import moment from 'moment-timezone';
import Eve from './Eve';
import { Dialogs } from './Dialogs';
import Helpers from './Helpers';
import Notifications from './Notifications';
import { Siggy as SiggyActivity } from './activity/Siggy';
import { Chainmap as ChainmapActivity } from './activity/Chainmap';
import { Thera as TheraActivity } from './activity/Thera';
import { ScannedSystems as ScannedSystemsActivity } from './activity/ScannedSystems';
import { Notifications as NotificationsActivity } from './activity/Notifications';
import { Search as SearchActivity } from './activity/Search';
import CharacterSettings from './CharacterSettings';
import GlobalNotes from './GlobalNotes';
import HotkeyHelper from './HotkeyHelper';
import { StaticData } from './StaticData';
import { Maps } from './Maps';


$( function()
{
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$(function(){
		$(document).on('click','input[type=text].select-on-focus',function(){ this.select(); });
	});
	
	moment.tz.setDefault("UTC");
});


/**
* @constructor
*/
export class Siggy {
	private inactive = false;
	private fatalError = false;
	private ajaxErrors = 0;
	private groupCacheTime = 0;

	private _updateTimeout = null;


	private readonly defaults = {
		baseUrl: '',
		initialSystemID: 0,
		freezeSystem: false,
		defaultActivity: 'siggy',
		charsettings: {
			themeID: 0,
			combineScanIntel: false,
			themeList: {},
			zoom: 1.0,
			language: 'en',
			defaultActivity: 'siggy'
		},
		map: {
			jumpTrackerEnabled: true
		},
		sigtable: {
			showSigSizeCol: false,
			enableWhSigLink: true
		},
		globalnotes: {
		},
		intel: {
			dscan: {
			},
			poses: {
			}
		},
		igb: true,
		sessionID: '',
		negativeBalance: false
	};

	private readonly defaultDisplayStates = {
		statsOpen: false,
		sigsAddOpen: true,
		posesOpen: true,
		dscanOpen: true,
		structuresOpen: true,
		map: {
			open: true,
			height: 400
		},
		sigFilters: {
			wh: true,
			ore: true,
			gas: true,
			data: true,
			relic: true,
			anomaly: true,
			none: true
		}
	}

	private settings: any;
	private location = {
		id: 0,
		name: ''
	};

	private displayStates: any;

	private charactersettings: CharacterSettings;
	private globalnotes: GlobalNotes;
	private hotkeyhelper: HotkeyHelper;
	private notifications: Notifications;

	private activity: string = '';
	private activities: any;

	constructor(options: any)
	{
		this.settings = $.extend(true, {}, this.defaults, options);

		Eve.Initialize(this.settings.baseUrl);
		Dialogs.init();

		Helpers.setupHandlebars();
		this.initializeBasicSystemContextMenu();

		// Display states cookie
		try {
			var displayStatesStored = window.localStorage.getItem('display_states');
			
			if(displayStatesStored != null)
			{
				this.displayStates = $.parseJSON(displayStatesStored);
			}
		} catch(e) {
			//errors such as NS_ERROR_FILE_CORRUPTED can occur on getItem which is stupid but we can't do much
			this.displayStates = {};
		}

		this.displayStates = $.extend(true, {}, this.defaultDisplayStates, this.displayStates);

		this.charactersettings = new CharacterSettings(this, this.settings.charsettings);
		this.charactersettings.settings.baseUrl = this.settings.baseUrl;

		this.globalnotes = new GlobalNotes(this.settings.globalnotes);
		this.globalnotes.siggyMain = this;
		this.globalnotes.settings.baseUrl = this.settings.baseUrl;

		this.hotkeyhelper = new HotkeyHelper();
		this.hotkeyhelper.siggyMain = this;
		this.hotkeyhelper.initialize();

		this.notifications = new Notifications(this);


		this.activity = '';
		this.activities = { thera: new TheraActivity(this),
							search: new SearchActivity(this),
							siggy: new SiggyActivity(this),
							scannedsystems: new ScannedSystemsActivity(this),
							notifications: new NotificationsActivity(this),
						//	astrolabe: new siggy2.Activity.Astrolabe(this),
							chainmap: new ChainmapActivity(this),
						//	homestead: new siggy2.Activity.Homestead(this)
						};

		$(document).on('click','button.dialog-cancel', function(e)
		{
			e.preventDefault();

			$.unblockUI();
		});
/*
		if(typeof(siggy2.Socket) != "undefined")
		{
			siggy2.Socket.Initialize("ws://localhost:51760/ws?token=" + this.settings.sessionID);
			siggy2.Socket.Open();
		}
*/
		if(this.settings.negativeBalance)
		{
			this.balanceHarass();
		}
	}

	public balanceHarass()
	{
		var $this = this;
		Dialogs.alert({
			title: "Negative balance!",
			message: "The balance for this siggy group has gone negative. Payment must be made according to the information \
				in the management panel or service may be discontinued at any time. <br />Contact <b>Jack Tronic</b> if assitance is needed. <br /> <br /> \
				If you have already paid and have waited a hour for processing, refresh the page and this message should stop appearing",
			okCallback: function() {
				setTimeout(	function(){ $this.balanceHarass(); }, (1000*60)*10);	//10 minutes for now
			}
		});
	}

	public queueUpdate()
	{
		this._updateTimeout = setTimeout(function (thisObj)
		{
			thisObj.update(0)
		}, 10000, this);
	}


	public updateNow()
	{
		clearTimeout(this._updateTimeout);
		return this.update();
	}

	public initialize()
	{
		var $this = this;
		this.setupFatalErrorHandler();

		$(document).ajaxStart( function() {
			$(this).show();
		});

		$(document).ajaxStop( function() {
			$(this).hide();
		} );


		$(document).idle({
			onIdle: function(){
				if(!$this.inactive)
				{
					$this.inactive = true;
					
					Dialogs.alert({ 
							message: "siggy session timed out due to one hour of inactivity", 
							title:"Session expired",
							okButtonText: "Refresh",
							okCallback: function(){
								window.location.reload(true);
							}
					});
				}
			},
			idle: 1000*(60*60)*1.5,	// 60 minutes
		})

		StaticData.load(this.settings.baseUrl, this);
	}

	public continueInitialize()
	{
		Helpers.setupSystemTypeAhead('.system-typeahead');


		this.charactersettings.initialize();
		this.globalnotes.initialize();

		this.registerMainMenu();

		this.updateNow();

		var defaultActivity = 'siggy';
		if( this.settings.defaultActivity != '' )
		{
			defaultActivity = this.settings.defaultActivity;
		}

		if( this.settings.charsettings.defaultActivity != '' )
		{
			defaultActivity = this.settings.charsettings.defaultActivity;
		}

		if(window.location.hash)
		{
			defaultActivity = window.location.hash.slice(1);;
		}

		this.loadActivity(defaultActivity);
	}

	public update()
	{
		if(this.inactive)
		{
			return;
		}

		var $this = this;
		var request = {
			last_location_id: $this.location.id,
			group_cache_time: $this.groupCacheTime,
			newest_notification: $this.notifications.newestTimestamp
		};

		$.ajax({
			url: $this.settings.baseUrl + 'update',
			data: request,
			dataType: 'json',
			cache: false,
			async: true,
			method: 'post',
			timeout: 10000,	//ten second timeout
			beforeSend : function(xhr, opts){
				if($this.fatalError == true)
				{
					xhr.abort();
				}
			},
			success: function (data)
			{
				if( data.redirect != undefined )
				{
					window.location.href = $this.settings.baseUrl + data.redirect;
					return;
				}

				if( parseInt( data.location.id ) != 0 )
				{
					var old = $this.location.id;
					$this.location.id = data.location.id;

					if( old != $this.location.id )
					{
						$(document).trigger('siggy.locationChanged', [old, $this.location.id] );
					}
				}

				if(data.chainmaps_update)
				{
					Maps.available = data.chainmaps;
					$(document).trigger('siggy.mapsAvaliableUpdate');
				}

				if (data.global_notes_update)
				{
					$this.globalnotes.update(data);
				}

				$this.notifications.update(data.notifications);

				$this.groupCacheTime = data.group_cache_time;
			}
		}).always(function(){
			$this.queueUpdate();
		});
	}

	public registerMainMenu()
	{
		var $this = this;

		$('.activity-menu-option').click( function() {
			$this.loadActivity( $(this).data('activity') );
		});
	}

	public loadActivity (activity, args: any = {})
	{
		var $this = this;


		if( typeof( $this.activities[ activity ] ) == 'undefined' && activity != 'siggy' )
			return;

		if( $this.activity == activity )
			return;


		if( $this.activity != '' )
			$this.activities[$this.activity].stop();


		$this.activity = activity;


		$this.activities[$this.activity].start( args );


		$('.activity-menu-option').show();
		$('.activity-menu-option').each( function() {
			if( $(this).data('activity') == activity )
			{

				$('#current-activity').text( $(this).text() );
				$(this).hide();
			}
		} );
	}


	public ecSetExpandedArrow(ele)
	{
		$(ele).children('.expand-collapse-indicator').removeClass('fa-caret-down').addClass('fa-caret-up');
	}

	public ecSetCollaspedArrow(ele)
	{
		$(ele).children('.expand-collapse-indicator').removeClass('fa-caret-up').addClass('fa-caret-down');
	}

	public setupCollaspible(baseID, displayState, onShow)
	{
		var $this = this;
		var content = $(baseID + ' div.sub-display-group-content');
		var h2 = $(baseID +' div.sub-display-group-header');

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

	public saveDisplayState()
	{
		try {
			window.localStorage.setItem('display_states', JSON.stringify(this.displayStates));
		} catch (e) {
			//silently ignore the error
			//incognito/private browsing can cause quota error
		}
	}


	public getCurrentTime()
	{
		var date = new Date();
		var time = Helpers.pad(date.getUTCHours(), 2) + ':' + Helpers.pad(date.getUTCMinutes(), 2) + ':' + Helpers.pad(date.getUTCSeconds(), 2);

		return time;
	}

	public displayFatalError(message)
	{
		Dialogs.alert({ 
				message: message, 
				title:"Fatal error has occurred",
				okButtonText: "Reload",
				okCallback: function(){
					window.location.reload(true);
				}
		});
	}

	public setupFatalErrorHandler()
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
	}


	public openBox(ele)
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

	public initializeBasicSystemContextMenu()
	{
		$(document).contextMenu({
			selector: '.basic-system-context',
			build: function($trigger, e) {
				var items: any = {
								"showinfo": {name: "Show Info"}
							};

				items.sep1 = "---------";
				items.setdest = {name:'Set Destination'};
				items.addwaypoint = {name: 'Add Waypoint'};

				return {
					callback: function(key, options) {
						var sysID = $($trigger).data("system-id");
						var sysName  = $($trigger).data("system-name");
						if( key == "setdest" )
						{
							Eve.SetDestination(sysID);
						}
						else if( key == "addwaypoint" )
						{
							Eve.AddWaypoint(sysID);
						}
						else if( key == "showinfo" )
						{
							window.open('http://evemaps.dotlan.net/system/'+sysName , '_blank');
						}
					},
					items: items
				};
			}
		});
	}
}
