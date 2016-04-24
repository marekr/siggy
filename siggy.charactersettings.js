/*
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

function charactersettings(core, options)
{
	this.core = core;
	this.defaults = {
		baseUrl: '',
		themeID: 0,
		combineScanIntel: false,
		zoom: 1.0,
		language: 'en',
		defaultActivity: 'siggy'
	};

	this.settings = $.extend({}, this.defaults, options);
}

charactersettings.prototype.initialize = function()
{
	var $this = this;

	$('#settings-button').click(function ()
	{
		$this.initForm();
		$this.core.openBox($('#settings-dialog'));
	});

	$("#character-settings-form select[name=theme_id]").change( function() {
		var themeID = $("#character-settings-form select[name=theme_id]").val();

		$this.changeTheme(themeID);
	});

	this.initForm();

	$('#character-settings-form').submit( function() {

		var data = {
			theme_id: $('#character-settings-form select[name=theme_id]').val(),
			combine_scan_intel: $('#character-settings-form input[name=combine_scan_intel]').is(':checked') ? 1 : 0,
			zoom: $this.settings.zoom,
			language: $('#character-settings-form select[name=language]').val(),
			default_activity: $('#character-settings-form select[name=default_activity]').val(),
		};

		$this.save(data);

		return false;
	});

	$this.performSettingsRefresh();

	$this.initializeHotkeys();
}

charactersettings.prototype.initForm = function()
{
	$("#character-settings-form input[name=combine_scan_intel]").prop('checked', this.settings.combineScanIntel ? true : false);

	$('#character-settings-form select[name=language]').val( this.settings.language );
	
	$('#character-settings-form select[name=default_activity]').val( this.settings.defaultActivity );
}

charactersettings.prototype.save = function(data)
{
	var $this = this;

	$.ajax({
			type: 'post',
			url: $this.settings.baseUrl + 'siggy/save_character_settings',
			data: JSON.stringify(data),
			contentType: 'application/json',
			success: function (ret)
					{
						$this.settings.themeID = data.theme_id;
						$this.settings.combineScanIntel = data.combine_scan_intel;
						$this.settings.language = data.language;
						$this.settings.defaultActivity = data.defaultActivity;
					},
			dataType: 'json'
		})
		.always(function(){
			$this.performSettingsRefresh();
			$.unblockUI();
		});
}


charactersettings.prototype.saveAll = function()
{
	var $this = this;

	var data = {
		theme_id: $this.settings.themeID,
		combine_scan_intel: $this.settings.combineScanIntel,
		zoom: $this.settings.zoom,
		language: $this.settings.language,
		default_activity: $this.settings.defaultActivity
	};

	$this.save(data);
}

charactersettings.prototype.initializeHotkeys = function()
{
	var $this = this;

	if( this.core.settings.igb )
	{
		$(document).bind('keydown', 'ctrl+-', function(){
			$this.zoomOut();
		});

		$(document).bind('keydown', 'ctrl+=', function(){
			$this.zoomIn();
		});

		$(document).bind('keydown', 'ctrl+z', function(){
			$this.resetZoom();
		});

		this.core.hotkeyhelper.registerHotkey('Ctrl+Z', 'Reset page zoom');
		this.core.hotkeyhelper.registerHotkey('Ctrl+-', 'Zoom page in');
		this.core.hotkeyhelper.registerHotkey('Ctrl+=', 'Zoom page out');
		this.core.hotkeyhelper.registerHotkey('+', 'Zoom page in');
		this.core.hotkeyhelper.registerHotkey('-', 'Zoom page out');
	}
}

charactersettings.prototype.performSettingsRefresh = function()
{
	var $this = this;

//	this.siggyMain.changeTab("#sigs");
	if( this.settings.combineScanIntel )
	{
		$("#system-stats").before($("#pos-box"));
		$("#system-stats").before($("#dscan-box"));
		$('li a[href="#system-intel"]').parent().hide();
	}
	else
	{
		$("#pos-box").detach().appendTo($('#system-intel'));
		$("#dscan-box").detach().appendTo($('#system-intel'));
		$('li a[href="#system-intel"]').parent().show();
	}

	$("body").css("zoom", this.settings.zoom);

	/* init localisations  */
	if( this.settings.language != 'en' )
	{
		jQuery.ajax({
			 url: this.settings.baseUrl + 'js/locale/siggy.locale.'+this.settings.language+'.js',
			 success: function(result) {
						_.setTranslation(result);
					  },
			 async: false,
			 dataType: 'json',
             cache: false
		});
	}

	//force a update to refresh
	$this.core.forceUpdate = 1;
	//do not call updateNow as on page load this will cause quirkyness/race condition with another update call
}

charactersettings.prototype.resetZoom = function()
{
	this.settings.zoom = 1.0;
	$("body").css("zoom", this.settings.zoom);

	this.saveAll();
}

charactersettings.prototype.zoomOut = function()
{
	this.settings.zoom -= 0.05;
	$("body").css("zoom", this.settings.zoom);

	this.saveAll();
}

charactersettings.prototype.zoomIn = function()
{
	this.settings.zoom += 0.05;
	$("body").css("zoom", this.settings.zoom);

	this.saveAll();
}

charactersettings.prototype.changeTheme = function(themeID)
{
	$("#theme-css").attr('href', this.settings.baseUrl + "theme.php?id=" + themeID);
}
