
<div class="offset4 span4">
	<div class="well">
        <legend>Login</legend>

		<form action='<?php echo URL::base(TRUE, TRUE);?>/account/login' method='POST'>
			<input type="hidden" name="bounce" value="<?php echo $bounce; ?>" />
			
			
			
            <?php if( $invalidLogin == true ): ?>
            <div class="alert alert-error">
                You have entered invalid login details.
            </div>
            <?php endif; ?>
            <input class="span3" type="text" id="username" name="username" placeholder="Username"<?php if( isset($username) ): ?> value='<?php echo $username; ?>'<?php endif;?>>
            <input class="span3" type="password" name="password" id="inputPassword" placeholder="Password">
            <label class="checkbox">
                <input name="rememberMe" type="checkbox"> Remember me?
            </label>
            <button type="submit" name="login" class="btn btn-primary">Sign in</button>
		</form>

		<span class='help-block text-centered'>Lost or forgot password? <a href='<?php echo URL::base(TRUE, TRUE);?>account/forgotPassword'>Click here</a></span>
	</div>
</div>