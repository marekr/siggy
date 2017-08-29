/*
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

function hotkeyhelper()
{
}

hotkeyhelper.prototype.initialize = function()
{
	var $this = this;
	$('#hotkey-button').click( function()
	{
		$this.siggyMain.openBox("#hotkey-helper");
	});
	
	this.initializeHotkeys();
}


hotkeyhelper.prototype.initializeHotkeys = function()
{
	var $this = this;
	$(document).on('keydown', null, 'ctrl+/', function(){
		$this.siggyMain.openBox("#hotkey-helper");
	});
}

hotkeyhelper.prototype.registerHotkey = function(keyString, description)
{
	var row = $('<tr>');

	var key = $('<td>').text(keyString);
	var desc = $('<td>').text(description);

	row.append(key).append(desc);
	$("#hotkey-list").append(row);
}
