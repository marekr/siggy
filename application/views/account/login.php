
<div class="container">
	<div class="row colored">
		<div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
			<div class="panel panel-default">
				<div class="panel-heading">
					<strong> Login in to continue</strong>
				</div>
				<div class="panel-body">
					<form role='form' action='<?php echo URL::base(TRUE, TRUE);?>/account/login' method='POST'>
						<input type="hidden" name="bounce" value="<?php echo $bounce; ?>" />
						
						<?php if( $invalidLogin == true ): ?>
						<div class="alert alert-danger">
							You have entered invalid login details.
						</div>
						<?php endif; ?>
								<div class="row">
									<div class="col-sm-12 col-md-10  col-md-offset-1 ">
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">
													<i class="glyphicon glyphicon-user"></i>
												</span> 
												<input class="form-control"  placeholder="Username"<?php if( isset($username) ): ?> value='<?php echo $username; ?>'<?php endif;?> name="username" type="text" autofocus >
											</div>
										</div>
										<div class="form-group">
											<div class="input-group">
												<span class="input-group-addon">
													<i class="glyphicon glyphicon-lock"></i>
												</span>
												<input class="form-control" placeholder="Password" name="password" type="password" value="">
											</div>
										</div>
										<div class="checkbox">
											<label class="pull-right">
												<input name="rememberMe" type="checkbox"> Remember me?
											</label>
										</div>
										<div class="form-group">
											<input type="submit" class="btn btn-lg btn-primary btn-block" value="Sign in">
										</div>
									</div>
								</div>
							</fieldset>
					</form>

					<span class='help-block text-centered'>Lost or forgot password? <a href='<?php echo URL::base(TRUE, TRUE);?>account/forgotPassword'>Click here</a></span>
				</div>
			</div>
		</div>
		</div>
	</div>
</div>
