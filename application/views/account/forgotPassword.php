
<div class="container">
    <div class="row">
		<div class="well">
			<h2>Forgot Password</h2>
			<p>Please enter the email address of the account you are trying to recover.</p>
			
			<?php if( !empty($errors) ): ?>
			<div class="alert alert-danger">
				<h4>Error</h4>
			<?php foreach($errors as $error): ?>
				<?php echo $error; ?>
			<?php endforeach; ?>
			</div>
			<?php endif; ?>
				
				
			<form action='<?php echo URL::base(TRUE, TRUE);?>account/forgotPassword' method='POST'>
				<?php echo formRenderer::input('Email', 'reset_email', '', '', $errors); ?>
				<div class="form-group">
					<button type="submit" class="btn btn-primary">Request Password Reset</button>
				</div>
			</form>
		</div>
	</div>
</div>