@extends('layouts.public',['layoutMode' => 'leftMenu', 'title' => 'change password', 'selectedTab'=>'account'])

@section('content')
	<h3>Change Password</h3>
	<div class="well">
		{!! Form::open(['url' => 'account/changePassword']) !!}
			<fieldset>
				<p>You can change your password as many times as you wish. Please be smart about your choice of new password!</p>
				<p>Minimum password length is 8</p>
					{!! Form::bsPassword('current_password', 'Current Password') !!}
					{!! Form::bsPassword('password', 'New Password') !!}
					{!! Form::bsPassword('password_confirmation', 'Confirm New Password') !!}

				<div class="form-group">
					<div class="controls">
						<button type="submit" class="btn btn-primary">Save changes</button>
						<button type="button" class="btn">Cancel</button>
					</div>
				</div>
			</fieldset>
		{!! Form::close() !!}
	</div>
@endsection

@section('left_menu')
@include('account.menu')
@endsection