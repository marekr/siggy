	<h3>Change Password</h3>
	<div class="well">
		<form action='<?php echo URL::base(TRUE, TRUE);?>account/changePassword' method='POST'>
			<fieldset>
				<p>You can change your password as many times as you wish. Please be smart about your choice of new password!</p>
				<p>Minimum password length is 8</p>
				<?php echo formRenderer::password('Password', 'current_password', '', '', $errors); ?>
				<?php echo formRenderer::password('New Password', 'password', '', '', $errors); ?>
				<?php echo formRenderer::password('Confirm New Password', 'password_confirm', '', '', $errors); ?>

				<div class="form-group">
					<div class="controls">
						<button type="submit" class="btn btn-primary">Save changes</button>
						<button type="button" class="btn">Cancel</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>