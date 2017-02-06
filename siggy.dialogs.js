siggy2.Dialogs = siggy2.Dialog || {};

siggy2.Dialogs = {
	templateBaseDialog: function(dummy)
	{
	},
};

siggy2.Dialogs.init = function()
{
	this.templateBaseDialog = Handlebars.compile( $("#template-dialog-base").html() );
}

siggy2.Dialogs.getOptions = function(defaults, options)
{
	var commonOptions = {
		message: '',
		title: '',
	};

	return $.extend(true, {}, commonOptions, defaults, options);
}

siggy2.Dialogs.build = function(dialogData, buttons)
{
	var dialog = $(this.templateBaseDialog(dialogData));
	
	dialog.data('callbacks',{});
	this.populateButtons(dialog, buttons);

	dialog.on('click', 'button', function(ev){
		ev.stopPropagation();
		
		var key = $(this).data('key');
		var callbacks = dialog.data('callbacks');

		if(!typeof(callbacks[key]) != "undefined")
		{
			callbacks[key]();
		}
	});

	
	dialog.one("shown.dialog", function(){
		dialog.find('.btn-focus-me').focus();
	});

	dialog.one("hide.dialog", function(){
		dialog.off("click");
		$.unblockUI();
		dialog.remove();
	});

	return dialog;
}

siggy2.Dialogs.alert = function(options)
{
	var defaultOptions = {
		okCallback: null,
		okButtonText: 'Ok'
	};

	var options = this.getOptions(defaultOptions, options);
	
	var dialogData = {
		title: options.title,
		message: options.message,
		type: 'alert'
	};

	var buttons = {
		ok: {
			text: options.okButtonText,
			callback: function()
			{
				if(options.okCallback != null)
				{
					options.okCallback();
				}

				dialog.trigger('hide.dialog');
			},
			style: 'primary',
			focus: false
		}
	};

	var dialog = this.build(dialogData, buttons);

	this.show(dialog);

	return dialog;
}

siggy2.Dialogs.confirm = function(options)
{
	var defaultOptions = {
		message: '',
		title: '',
		yesCallback: null,
		noCallback: null,
		yesText: 'Yes',
		noText: 'No',
	};

	var options = this.getOptions(defaultOptions, options);

	var dialogData = {
		title: options.title,
		message: options.message,
		type: 'confirm'
	};

	var buttons = {
		yes: {
			text: options.yesText,
			callback: function()
			{
				if(options.yesCallback != null)
				{
					options.yesCallback();
				}

				dialog.trigger('hide.dialog');
			},
			style: 'primary'
		},
		no: {
			text: options.noText,
			callback: function()
			{
				if(options.noCallback != null)
				{
					options.noCallback();
				}

				dialog.trigger('hide.dialog');
			},
			style: 'danger',
			focus: true
		}
	};

	var dialog = this.build(dialogData, buttons);

	this.show(dialog);

	return dialog;
}

siggy2.Dialogs.populateButtons = function(dialog, buttons)
{
	var footer = dialog.find("div.dialog-footer");
	var callbacks = dialog.data('callbacks');
	$.each(buttons, function(key, button) {
		var buttonEle = $("<button>").addClass('btn btn-default');
		buttonEle.text(button.text);
		footer.append(buttonEle);

		if(button.callback != null)
		{
			callbacks[key] = button.callback;
		}

		buttonEle.data('key', key);

		if(typeof(button.style) != 'undefined')
		{
			buttonEle.addClass('btn-'+button.style);
		}

		if(button.focus)
		{
			buttonEle.addClass('btn-focus-me');
		}
	});

	dialog.data('callbacks',callbacks);
}

siggy2.Dialogs.dialog = function(options)
{
	var options = getOptions(options);
}

siggy2.Dialogs.show = function(dialog)
{
	$.blockUI({
		message: dialog,
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
		fadeOut:  0,
		focusInput: false
	});
	
	dialog.trigger('shown.dialog');
}

siggy2.Dialogs.hide = function(dialog)
{
	dialog.trigger('hide.dialog');
}