@extends('layouts.public',[
							'title' => 'create group',
							'selectedTab' => 'createGroup',
							'layoutMode' => 'blank'
						])

@section('content')
<div class="container">
	<div class="row">
		<div class="well">
			<h2>siggy Group Creation</h2>
			@if(count($errors))
			<div class="alert alert-error">
				<strong>You must fix the following errors before proceeding:</strong>
				<ul>
				@foreach($errors as $error)
					<li><?php echo $error; ?></li>
				@endforeach
				</ul>
			</div>
			@endif
			{!! Form::open(['url' => 'group/create/form']) !!}
				<fieldset>
					<legend>General Info</legend>
					{!! Form::bsText('name', 'Name') !!}
					{!! Form::bsText('ticker', 'Ticker') !!}
				</fieldset>
				<fieldset>
				<legend>Basic Settings</legend>
					{!! Form::yesNo('password_required', 'Group password required?', 'If yes, siggy will prompt for a password from all users, this is highly recommended.') !!}

					{!! Form::bsPassword('password', 'Group Password') !!}
					{!! Form::bsPassword('password_confirmation', 'Confirm Current Password') !!}
				</fieldset>
				<div class="form-actions">
					<button type="submit" class="btn btn-primary">Create Group</button>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@endsection