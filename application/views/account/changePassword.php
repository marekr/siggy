	<form class="form-horizontal"  action='<?php echo URL::base(TRUE, TRUE);?>account/changePassword' method='POST'>
		<legend>Change Password</legend>
		<p>You can change your password as many times as you wish. Please be smart about your choice of new password! Minimum password length is 8</p>
		<?php echo formRenderer::password('Password', 'current_password', '', $errors); ?>
		<?php echo formRenderer::password('New Password', 'password', '', $errors); ?>
		<?php echo formRenderer::password('Confirm New Password', 'password_confirm', '', $errors); ?>
		<div class="form-actions">
		  <button type="submit" class="btn btn-primary">Save changes</button>
		  <button type="button" class="btn">Cancel</button>
		</div>
	</form>

