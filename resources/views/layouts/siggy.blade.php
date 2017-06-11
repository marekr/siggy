<!DOCTYPE html>
<html lang="en">
	<head>
		<title>siggy</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		@if( App::environment('local') )
		<link type="text/css" href="{{ url('theme.php?id='. $settings->theme_id.'&'.time()) }}" id="theme-css" rel="stylesheet" media="screen" />
		@else
		<link type="text/css" href="{{ url('theme.php?id='.$settings->theme_id)}}" id="theme-css" rel="stylesheet" media="screen" />
		@endif
		<link href="{{asset('font-awesome-4.2.0/css/font-awesome.min.css')}}" rel="stylesheet">
		<link rel="icon" href="{{asset('favicon.ico')}}">

		@include('layouts._javascript')
		<meta name="csrf-token" content="{{ Auth::$session->csrf_token }}">
	</head>
	<body>
		<div class="navbar navbar-default navbar-fixed-top">
			<ul class="nav navbar-nav">
				@if( $group != null )
				<li class="dropdown">
					<a href="#" class="dropdown-toggle siggy-navbar-brand" data-toggle="dropdown" role="button" aria-expanded="false">
					<span>siggy</span>
					<span id="current-activity">scan</span> <span id="main_icon" class="glyphicon glyphicon-align-justify"></span> <i class="fa fa-caret-down"></i>
					</a>
					<ul class="dropdown-menu siggy-main-navbar" role="menu">
						<li><a class="activity-menu-option" data-activity="siggy" style="display:none"><span class="glyphicon glyphicon-list"></span> scan</a></li>
					<!--     <li><a class="activity-menu-option" data-activity="astrolabe"><span class="glyphicon glyphicon-map-marker"></span> Astrolabe</a></li> -->
						<li><a class="activity-menu-option" data-activity="scannedsystems" style="display:none"><span class="glyphicon glyphicon-list"></span> Scanned Systems</a></li>
						<li><a class="activity-menu-option" data-activity="thera"><span class="glyphicon glyphicon-magnet"></span> Thera</a></li>
					<!--  	<li><a class="activity-menu-option" data-activity="homestead"><span class="glyphicon glyphicon-globe"></span> Homestead</a></li>-->
						<li><a class="activity-menu-option" data-activity="notifications"><span class="glyphicon glyphicon-bell"></span> Notifications</a></li>
						<li><a class="activity-menu-option" data-activity="search"><span class="glyphicon glyphicon-search"></span> Search</a></li>
						<li role="separator" class="divider"></li>

						<li><a id="global-notes-button"><span class="glyphicon glyphicon-folder-close"></span> Notes</a></li>
						<li><a target="_blank" href="{{ url('stats') }}"><span class="glyphicon glyphicon-list"></span> Stats</a></li>
						<li id="settings-button"><a><span class="glyphicon glyphicon-cog"></span> Settings</a></li>
						@if( count(Auth::$user->perms()) > 0 )
						<li><a href="{{ url('manage') }}"><span class="glyphicon glyphicon-home"></span> Admin</a></li>
						@endif
					</ul>
				</li>
				@else
				<li class="dropdown">
					<a href="#" class="dropdown-toggle siggy-navbar-brand" data-toggle="dropdown" role="button" aria-expanded="false">
						<span>siggy</span>
					</a>
					@if( count(Auth::$user->perms()) > 0 )
					<ul class="dropdown-menu siggy-main-navbar" role="menu">
						<li><a href="{{ url('manage') }}"><span class="glyphicon glyphicon-home"></span> Admin</a></li>
					</ul>
					@endif
				</li>
				@endif
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Support <i class="fa fa-caret-down"></i>
					</a>
					<ul class="dropdown-menu" role="menu">
						<li><a target="_blank" href="http://wiki.siggy.borkedlabs.com">Guide</a></li>
						<li><a target="_blank" href="{{ url('changelog') }}">Changelog</a></li>
						<li><a target="_blank" href="http://wiki.siggy.borkedlabs.com/support/contact/">Contact</a></li>
					</ul>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				@if( $group != null )
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

					<li id="notification-header-dropdown" class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" id="notifications-button">
							<i class="fa fa-bell-o fa-lg"></i>
							<span id="notification-count"></span>
						</a>
						<ul id="notifications-menu" class="dropdown-menu" role="menu">
							<li class='notification-dropdown-view-link'><a>View all notifications</a></li>
						</ul>
					</li>
				@else
					<li>
						<a href="#">
							No Group
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
		@include('siggy.activities.search')
		@include('siggy.activities.thera')
		@include('siggy.activities.scanned_systems')
		@include('siggy.activities.notifications')
		@include('siggy.activities.astrolabe')
		@include('siggy.activities.chainmap')
		@yield('content')

		@yield('alt_content')

		<div id="footer-link" class="wrapper" style="font-size:0.9em;">
			<p style="width:33%;float:left;text-align:left;">
			&copy; 2011-{{ date("Y") }} borkedLabs
			</p>
			<p style="width:33%;float:left;text-align:center;">
				Last Update: <span class="updateTime" title='Last update received'>00:00:00</span>
			</p>
			<p style="width:33%;float:left;text-align:right;">
			@if( defined("SIGGY_VERSION") )
				siggy version: {{ SIGGY_VERSION }}
			@endif
			</p>
		</div>
	</body>
</html>
