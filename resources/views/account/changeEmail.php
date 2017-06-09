	{!! Form::open(['url' => 'account/changePassword']) !!}
		<legend>Change Email Address</legend>
		<p>You must reenter your current password to change your email address. A confirmation mail will be sent to the new email address before the change is accepted.</p>
		<div class="control-group">
			<label class="control-label" for="inputPassword">Current Password</label>
			<div class="controls">
				<input type="password" name="password" id="inputPassword">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="inputEmail">New Email Address</label>
			<div class="controls">
				<input type="text" name="email" id="inputEmail">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="inputConfirmEmail">Confirm New Email Address</label>
			<div class="controls">
				<input type="text" name="email_confirm" id="inputConfirmEmail">
			</div>
		</div>
		<div class="form-actions">
		  <button type="submit" class="btn btn-primary">Save changes</button>
		  <button type="button" class="btn">Cancel</button>
		</div>
	{!! Form::close() !!}

