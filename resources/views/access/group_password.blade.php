@extends('layouts.siggy')

@section('alt_content')

<style type='text/css'>
#message
{
	width: 30%;
	margin: 200px auto 0;
}

#message h1
{
	font-weight:bold;
	font-size:1.3em;
}

#authBox
{
	background: #000;
	text-align:center;
	padding:20px;
}

#passError
{
	background: none repeat scroll 0 0 #910E0E;
	margin-bottom: 15px;
	padding: 4px;
}
</style>

<div id="message">
	<div class="box">
		<div class="box-header">Authentication Required</div>
		<div class="box-content">
			<p style='font-weight:bold'>
			In order to continue, please enter your group's password below.</p>
			<p>	This password should have been provided by your group in a bulletin, mail, etc.</p>
			@if( $wrongPass == true )
			<p id="passError">You have entered an incorrect password!</p>
			@endif
			{!! Form::open(['url' => 'access/group_password','class'=>'center-text','autocomplete' => 'off']) !!}
				<div class="form-group">
					<input type='password' name='group_password' class='form-control' />
				</div>
				<div class="form-group text-right">
					<button type='submit' class='btn btn-primary'  style='width:100%'>Submit</button>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@endsection