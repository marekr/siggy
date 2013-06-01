
		<div class="span9">
			<div class="siggy-hero-unit hero-unit">
				<h2>WH Exploration Tool</h2>
				<p>Siggy is one of EVE's oldest WH scanning tool now 2 years old and counting! It has aided countless explorers of WH Space</p>
				<br />
			</div>
		</div>
		<div class="span3">
			<div class="well" style="min-width:250px;">
				<form class="form" action='<?php echo URL::base(TRUE, TRUE);?>/account/login' method='POST'>
				<legend>Login</legend>
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
							<input type="checkbox" name="rememberMe"> Remember me?
						</label>
						<br />
						<button type="submit" name="login" class="btn">Sign in</button>
					</div>
				</div>
			</form>

			<span class='help-block text-centered'>Lost or forgot password? <a href='<?php echo URL::base(TRUE, TRUE);?>account/forgotPassword'>Click here</a></span>
			</div>
		</div>
</div>
<div class="row">
		<div class="span9 hero-unit" style="width: 750px;">
		
				<p>Looking to gain access to your corp or alliance siggy? <a href="<?php echo URL::base(TRUE, TRUE);?>account/register" />Register here</a></p>
				<p>Looking to start using siggy for the first time?  <a href="<?php echo URL::base(TRUE, TRUE);?>pages/getting-siggy" />Click here for info</a></p>
		</div>
		<div class="span3">
		</div>