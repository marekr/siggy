
    <div class="well">
					<h1>Forgot Password</h1>
					<div id="authBox" class='miniForm'>
						<p>Please enter the email address of the account you are trying to recover.</p>
						
						<?php if( !empty($errors) ): ?>
						<div class="alert alert-error">
						<strong>Error</strong><br />
						<?php foreach($errors as $error): ?>
						<?php echo $error; ?>
						<?php endforeach; ?>
						</div>
						<?php endif; ?>
						<form action='<?php echo URL::base(TRUE, TRUE);?>account/forgotPassword' method='POST' class='form-horizontal'>
							<div class="control-group">
								<label class="control-label" for="inputEmail">Email</label>
								<div class="controls">
									<input type="text" id="inputEmail" name="reset_email">
								</div>
							</div>
							<div class="form-actions">
								<button type="submit" class="btn btn-primary">Request Password Reset</button>
							</div>
						</form>
          </div>
    </div>