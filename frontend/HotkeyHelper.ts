/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import $ from 'jquery';
import { Siggy as SiggyCore } from './Siggy';

export default class HotkeyHelper {
	
	private core: SiggyCore;

	constructor(core: SiggyCore) {
		this.core = core;
	}

	public initialize() {
		var $this = this;
		$('#hotkey-button').click( function()
		{
			$this.core.openBox("#hotkey-helper");
		});
		
		this.initializeHotkeys();
	}


	public initializeHotkeys()
	{
		var $this = this;
		$(document).on('keydown', null, 'ctrl+/', function(){
			$this.core.openBox("#hotkey-helper");
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