
@extends('layouts.public',['layoutMode' => 'blank', 'title' => 'siggy: complete password reset', 'selectedTab'=>'login'])

@section('content')
<div class="container">
    <div class="row">
		<div class="well">
			<h2>Complete Password Reset</h2>
			<p>Please enter your account's email address and the reset token provided in the email sent to you.</p>
			
			{!! Form::open(['url' => 'account/completePasswordReset']) !!}
				{!! Form::bsText('reset_email', 'Account Email') !!}
				{!! Form::bsText('reset_token', 'Reset Token') !!}
			
				<div class="form-group">
					<button type="submit" name='proceed' class="btn btn-primary">Proceed with Reset</button>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@endsection