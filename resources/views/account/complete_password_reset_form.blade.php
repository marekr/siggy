
@extends('layouts.public',['layoutMode' => 'blank', 'title' => 'siggy: complete password reset', 'selectedTab'=>'login'])

@section('content')
<div class="container">
    <div class="row">
		<div class="well">
			<h2>Complete Password Reset</h2>
			<p>Please enter the new password for your account</p>
			
			{!! Form::open(['url' => 'account/password_reset/'.$token]) !!}
				{!! Form::bsPassword('password', 'New Password') !!}
				{!! Form::bsPassword('password_confirmation', 'Confirm New Password') !!}
			
				<div class="form-group">
					<button type="submit" name='proceed' class="btn btn-primary">Save</button>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@endsection