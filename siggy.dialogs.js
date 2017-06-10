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
	var obj = new siggy2.Dialog(dialog);
	
	dialog.data('callbacks',{});
	this.populateButtons(dialog, buttons);

	dialog.on('click', 'button', function(ev){
		
		var key = $(this).data('key');
		var callbacks = dialog.data('callbacks');

		if(typeof(callbacks[key]) !== "undefined")
		{
			callbacks[key](obj);
			ev.stopPropagation();
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


	return obj;
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
			callback: function(dialog)
			{
				if(options.okCallback != null)
				{
					options.okCallback(dialog);
				}

				dialog.hide();
			},
			style: 'primary',
			focus: false
		}
	};

	var dialog = this.build(dialogData, buttons);

	dialog.show();

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
			callback: function(dialog)
			{
				if(options.yesCallback != null)
				{
					options.yesCallback(dialog);
				}

				dialog.hide();
			},
			style: 'primary'
		},
		no: {
			text: options.noText,
			callback: function(dialog)
			{
				if(options.noCallback != null)
				{
					options.noCallback(dialog);
				}

				dialog.hide();
			},
			style: 'danger',
			focus: true
		}
	};

	var dialog = this.build(dialogData, buttons);

	dialog.show();

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

		if(siggy2.isDefined(button.style))
		{
			buttonEle.addClass('btn-'+button.style);
		}

		if(siggy2.isDefined(button.disabled))
		{
			buttonEle.prop('disabled', true);
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
	var defaultOptions = {
		content: null,
		title: '',
		id: '',
		buttons: {}
	};

	var options = this.getOptions(defaultOptions, options);

	var dialogData = {
		title: options.title,
		content: options.content,
		type: 'dialog',
		id: options.id
	};

	var dialog = this.build(dialogData, options.buttons);

	return dialog;
}

siggy2.Dialogs.alertServerError = function(action)
{
	return siggy2.Dialogs.alert({ 
					message: "The server encountered an error while "+action+", sorry", 
					title:"Server Error"
			})
}

siggy2.Dialogs.alertActionError = function(action)
{
	return siggy2.Dialogs.alert({ 
					message: action, 
					title:"Action Error"
			})
}

siggy2.Dialog = function(ele)
{
	this.ele = $(ele);
}

siggy2.Dialog.prototype.show = function()
{
	$.blockUI({
		message: this.ele,
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
	
	$(this.ele).trigger('shown.dialog');
}

siggy2.Dialog.prototype.hide = function(dialog)
{
	$(this.ele).trigger('hide.dialog');
}

siggy2.Dialog.prototype.replaceContent = function(newContent)
{
	$(this.ele).find('.dialog-content').empty().append(newContent);
}

siggy2.Dialog.prototype.enableButton = function(key)
{
	$(this.ele).find('button').each(function(){
		if($(this).data('key') == key)
		{
			$(this).prop('disabled', false);
		}
	});
}

siggy2.Dialog.prototype.disableButton = function(key)
{
	$(this.ele).find('button').each(function(){
		if($(this).data('key') == key)
		{
			$(this).prop('disabled', true);
		}
	});
}