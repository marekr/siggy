/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import $ from 'jquery';
import Helpers from './Helpers';
import { Siggy as SiggyCore } from './Siggy';

export default class GlobalNotes {

	private readonly defaults = {
		baseUrl: ''
	};

	public settings = null;
	
	private globalNotesEle = null;
	private _blinkNotesInterval = null;
	private globalNotes = '';
	private editingGlobalNotes = false;

	private core: SiggyCore = null;
	private lastGlobalNotesUpdate: boolean = false;

	constructor(core: SiggyCore, options)
	{
		this.core = core;
		this.settings = $.extend(this.defaults, options);
		this.globalNotesEle = $('#global-notes');
	}

	public initialize()
	{
		var $this = this;

		$('#global-notes-button').click(function ()
		{
			$this.stopBlinkingNotes();

			$.blockUI({
				message: $this.globalNotesEle,
				css: {
					border: 'none',
					padding: '15px',
					background: 'transparent',
					color: 'inherit',
					cursor: 'auto',
					textAlign: 'left',
					centerX: true,
					centerY: true
				},
				overlayCSS: {
					cursor: 'auto'
				},
				fadeIn:  0,
				fadeOut:  0
			});
			$('.blockOverlay').attr('title','Click to unblock').click($.unblockUI);
		});

		$('#global-notes-edit').click(function ()
		{
			$(this).hide();
			$('#global-notes-content').hide();
			$('#global-notes-edit-box').val(Helpers.unescape_html_entities($this.globalNotes)).show();
			$('#global-notes-save').show();
			$('#global-notes-cancel').show();
		});

		$('#global-notes-save').click(function ()
		{
			$this.globalNotes = $('#global-notes-edit-box').val();

			$.post($this.settings.baseUrl + 'siggy/notes_save', {
				notes: $this.globalNotes
			}, function (data)
			{
				$this.editingGlobalNotes = false;
				$this.lastGlobalNotesUpdate = data;
				Helpers.setCookie('notesUpdate', $this.lastGlobalNotesUpdate, 365);
				$('#global-notes-time').text(Helpers.displayTimeStamp($this.lastGlobalNotesUpdate));
			});

			$('#global-notes-content').html($this.globalNotes.replace(/\n/g, '<br />')).show();
			$('#global-notes-edit-box').hide();
			$('#global-notes-edit').show();
			$('#global-notes-cancel').hide();
			$(this).hide();
		});


		$('#global-notes-cancel').click(function ()
		{
			$this.editingGlobalNotes = false;
			$('#global-notes-content').show();
			$('#global-notes-edit-box').hide();
			$('#global-notes-edit').show();
			$('#global-notes-save').hide();
			$(this).hide();
		});
	}

	public blinkNotes()
	{
		if( this._blinkNotesInterval != null )
		{
			return;
		}

		$('#globalNotesButton').flash("#A46D00", 3000);

		this._blinkNotesInterval = setInterval( function() {
				$('#globalNotesButton').flash("#A46D00", 3000);
		}, 4000 );
	}

	public stopBlinkingNotes()
	{
		if( this._blinkNotesInterval != null )
		{
			clearInterval(this._blinkNotesInterval);
			this._blinkNotesInterval = null;
		}
	}

	public update(data)
	{
		if (!this.editingGlobalNotes)
		{
			if( typeof(data.globalNotes) != 'undefined' && data.globalNotes != null )
			{
				let nlu = 0;
				if( Helpers.getCookie('notesUpdate') != null )
				{
					nlu = parseInt(Helpers.getCookie('notesUpdate'));
				}
				else
				{
					nlu = this.core.groupCacheTime;
				}

				if( !this.globalNotesEle.is(':visible') && data.group_cache_time > nlu && nlu != 0 )
				{
					this.blinkNotes();
				}

				Helpers.setCookie('notesUpdate', data.group_cache_time, 365);

				this.globalNotes = data.globalNotes;
				$('#global-notes-content').html(this.globalNotes.replace(/\n/g, '<br />'));
				$('#global-notes-time').text( Helpers.displayTimeStamp(data.group_cache_time) );
			}
		}
	}
}