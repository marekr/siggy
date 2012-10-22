
    <div class="well">
					<h1>Complete Password Reset</h1>
						<p>Please enter your account's email address and the reset token provided in the email sent to you.</p>
						<form action='<?php echo URL::base(TRUE, TRUE);?>account/completePasswordReset' method='POST' class='form-horizontal'>	
							<br />
							<div class="control-group">
								<label class="control-label" for="inputEmail">Account Email</label>
								<div class="controls">
									<input type="text" id="inputEmail" name='reset_email' placeholder="">
								</div>
							</div>
							
							<div class="control-group">
								<label class="control-label" for="inputToken">Reset Token</label>
								<div class="controls">
									<input type="text" id="inputToken" name='reset_token' placeholder="">
								</div>
							</div>
							
							<div class="form-actions">
								<button type="submit" name='proceed' class="btn btn-primary">Proceed with Reset</button>
							</div>
						</form>
    </div>