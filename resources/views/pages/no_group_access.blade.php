@extends('layouts.public',[
							'title' => 'no group access',
							'selectedTab' => 'about',
							'layoutMode' => 'blank'
						])

@section('content')

<div class="container">
	<div class="row">
		<div class="well">
		<h2>Welcome to siggy!</h2>
		<p>
		Sorry, but you currently do not have access to siggy for your either your character or corporation.
		</p>
		<p>
			If your corporation and/or character is supposed to have siggy access, please contact your siggy group administrator.
		</p>
		<p>
			If you want to obtain siggy access, please read the guide here:<br />
			<a href="http://wiki.siggy.borkedlabs.com/getting-siggy">http://wiki.siggy.borkedlabs.com/getting-siggy</a>
		</p>
	</div>
</div>
@endsection