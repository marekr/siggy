
    <div id="message">
					<h1>Login Required</h1>
					<div id="authBox">
						If you have a siggy account,please login below. Otherwise you must <a href='<?php echo URL::base(TRUE, TRUE);?>account/register'>register</a> an account before being able to continue.<br /><br />
						<?php if( $invalidLogin == true ): ?>
							<p id="passError">You have entered invalid login details.</p>
						<?php endif; ?>
						<form action='<?php echo URL::base(TRUE, TRUE);?>/account/login' method='POST'>
							<label for='username'>Username</label> <input type='text' id='username' name='username'<?php if( isset($username) ): ?> value='<?php echo $username; ?>'<?php endif;?>/><br /><br />
							<label for='password'>Password</label> <input type='password' id='password' name='password' /><br /><br />
							<input type='submit' name='login' value='Login' />
							
						</form>
						<br />
						<p style='font-size:0.9em;'>Lost or forgot password? <a href='<?php echo URL::base(TRUE, TRUE);?>account/forgotpassword'>Click here</a></p>
          </div>
    </div>