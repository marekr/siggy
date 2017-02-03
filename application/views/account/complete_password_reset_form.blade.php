
@extends('layouts.public',['layoutMode' => 'blank', 'title' => 'siggy: complete password reset', 'selectedTab'=>'login'])

@section('content')
<div class="container">
    <div class="row">
		<div class="well">
			<h2>Complete Password Reset</h2>
			<p>Please enter your account's email address and the reset token provided in the email sent to you.</p>
			<form action='<?php echo URL::base(TRUE, TRUE);?>account/completePasswordReset' method='POST'>	
				<?php echo formRenderer::input('Account Email', 'reset_email', '', '',$errors); ?>
				<?php echo formRenderer::input('Reset Token', 'reset_token', '', '',$errors); ?>
				
				<div class="form-group">
					<button type="submit" name='proceed' class="btn btn-primary">Proceed with Reset</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection