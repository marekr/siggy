
@extends('layouts.public',['layoutMode' => 'blank', 'title' => 'register', 'selectedTab'=>'register'])

@section('content')
<div class="container">
	<div class="row">
		<div class="well">
			<h2>Register</h2>
				<p>To create an account, please fill in the fields below.</p>
				<p>
					Username, password and email address <b>DO NOT</b> have to be the same as your EVE account.
				</p>
				<p>
					It is recommended you do not use the same same combination of username and password as your EVE account.
				</p>

				{!! Form::open(['url' => 'account/register']) !!}
				<fieldset>
					<legend>Account Details</legend>
					{!! Form::bsText('username', 'Username') !!}
					{!! Form::bsText('email', 'Email') !!}
					{!! Form::bsPassword('password', 'Password') !!}
					{!! Form::bsPassword('password_confirmation', 'Confirm Password') !!}
					
					{!! htmlFormSnippet() !!}
					<div class="form-group">
						<div class="controls">
						<button type="submit" name="register" class="btn btn-primary">Register</button>
						</div>
					</div>
				</fieldset>
				{!! Form::close() !!}
		</div>
	</div>
</div>
@endsection