	<form class="form-horizontal"  action='<?php echo URL::base(TRUE, TRUE);?>account/changePassword' method='POST'>
		<legend>Change Password</legend>
		<p>You can change your password as many times as you wish. Please be smart about your choice of new password! Minimum password length is 8</p>
		<div class="control-group">
			<label class="control-label" for="inputPassword">Current Password</label>
			<div class="controls">
				<input type="password" name="current_password" id="inputPassword" placeholder="Current Password">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="inputPassword">New Password</label>
			<div class="controls">
				<input type="password" name="password" id="inputPassword" placeholder="New Password">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="inputPassword">Confirm New Password</label>
			<div class="controls">
				<input type="password" name="password_confirm" id="inputPassword" placeholder="Confirm New Password">
			</div>
		</div>
		<div class="form-actions">
		  <button type="submit" class="btn btn-primary">Save changes</button>
		  <button type="button" class="btn">Cancel</button>
		</div>
	</form>

