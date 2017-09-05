/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import $ from 'jquery';
import Helpers from './Helpers';
import { Siggy as SiggyCore } from './Siggy';

export default class CharacterSettings {
	private core: SiggyCore;
	public settings: any;
	
	private readonly defaults = {
		baseUrl: '',
		themeID: 0,
		combineScanIntel: false,
		zoom: 1.0,
		language: 'en',
		defaultActivity: 'siggy'
	};
	constructor(core: SiggyCore, options)
	{
		this.core = core;

		this.settings = $.extend({}, this.defaults, options);
	}

	public initialize()
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

		$this.performSettingsRefresh(false);
	}

	public initForm()
	{
		$("#character-settings-form input[name=combine_scan_intel]").prop('checked', this.settings.combineScanIntel ? true : false);

		$('#character-settings-form select[name=language]').val( this.settings.language );
		
		$('#character-settings-form select[name=default_activity]').val( this.settings.defaultActivity );
	}

	public save(data)
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
				$this.performSettingsRefresh(true);
				$.unblockUI();
			});
	}


	public saveAll()
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

	public performSettingsRefresh(refresh: boolean)
	{
		var $this = this;

	//	this.siggyMain.changeTab("#sigs");
		if( this.settings.combineScanIntel )
		{
			$("#system-stats").before($("#structure-box"));
			$("#system-stats").before($("#pos-box"));
			$("#system-stats").before($("#dscan-box"));
			$('li a[href="#system-intel"]').parent().hide();
		}
		else
		{
			$("#structure-box").detach().appendTo($('#system-intel'));
			$("#pos-box").detach().appendTo($('#system-intel'));
			$("#dscan-box").detach().appendTo($('#system-intel'));
			$('li a[href="#system-intel"]').parent().show();
		}

		$("body").css("zoom", this.settings.zoom);

		/* init localisations  */
		if( this.settings.language != 'en' )
		{
			$.ajax({
				url: this.settings.baseUrl + 'data/locale/'+this.settings.language,
				success: function(result) {
							window._.setTranslation(result);
						},
				async: false,
				dataType: 'json',
				cache: false
			});
		}

		//force a update to refresh
		if(refresh){
			$(document).trigger('siggy.updateRequested', true );
		}
		//do not call updateNow as on page load this will cause quirkyness/race condition with another update call
	}

	public resetZoom()
	{
		this.settings.zoom = 1.0;
		$("body").css("zoom", this.settings.zoom);

		this.saveAll();
	}

	public zoomOut()
	{
		this.settings.zoom -= 0.05;
		$("body").css("zoom", this.settings.zoom);

		this.saveAll();
	}

	public zoomIn()
	{
		this.settings.zoom += 0.05;
		$("body").css("zoom", this.settings.zoom);

		this.saveAll();
	}

	public changeTheme(themeID)
	{
		$("#theme-css").attr('href', this.settings.baseUrl + "theme.php?id=" + themeID);
	}
}