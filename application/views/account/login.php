
<div class="offset3 span6">
	<div class="well">


		<form class="form-horizontal" action='<?php echo URL::base(TRUE, TRUE);?>/account/login' method='POST'>
			<input type="hidden" name="bounce" value="<?php echo $bounce; ?>" />
			<legend>Login</legend>
			
			
		<?php if( $invalidLogin == true ): ?>
		<div class="alert alert-error">
			You have entered invalid login details.
		</div>
		<?php endif; ?>
			<div class="control-group">
				<label class="control-label" for="username">Username</label>
				<div class="controls">
					<input type="text" id="username" name="username" placeholder="Username"<?php if( isset($username) ): ?> value='<?php echo $username; ?>'<?php endif;?>>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="inputPassword">Password</label>
				<div class="controls">
					<input type="password" name="password" id="inputPassword" placeholder="Password">
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<label class="checkbox">
						<input name="rememberMe" type="checkbox"> Remember me?
					</label>
					<br />
					<button type="submit" name="login" class="btn">Sign in</button>
				</div>
			</div>
		</form>

		<span class='help-block text-centered'>Lost or forgot password? <a href='<?php echo URL::base(TRUE, TRUE);?>account/forgotPassword'>Click here</a></span>

	</div>
</div>