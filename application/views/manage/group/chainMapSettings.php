<form class="form-horizontal" action="<?php echo URL::base(TRUE,TRUE); ?>manage/group/chainMapSettings" method="post">
	<legend>Basic</legend>
	<?php echo formRenderer::yesNo('Force location broadcasting?', 'alwaysBroadcast', $data['alwaysBroadcast'], 'If yes, broadcasting cannot be disabled by users.', $errors); ?>
	<legend>Jump Log</legend>	
	<?php echo formRenderer::yesNo('Enable jump log?', 'jumpLogEnabled', $data['jumpLogEnabled'], 'If yes, wormholes will have the jumps recorded. This is meant to display an approximate mass total and viewable in the chain map. ALL DATA regarding jumps is deleted immediately on wormhole deletion.', $errors); ?>
	<?php echo formRenderer::yesNo('Record pilot names?', 'jumpLogRecordNames', $data['jumpLogRecordNames'], 'If yes, pilot names of ships will be recorced and displayed, otherwise they will be blank.', $errors); ?>
  <?php echo formRenderer::yesNo('Record time of jump?', 'jumpLogRecordTime', $data['jumpLogRecordTime'], 'If yes, the time of the jump will be recorded and displayed, otherwise it will be blank.', $errors); ?>
  <?php echo formRenderer::yesNo('Display ship type?', 'jumpLogDisplayShipType', $data['jumpLogDisplayShipType'], 'If yes, the ship type will be displayed. Otherwise it will be recorded for mass calculation purposes but hidden.', $errors); ?>

	 <div class="form-actions">
		<button type="submit" class="btn btn-primary">Save changes</button>
		<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
	</div> 			
</form>