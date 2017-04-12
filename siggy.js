/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

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
});


var siggy2 = siggy2 || {};

/**
* @constructor
*/
siggy2.Core = function( options )
{
	this.inactive = false;
	this.fatalError = false;
	this.ajaxErrors = 0;
	this.groupCacheTime = 0;

	this._updateTimeout = null;

	this.location = {
		id: 0,
		name: ''
	};

	this.defaults = {
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
		igb: true,
		sessionID: '',
		negativeBalance: false
	};

	this.settings = $.extend(true, {}, this.defaults, options);

	this.defaultDisplayStates = {
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

	siggy2.Eve.Initialize(this.settings.baseUrl);
	siggy2.Dialogs.init();

	siggy2.Helpers.setupHandlebars();
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

	this.charactersettings = new charactersettings(this, this.settings.charsettings);
	this.charactersettings.settings.baseUrl = this.settings.baseUrl;

	this.globalnotes = new globalnotes(this.settings.globalnotes);
	this.globalnotes.siggyMain = this;
	this.globalnotes.settings.baseUrl = this.settings.baseUrl;

	this.hotkeyhelper = new hotkeyhelper();
	this.hotkeyhelper.siggyMain = this;
	this.hotkeyhelper.initialize();

	this.notifications = new siggy2.Notifications(this);


	this.activity = '';
	this.activities = { thera: new siggy2.Activity.Thera(this),
	  					search: new siggy2.Activity.Search(this),
						siggy: new siggy2.Activity.siggy(this),
						scannedsystems: new siggy2.Activity.ScannedSystems(this),
						notifications: new siggy2.Activity.Notifications(this),
						astrolabe: new siggy2.Activity.Astrolabe(this),
						chainmap: new siggy2.Activity.Chainmap(this),
					//	homestead: new siggy2.Activity.Homestead(this)
					};

	$(document).on('click','button.dialog-cancel', function(e)
	{
		e.preventDefault();

		$.unblockUI();
	});

	if(typeof(siggy2.Socket) != "undefined")
	{
		siggy2.Socket.Initialize("ws://localhost:51760/ws?token=" + this.settings.sessionID);
		siggy2.Socket.Open();
	}

	if(this.settings.negativeBalance)
	{
		this.balanceHarass();
	}
}

siggy2.Core.prototype.balanceHarass = function()
{
	var $this = this;
	siggy2.Dialogs.alert({
		title: "Negative balance!",
		message: "The balance for this siggy group has gone negative. Payment must be made according to the information \
			in the management panel or service may be discontinued at any time. <br />Contact <b>Jack Tronic</b> if assitance is needed. <br /> <br /> \
			If you have already paid and have waited a hour for processing, refresh the page and this message should stop appearing",
		okCallback: function() {
			setTimeout(	function(){ $this.balanceHarass(); }, (1000*60)*10);	//10 minutes for now
		}
	});
}

siggy2.Core.prototype.queueUpdate = function()
{
	this._updateTimeout = setTimeout(function (thisObj)
	{
		thisObj.update(0)
	}, 10000, this);
}


siggy2.Core.prototype.updateNow = function()
{
	clearTimeout(this._updateTimeout);
	return this.update();
}

siggy2.Core.prototype.initialize = function ()
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
				
				siggy2.Dialogs.alert({ 
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

	siggy2.StaticData.load(this.settings.baseUrl, this);
}

siggy2.Core.prototype.continueInitialize = function()
{
	siggy2.Helpers.setupSystemTypeAhead('.system-typeahead');


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

siggy2.Core.prototype.update = function()
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
				window.location = $this.settings.baseUrl + data.redirect;
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
				siggy2.Maps.available = data.chainmaps;
				$(document).trigger('siggy.mapsAvaliableUpdate');
			}

			if (data.global_notes_update)
			{
				$this.globalnotes.update(data);
			}

			$this.notifications.update(data.notifications);

			$this.groupCacheTime = data.group_cache_time;

			delete data;
		}
	}).always(function(){
		$this.queueUpdate();
	});
}

siggy2.Core.prototype.registerMainMenu = function()
{
	var $this = this;

	$('.activity-menu-option').click( function() {
		$this.loadActivity( $(this).data('activity') );
	});
}

siggy2.Core.prototype.loadActivity = function(activity, args)
{
	var $this = this;


	if( typeof( $this.activities[ activity ] ) == 'undefined' && activity != 'siggy' )
		return;

	if( $this.activity == activity )
		return;


	if( $this.activity != '' )
		$this.activities[$this.activity].stop();


	$this.activity = activity;

	if( typeof(args) == 'undefined' )
		args = {};

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

siggy2.Core.prototype.saveDisplayState = function()
{
	try {
		window.localStorage.setItem('display_states', JSON.stringify(this.displayStates));
	} catch (e) {
		//silently ignore the error
		//incognito/private browsing can cause quota error
	}
}


siggy2.Core.prototype.getCurrentTime = function ()
{
	var date = new Date();
	var time = pad(date.getUTCHours(), 2) + ':' + pad(date.getUTCMinutes(), 2) + ':' + pad(date.getUTCSeconds(), 2);

	delete date;

	return time;
}

siggy2.Core.prototype.displayFatalError = function(message)
{
	siggy2.Dialogs.alert({ 
			message: message, 
			title:"Fatal error has occurred",
			okButtonText: "Reload",
			okCallback: function(){
				window.location.reload(true);
			}
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

siggy2.Core.prototype.initializeBasicSystemContextMenu = function()
{
	$(document).contextMenu({
		selector: '.basic-system-context',
        build: function($trigger, e) {
			var items = {
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
						siggy2.Eve.SetDestination(sysID);
					}
					else if( key == "addwaypoint" )
					{
						siggy2.Eve.AddWaypoint(sysID);
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

siggy2.Maps = {
	available: {},
	selected: 0,
	getSelectDropdown: function(selected, selectedNote)
	{
		var sel = $('<select>');
		$.each(this.available, function(key, c) {
			var value = c.name;
			if( selected == key && typeof selectedNote != '' && selectedNote != '' )
			{
				value += ' ' + selectedNote;
			}

			sel.append($("<option>", {
				value: key,
				text: value
			}));
		});

		if( typeof(selected) != "undefined" )
		{
			sel.val(selected);
		}

		return sel;
	}
}
