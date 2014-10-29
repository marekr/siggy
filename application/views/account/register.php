
<div class="container">
    <div class="row">
		<div class="well">
			<h2>Register</h2>
			<p>To create an account, please fill in the fields below. All passwords are salted when stored and are secured, your email address is only used for password recovery.</p>
			 <p>Username, password and email address <b>DO NOT</b> have to be the same as your EVE account. It is advised to not use the same same combination of username and password.</p>
			 <p> You will be asked for an API key on a different page.</p>
			 
			  
			<form role="form" action='<?php echo URL::base(TRUE, TRUE);?>account/register'  method='POST'>		
				<fieldset>
					<legend>Account Details</legend> 
					
					<?php echo formRenderer::input('Username', 'username', '', '', $errors); ?>
					<?php echo formRenderer::input('Email', 'email', '', '', $errors); ?>
					<?php echo formRenderer::password('Password', 'password', '', '', $errors); ?>
					<?php echo formRenderer::password('Confirm Password', 'password_confirm', '', '', $errors); ?>
					
					<div class="form-group">
						<div class="controls">
						<button type="submit" name="register" class="btn btn-primary">Register</button>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</div>