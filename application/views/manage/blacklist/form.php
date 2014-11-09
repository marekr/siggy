<?php
$formUrl =  URL::base(TRUE,TRUE).'manage/blacklist/add';
?>
   <form role="form" action="<?php echo $formUrl; ?>" method="POST">
   <h2>Add Character to Blacklist</h2>
        <?php echo formRenderer::input('Character Name', 'character_name', $data['character_name'], 'EVE character name. This must be the full and complete name.', $errors); ?>
        <?php echo formRenderer::input('Reason', 'reason', $data['reason'], 'Reason why the character is blacklisted, it will be displayed to the character. This can be left empty', $errors); ?>
   

		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Save</button>
			<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
		</div>
   </form>