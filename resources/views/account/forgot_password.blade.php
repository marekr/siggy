
@extends('layouts.public',['layoutMode' => 'blank', 'title' => 'siggy: forgot password', 'selectedTab'=>'login'])

@section('content')
<div class="container">
	<div class="row">
		<div class="well">
			<h2>Forgot Password</h2>
			<p>Please enter the email address of the account you are trying to recover.</p>

			{!! Form::open(['url' => 'account/password_reset']) !!}
				{!! Form::bsText('reset_email', 'Email') !!}
				<div class="form-group">
					<button type="submit" class="btn btn-primary">Request Password Reset</button>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@endsection