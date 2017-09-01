/*
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import $ from 'jquery';

export default class HotkeyHelper {
	
	public siggyMain;

	public initialize() {
		var $this = this;
		$('#hotkey-button').click( function()
		{
			$this.siggyMain.openBox("#hotkey-helper");
		});
		
		this.initializeHotkeys();
	}


	public initializeHotkeys()
	{
		var $this = this;
		$(document).on('keydown', null, 'ctrl+/', function(){
			$this.siggyMain.openBox("#hotkey-helper");
		});
	}

	public registerHotkey(keyString, description)
	{
		var row = $('<tr>');

		var key = $('<td>').text(keyString);
		var desc = $('<td>').text(description);

		row.append(key).append(desc);
		$("#hotkey-list").append(row);
	}
}