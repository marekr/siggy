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

	// Display states cookie
	var displayStatesCookie = getCookie('display_states');
	var dispStates = '';
	if( displayStatesCookie != null )
	{
		dispStates = $.parseJSON(displayStatesCookie);
	}

	this.displayStates = $.extend({}, this.defaultDisplayStates, dispStates);
	this.displayStates.map = $.extend({}, this.defaultDisplayStates.map, dispStates.map);


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
						notifications: new siggy2.Activity.Notifications(this)
					};

	$(document).on('click','button.dialog-cancel', function(e)
	{
		e.preventDefault();

		$.unblockUI();
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
	siggy2.Helpers.setupHandlebars();

	var that = this;
	this.setupFatalErrorHandler();

	$(document).ajaxStart( function() {
		$(this).show();
	});

	$(document).ajaxStop( function() {
		$(this).hide();
	} );

	siggy2.StaticData.load(this.settings.baseUrl);
	siggy2.Helpers.setupSystemTypeAhead('.system-typeahead');


	this.charactersettings.initialize();
	this.globalnotes.initialize();

	this.registerMainMenu();

	this.updateNow();


	this.loadActivity('siggy');
}

siggy2.Core.prototype.update = function()
{
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
	});

	this.queueUpdate();
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
	setCookie('display_states', JSON.stringify(this.displayStates), 365);
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
