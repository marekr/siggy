<form role="form" action="<?php echo URL::base(TRUE,TRUE); ?>manage/settings/statistics" method="POST">
	<fieldset>
		<legend>Statistics</legend>
		<?php echo formRenderer::yesNo('Record pilot jump statistics?', 'recordJumps', $group->record_jumps, 'If yes, siggy will gather per hour, the character jump totals similar to the eve API jumps for systems and display them together as a comparison statistic. Of course this setting depends on users having siggy open in order for the jumps to be recorded.', $errors); ?>
		<?php echo formRenderer::yesNo('Record usage statistics?', 'statsEnabled',  $group->stats_enabled, 'If yes, siggy will record the number of sigs added, number of sigs edited and WHs mapped on a daily basis for each character. The recorded data is only currently avaliable as the in group stats board viewable by anyone who has access to your group.', $errors); ?>
	</fieldset>
	
	<fieldset>
		<legend>Leaderboard Point Multipliers</legend>
		<?php echo formRenderer::input('Signature Addition Point Multiplier', 'stats_sig_add_points', $group->stats_sig_add_points, 'Point multiplier for each signature addition.  Valid values are from 0 to 1000. Decimal values permitted.', $errors); ?>
		<?php echo formRenderer::input('Signature Update Point Multiplier', 'stats_sig_update_points', $group->stats_sig_update_points, 'Point multiplier for each signature update.  Valid values are from 0 to 1000. Decimal values permitted.', $errors); ?>
		<?php echo formRenderer::input('Wormholes Mapped Multiplier', 'stats_wh_map_points', $group->stats_wh_map_points, 'Point multiplier for each wormhole mapped. Valid values are from 0 to 1000. Decimal values permitted.', $errors); ?>
		<?php echo formRenderer::input('POS Addition Multiplier', 'stats_pos_add_points', $group->stats_pos_add_points, 'Point multiplier for each POS addition. Valid values are from 0 to 1000. Decimal values permitted.', $errors); ?>
		<?php echo formRenderer::input('POS Update Multiplier', 'stats_pos_update_points', $group->stats_pos_update_points, 'Point multiplier for each POS update. Valid values are from 0 to 1000. Decimal values permitted.', $errors); ?>
	</fieldset>
	
	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Save changes</button>
		<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
	</div> 			
</form>