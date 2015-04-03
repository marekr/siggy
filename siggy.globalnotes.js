/*
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

function globalnotes(options)
{
	this.defaults = {
		baseUrl: ''
	};

	this.settings = $.extend(this.defaults, options);

	this.globalNotesEle = null;
	this._blinkNotesInterval = null;
	this.globalNotes = '';
	this.editingGlobalNotes = false;

	this.siggyMain = null;
}

globalnotes.prototype.initialize = function()
{
	var $this = this;

	$this.globalNotesEle = $('#global-notes');
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
		$('#global-notes-edit-box').val($this.globalNotes).show();
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
			setCookie('notesUpdate', $this.lastGlobalNotesUpdate, 365);
			$('#global-notes-time').text(siggy2.Helpers.displayTimeStamp($this.lastGlobalNotesUpdate));
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

globalnotes.prototype.blinkNotes = function()
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

globalnotes.prototype.stopBlinkingNotes = function()
{
	if( this._blinkNotesInterval != null )
	{
		clearInterval(this._blinkNotesInterval);
		this._blinkNotesInterval = null;
	}
}

globalnotes.prototype.update = function(data)
{
	if (!this.editingGlobalNotes)
	{
		if( typeof(data.globalNotes) != 'undefined' && data.globalNotes != null )
		{
			if( getCookie('notesUpdate') != null )
			{
				var nlu = parseInt(getCookie('notesUpdate'));
			}
			else
			{
				var nlu = this.siggyMain.groupCacheTime;
			}

			if( !this.globalNotesEle.is(':visible') && data.group_cache_time > nlu && nlu != 0 )
			{
				this.blinkNotes();
			}

			setCookie('notesUpdate', data.group_cache_time, 365);

			this.globalNotes = data.globalNotes;
			$('#global-notes-content').html(this.globalNotes.replace(/\n/g, '<br />'));
			$('#global-notes-time').text( siggy2.Helpers.displayTimeStamp(data.group_cache_time) );
		}
	}
}
