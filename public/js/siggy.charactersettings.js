function charactersettings(options)
{
	this.siggyMain = null;
	this.defaults = {
		baseUrl: '',
		themeID: 0,
		combineScanIntel: false,
		zoom: 1.0
	};

	this.settings = $.extend({}, this.defaults, options);
	this.dscans = {};
}


charactersettings.prototype.initialize = function()
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
	
	$("#settings-form select[name=theme_id]").change( function() {
		var themeID = $("#settings-form select[name=theme_id]").val();
		
		$this.changeTheme(themeID);
	});
	
	$("#settings-form input[name=combine_scan_intel]").prop('checked', this.settings.combineScanIntel ? true : false);
	
	$('#settings-form').submit( function() {
		
		var data = {
			theme_id: $("#settings-form select[name=theme_id]").val(),
			combine_scan_intel: $("#settings-form input[name=combine_scan_intel]").is(':checked') ? 1 : 0,
			zoom: $this.settings.zoom
		};
		
		$this.save(data);
		
		return false;
	});
	
	$this.performSettingsRefresh();
	
	$this.initializeHotkeys();
}

charactersettings.prototype.save = function(data)
{
	var $this = this;
	
	$.post($this.settings.baseUrl + 'siggy/save_character_settings', data, function (ret)
	{
		$this.settings.themeID = data.theme_id;
		$this.settings.combineScanIntel = data.combine_scan_intel;
		
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
		zoom: $this.settings.zoom
	};
	
	$this.save(data);
}

charactersettings.prototype.initializeHotkeys = function()
{
	var $this = this;
	
	$(document).bind('keydown', 'ctrl+-', function(){
		$this.zoomOut();
	});
	$(document).bind('keydown', '-', function(){
		$this.zoomOut();
	});
	
	$(document).bind('keydown', 'ctrl+=', function(){
		$this.zoomIn();
	});
	$(document).bind('keydown', '+', function(){
		$this.zoomIn();
	});
	
	$(document).bind('keydown', 'ctrl+z', function(){
		$this.resetZoom();
	});
}

charactersettings.prototype.performSettingsRefresh = function()
{
	var $this = this;
	
	this.siggyMain.changeTab("#sigs");
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
