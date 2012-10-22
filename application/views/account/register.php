<div class="well">
				<h2>Register</h2>
				<p>To create an account, please fill in the fields below. All passwords are salted when stored and are secured, your email address is only used for password recovery. <br />
				 Username, password and email address <b>DO NOT</b> have to be the same as your EVE account. It is advised to not use the same same combination of username and password. <br />
				  You will be asked for an API key on a different page.</p>
				  
				<form action='<?php echo URL::base(TRUE, TRUE);?>account/register' class="form-horizontal" method='POST'>		
							
					<legend>Account Details</legend>
					<div class="control-group">
						<label class="control-label" for="inputUsername">Username</label>
						<div class="controls">
							<input type="text" id="inputUsername" name="username" placeholder="Username">
							<?php if( isset($errors['username']) ): ?>
								<span class="help-inline"><?php echo $errors['username']; ?></span>
							<?php endif; ?>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label" for="inputEmail">Email</label>
						<div class="controls">
							<input type="text" id="inputEmail" name="email" placeholder="Email Address" autocomplete="off">
							<?php if( isset($errors['email']) ): ?>
								<span class="help-inline"><?php echo $errors['email']; ?></span>
							<?php endif; ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputPassword">Password</label>
						<div class="controls">
							<input type="password" id="inputPassword" name="password" autocomplete="off">
							<?php if( isset($errors['password']) ): ?>
								<span class="help-inline"><?php echo $errors['password']; ?></span>
							<?php endif; ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputConfirmPassword">Confirm Password</label>
						<div class="controls">
							<input type="password" id="inputConfirmPassword" name="password_confirm" autocomplete="off">
							<?php if( isset($errors['password_confirm']) ): ?>
								<span class="help-inline"><?php echo $errors['password_confirm']; ?></span>
							<?php endif; ?>
						</div>
					</div>
					<div class="form-actions">
						<button type="submit" name="register" class="btn">Register</button>
					</div>
				</form>
</div>