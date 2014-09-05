function charactersettings(options)
{
	this.siggyMain = null;
	this.defaults = {
		baseUrl: '',
		themeID: 0,
		combineScanIntel: false
	};

	this.settings = $.extend(this.defaults, options);
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
	
	$("#settings-form input[name=combine_scan_intel]").attr('checked', this.settings.combineScanIntel ? true : false);
	
	$('#settings-form').submit( function() {
		
		var data = {
			theme_id: $("#settings-form select[name=theme_id]").val(),
			combine_scan_intel: $("#settings-form input[name=combine_scan_intel]").is(':checked') ? 1 : 0
		};
		
		$.post($this.settings.baseUrl + 'siggy/save_character_settings', data, function (ret)
		{
			$this.settings.themeID = data.theme_id;
			$this.settings.combineScanIntel = data.combine_scan_intel;
			
			$this.performSettingsRefresh();
			$.unblockUI();
		});
		
		return false;
	});
	
	$this.performSettingsRefresh();
}

charactersettings.prototype.performSettingsRefresh = function()
{
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
}

charactersettings.prototype.changeTheme = function(themeID)
{
	$("#theme-css").attr('href', this.settings.baseUrl + "theme.php?id=" + themeID);
}
