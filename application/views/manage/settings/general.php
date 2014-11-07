<form role="form" action="<?php echo URL::base(TRUE,TRUE); ?>manage/settings/general" method="POST">
	<fieldset>
		<legend>Group Settings</legend>
		<?php echo formRenderer::input('Group Name', 'groupName', $data['groupName'], 'The name of your group be it alliance, corp or whatever. This is not important', $errors); ?>
		<?php echo formRenderer::input('Group Ticker', 'groupTicker', $data['groupTicker'], 'The ticker of your group be it alliance, corp or whatever.', $errors); ?>
	</fieldset>

	<fieldset>
		<legend>Miscellaneous</legend>
		<?php echo formRenderer::yesNo("Enable 'size' for sig entry?", 'showSigSizeCol', $data['showSigSizeCol'], 'If yes, an additional column and dropdown for entry will appear for sigs to list its calculator size. ', $errors); ?>
	</fieldset>
  	
	<fieldset>
	<legend>Auth</legend>
		<?php echo formRenderer::yesNo("API login required?", 'api_login_required', $data['api_login_required'], 'If yes, users will have to log into an siggy account with an valid EVE API key to prove their identity.', $errors); ?>

		<?php echo formRenderer::yesNo("Group password required?", 'group_password_required', $data['group_password_required'], 'If yes, siggy will prompt for a password from all users, this is highly recommended.', $errors); ?>

		<?php echo formRenderer::password('Group password', 'password', '', "Only enter in a password here and confirm it below if you are trying to set or change the group password, otherwise leave blank and it won't get reset/changed.", $errors); ?>
		<?php echo formRenderer::password('Confirm group password', 'password_confirm', '', "Only enter in a password here and confirm it below if you are trying to set or change the group password, otherwise leave blank and it won't get reset/changed.", $errors); ?>
	
	</fieldset>
  			
	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Save changes</button>
		<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
	</div> 			
</form>