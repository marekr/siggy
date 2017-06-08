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
			<?php if( $wrongPass == true ): ?>
			<p id="passError">You have entered an incorrect password!</p>
			<?php endif; ?>
			<form action='{{url('access/group_password')}}' method='POST' class="center-text">
				<input type='password' name='group_password' /><br /><br />
				<input type='submit' value='Submit' style="padding:10px" />
			</form>
		</div>
	</div>
</div>
@endsection