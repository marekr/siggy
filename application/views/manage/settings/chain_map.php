<form role="form" action="<?php echo URL::base(TRUE,TRUE); ?>manage/settings/chain_map" method="POST">
	<fieldset>
		<legend>Basic</legend>
		<?php echo formRenderer::yesNo('Force location broadcasting?', 'alwaysBroadcast', $group->always_broadcast, 'If yes, broadcasting cannot be disabled by users.', $errors); ?>
		<?php echo formRenderer::yesNo('Display ship types for characters active in systems?', 'chain_map_show_actives_ships', $group->chain_map_show_actives_ships, 'If yes, ship types will display next to the characters on hover over a system for the full activity list of characters.', $errors); ?>
		<?php echo formRenderer::yesNo("Allow expanding map height?", 'allow_map_height_expand', $group->allow_map_height_expand, 'If yes, you will be able to expand the map to twice its height with a resize a', $errors); ?>
		<?php echo formRenderer::yesNo("Always show system class?", 'chainmap_always_show_class', $group->chainmap_always_show_class, 'By default, when you rename wormhole systems, the class gets removed since you may name the hole c5a, c5b, etc. This instead keeps it always displayed.', $errors); ?>
	
		<?php echo formRenderer::input('Max numbers of displayed characters per system', 'chainmap_max_characters_shown', $group->chainmap_max_characters_shown, 'Max number of characters displayed inside a system before the # pilots count appears. This can be any number.', $errors); ?>
	</fieldset>
	<fieldset>
		<legend>Jump Log</legend>	
		<?php echo formRenderer::yesNo('Enable jump log?', 'jumpLogEnabled',$group->jump_log_enabled, 'If yes, wormholes will have the jumps recorded. This is meant to display an approximate mass total and viewable in the chain map. ALL DATA regarding jumps is deleted immediately on wormhole deletion.', $errors); ?>
		<?php echo formRenderer::yesNo('Record pilot names?', 'jumpLogRecordNames', $group->jump_log_record_names, 'If yes, pilot names of ships will be recorced and displayed, otherwise they will be blank.', $errors); ?>
		<?php echo formRenderer::yesNo('Record time of jump?', 'jumpLogRecordTime',$group->jump_log_record_time, 'If yes, the time of the jump will be recorded and displayed, otherwise it will be blank.', $errors); ?>
		<?php echo formRenderer::yesNo('Display ship type?', 'jumpLogDisplayShipType', $group->jump_log_display_ship_type, 'If yes, the ship type will be displayed. Otherwise it will be recorded for mass calculation purposes but hidden.', $errors); ?>
	</fieldset>
	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Save changes</button>
		<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
	</div> 			
</form>