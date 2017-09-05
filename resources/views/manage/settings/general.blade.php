@extends('layouts.manage',[
							'title' => 'siggy.manage: chainmap settings'
						])

@section('content')
{!! Form::model($group, ['url' => 'manage/settings/general', 'method' => 'post']) !!}

<fieldset>
	<legend>Group Settings</legend>

		<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
			{!! Form::label('name', "Name", ['class' => 'control-label']) !!}
			
			<div class="controls">
				{!! Form::text('name', null, ['class' => 'form-control']) !!}
				<span class="help-block">The name of your group be it alliance, corp or whatever. This is not important</span>
				<span class="help-block with-errors">{{ $errors->first('name', ':message') }}</span>
			</div>
		</div>

		<div class="form-group">
			{!! Form::label('ticker', "Ticker", ['class' => 'control-label']) !!}
			
			<div class="controls">
				{!! Form::text('ticker', null, ['class' => 'form-control']) !!}
				<span class="help-block">'The ticker of your group be it alliance, corp or whatever</span>
				<span class="help-block with-errors">{{ $errors->first('ticker', ':message') }}</span>
			</div>
		</div>
</fieldset>

<fieldset>
	<legend>Miscellaneous</legend>
	
	<div class="form-group">
		{!! Form::label('show_sig_size_col', "Enable 'size' for sig entry?", ['class' => 'control-label']) !!}
		<div class="controls">
			{!! Form::checkbox('show_sig_size_col', 1, null, ['class' => 'form-control yesno']) !!}
		</div>
	</div>

	<div class="form-group">
		{!! Form::label('default_activity', "Default activity", ['class' => 'control-label']) !!}
		<div class="controls">
			{!! Form::select('default_activity', ['' => 'None', 'scan' => 'Scan', 'thera' => 'Thera', 'scannedsystems' => 'Scanned Systems'], null, ['class' => 'form-control']) !!}
			<span class="help-block with-errors">{{ $errors->first('default_activity', ':message') }}</span>
		</div>
	</div>

</fieldset>

<fieldset>
	<legend>Auth</legend>
	
	<div class="form-group">
		{!! Form::label('password_required', "If yes, siggy will prompt for a password from all users, this is highly recommended.", ['class' => 'control-label']) !!}
		<div class="controls">
			{!! Form::checkbox('password_required', 1, null, ['class' => 'form-control yesno']) !!}
			<span class="help-block with-errors">{{ $errors->first('password_required', ':message') }}</span>
		</div>
	</div>

	<div class="form-group">
		{!! Form::label('password', "Group password", ['class' => 'control-label']) !!}
		
		<div class="controls">
			{!! Form::password('password', ['class' => 'form-control']) !!}
			<span class="help-block">Only enter in a password here and confirm it below if you are trying to set or change the group password, otherwise leave blank and it won't get reset/changed</span>
			<span class="help-block with-errors">{{ $errors->first('password', ':message') }}</span>
		</div>
	</div>
	
	<div class="form-group">
		{!! Form::label('password_confirmation', "Confirm Group password", ['class' => 'control-label']) !!}
		
		<div class="controls">
			{!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
			<span class="help-block with-errors">{{ $errors->first('password_confirmation', ':message') }}</span>
		</div>
	</div>
	
	{!! Form::actionButtons() !!}
</fieldset>

{!! Form::close() !!}

@endsection