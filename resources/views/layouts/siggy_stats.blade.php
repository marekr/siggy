<!DOCTYPE html>
<html lang="en">
	<head>
		<title>siggy: stats</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link href="{{asset("bootstrap3/css/bootstrap.min.css?".SIGGY_VERSION)}}" rel="stylesheet">
		@if( App::environment('local') )
		<link type="text/css" href="{{url("theme.php?id={$settings->theme_id}&{ time() }")}}" id="theme-css" rel="stylesheet" media="screen" />
		@else
		<link type="text/css" href="{{url("theme.php?id={$settings->theme_id }")}}" id="theme-css" rel="stylesheet" media="screen" />
		@endif
		<link href="{{asset("font-awesome-4.2.0/css/font-awesome.min.css")}}" rel="stylesheet">
		<link href="{{asset("css/frontend.css?".SIGGY_VERSION)}}" rel="stylesheet">
		<link rel="icon" href="{{asset("favicon.ico")}}">

		@include('layouts._javascript')
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<style text='text/css'>
			table {
				background-color: rgb(29, 21, 21);
			}
		</style>
	</head>
	<body>
		<div class="navbar navbar-default navbar-fixed-top">
			<ul class="nav navbar-nav">
				<li>
					<a href="#" class="siggy-navbar-brand" data-toggle="dropdown" role="button" aria-expanded="false">
					<span>siggy</span>
					<span id="current-activity">stats</span>
					</a>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Support <i class="fa fa-caret-down"></i>
					</a>
					<ul class="dropdown-menu" role="menu">
						<li><a target="_blank" href="http://wiki.siggy.borkedlabs.com">Guide</a></li>
						<li><a target="_blank" href="{{ url("changelog") }}">Changelog</a></li>
						<li><a target="_blank" href="http://wiki.siggy.borkedlabs.com/support/contact/">Contact</a></li>
					</ul>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li>
					<a href="{{url('/')}}">Back to scanning!</a>
				</li>
				@if( count(Auth::$session->accessibleGroups()) > 1 )
				<li class="dropdown">
					<a data-toggle="dropdown" href="#">
						{{ substr($group->name,0,10) }}&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down"></i>
					</a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
						@foreach( Auth::$session->accessibleGroups() as $g )
							<li onClick="javascript:$(this).find('form').submit();">
								<a>
								<form action='{{ url('access/groups') }}' method='POST'>
									{{ csrf_field() }}
									<input type='hidden' name='group_id' value='{{ $g->id }}' />
									{{ $g->name }}
								</form>
								</a>
							</li>
						@endforeach
					</ul>
				</li>
				@else
				<li>
					<a href="#">
						{{ substr($group->name,0,10) }}
					</a>
				</li>
				@endif

				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
						 <img class="navbar-eve-image" src="https://image.eveonline.com/Corporation/{{ Auth::$session->corporation_id }}_64.png" height="32px"/>
						 <img class="navbar-eve-image" src="https://image.eveonline.com/Character/{{ Auth::$session->character_id }}_64.jpg" height="32px"/>
						{{ Auth::$session->character_name }}
						@if( Auth::loggedIn() )
						<i class="fa fa-caret-down"></i>
						@endif
					</a>
					@if( Auth::loggedIn() )
					<ul class="dropdown-menu" role="menu">
						<li><a href="{{url('account/characters')}}">Switch Character</a></li>
						<li class="divider"></li>
						<li><a href="{{url('account/connected')}}">Connected Characters</a></li>
						<li><a href="{{url('account/changePassword')}}">Change Password</a></li>
						<li class="divider"></li>
						<li><a href="{{url('account/logout')}}">Logout</a></li>
					</ul>
					@endif
				</li>
				<li><a></a></li>
			</ul>
		</div>
		@yield('content')

		<div class="container" style="font-size:0.9em;">
			<div class="row">
				<p style="width:33%;float:left;text-align:left;">
				&copy; 2011-{{ date("Y") }} borkedLabs
				</p>

				<p style="width:33%;float:right;text-align:right;">
				@if( defined("SIGGY_VERSION") )
					siggy version: {{ SIGGY_VERSION }}
				@endif
				</p>
			</div>
		</div>
	</body>
</html>
