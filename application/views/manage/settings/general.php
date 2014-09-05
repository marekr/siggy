<form class="form-horizontal" action="<?php echo URL::base(TRUE,TRUE); ?>manage/settings/general" method="post">
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
		<?php echo formRenderer::select('Auth Mode', 'authMode', array(0 =>'No auth', 1 => 'Group password', 2 => 'API based Login'), $data['authMode'], "
		There are currently three states for auth, no auth and group password. <br />
						<b>No auth:</b> No authenication measures are taken when a person tries to access siggy. This isn't the best mode to use as people can spoof their corporation ID among other things.<br />
						<b>Group Password:</b> The password set below or in individual subgroups will be prompted the first time a user attempts to use siggy on a computer. The password will be remembered by their client until it is changed or client deletes its cookies.<br />
						<b>API based login:</b> Utilizing the same accounts as out of game login, this requires an siggy account with valid API key to access your group.
						", $errors); ?>
		<?php echo formRenderer::password('Group password', 'password', '', "Only enter in a password here and confirm it below if you are trying to set or change the group password, otherwise leave blank and it won't get reset/changed.", $errors); ?>
		<?php echo formRenderer::password('Confirm group password', 'password_confirm', '', "Only enter in a password here and confirm it below if you are trying to set or change the group password, otherwise leave blank and it won't get reset/changed.", $errors); ?>
	</fieldset>
  			
	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Save changes</button>
		<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
	</div> 			
</form>