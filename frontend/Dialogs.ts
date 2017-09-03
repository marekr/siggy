/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import $ from "jquery";
import * as Handlebars from './vendor/handlebars';
import Helpers from './Helpers';

export class Dialogs {
	private static templateBaseDialog = null;

	public static init()
	{
		this.templateBaseDialog = Handlebars.compile( $("#template-dialog-base").html() );
	}
	
	public static getOptions(defaults, options)
	{
		var commonOptions = {
			message: '',
			title: '',
		};
	
		return $.extend(true, {}, commonOptions, defaults, options);
	}
	
	public static build(dialogData, buttons)
	{
		var dialog = $(this.templateBaseDialog(dialogData));
		var obj = new Dialog(dialog);
		
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
			dialog.find('.dialog-focus-me').focus();
		});
	
		dialog.one("hide.dialog", function(){
			dialog.off("click");
			$.unblockUI();
			dialog.remove();
		});
	
	
		return obj;
	}
	
	
	public static alert(options)
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
	
	public static confirm(options)
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
	
	public static populateButtons(dialog, buttons)
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
	
			if(Helpers.isDefined(button.style))
			{
				buttonEle.addClass('btn-'+button.style);
			}
	
			if(Helpers.isDefined(button.disabled))
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
	
	public static dialog(options)
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
	
	public static alertServerError(action)
	{
		return this.alert({ 
						message: "The server encountered an error while "+action+", sorry", 
						title:"Server Error"
				})
	}
	
	public static alertActionError(action)
	{
		return this.alert({ 
						message: action, 
						title:"Action Error"
				})
	}

}

export class Dialog {
	private ele = null;

	constructor(ele) {
		this.ele = $(ele);
	}

	public show()
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

	public hide(dialog)
	{
		$(this.ele).trigger('hide.dialog');
	}

	public replaceContent(newContent)
	{
		$(this.ele).find('.dialog-content').empty().append(newContent);
	}

	public enableButton(key)
	{
		$(this.ele).find('button').each(function(){
			if($(this).data('key') == key)
			{
				$(this).prop('disabled', false);
			}
		});
	}

	public disableButton(key)
	{
		$(this.ele).find('button').each(function(){
			if($(this).data('key') == key)
			{
				$(this).prop('disabled', true);
			}
		});
	}
}