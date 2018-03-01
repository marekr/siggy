<!DOCTYPE html>
<html lang="en">
	<head>
		<title>siggy</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link href="{{asset("bootstrap3/css/bootstrap.min.css?".SIGGY_VERSION)}}" rel="stylesheet">
		@if( App::environment('local') )
		<link type="text/css" href="{{ url('theme.php?id='. $settings->theme_id.'&'.time()) }}" id="theme-css" rel="stylesheet" media="screen" />
		@else
		<link type="text/css" href="{{ url('theme.php?id='.$settings->theme_id)}}" id="theme-css" rel="stylesheet" media="screen" />
		@endif
		<link href="{{asset('font-awesome-4.7.0/css/font-awesome.min.css')}}" rel="stylesheet">

		@include('layouts._iconglue')
		@include('layouts._javascript')
		<meta name="csrf-token" content="{{ csrf_token() }}">
	</head>
	<body>
		<div class="navbar navbar-default navbar-fixed-top">
			<ul class="nav navbar-nav">
				@if( $group != null )
				<li class="dropdown">
					<a href="#" class="dropdown-toggle siggy-navbar-brand" data-toggle="dropdown" role="button" aria-expanded="false">
					<span>siggy</span>
					<span id="current-activity">scan</span> <i class="fa fa-align-justify" aria-hidden="true"></i> <i class="fa fa-caret-down"></i>
					</a>
					<ul class="dropdown-menu siggy-main-navbar" role="menu">
						<li><a class="activity-menu-option" href="/scan" data-navigo style="display:none" data-activity-key='scan'><i class="fa fa-bolt" aria-hidden="true"></i> scan</a></li>
						<li><a class="activity-menu-option" href="/scannedsystems" data-navigo data-activity-key='scannedsystems' style="display:none"><i class="fa fa-list" aria-hidden="true"></i> Scanned Systems</a></li>
						<li><a class="activity-menu-option" href="/thera" data-navigo data-activity-key='thera'><i class="fa fa-magnet" aria-hidden="true"></i> Thera</a></li>
						<li><a class="activity-menu-option" href="/notifications" data-navigo data-activity-key='notifications'><i class="fa fa-bell" aria-hidden="true"></i> Notifications</a></li>
						<li><a class="activity-menu-option" href="/search" data-navigo data-activity-key='search'><i class="fa fa-search" aria-hidden="true"></i> Search</a></li>
						<li><a class="activity-menu-option" href="/dscan" data-navigo data-activity-key='dscan' data-activity-hidden style="display:none"><i class="fa fa-wifi" aria-hidden="true"></i> DScan</a></li>
						<li><a class="activity-menu-option" href="/timerboard" data-navigo data-activity-key='timerboard'><i class="fa fa-wifi" aria-hidden="true"></i> Timer Board</a></li>
						<li role="separator" class="divider"></li>

						<li><a id="global-notes-button"><i class="fa fa-sticky-note" aria-hidden="true"></i> Notes</a></li>
						<li><a target="_blank" href="{{ url('stats') }}"><i class="fa fa-star" aria-hidden="true"></i> Stats</a></li>
						<li id="settings-button"><a><i class="fa fa-cog" aria-hidden="true"></i> Settings</a></li>
						@if( count(Auth::user()->perms()) > 0 )
						<li><a href="{{ url('manage') }}"><i class="fa fa-home" aria-hidden="true"></i> Admin</a></li>
						@endif
					</ul>
				</li>
				@else
				<li class="dropdown">
					<a href="#" class="dropdown-toggle siggy-navbar-brand" data-toggle="dropdown" role="button" aria-expanded="false">
						<span>siggy</span>
					</a>
					@if( count(Auth::user()->perms()) > 0 )
					<ul class="dropdown-menu siggy-main-navbar" role="menu">
						<li><a href="{{ url('manage') }}"><i class="fa fa-home" aria-hidden="true"></i> Admin</a></li>
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
					<li id="header-location-container">
						<p class="navbar-text">
							<i class="fa fa-map-marker fa-lg" aria-hidden="true" id="header-location-icon"></i> 
							<span id="header-location-content" class="offline">Unknown</span>
						</p>
					</li>
					@if( count(SiggySession::accessibleGroups()) > 1 )
					<li class="dropdown">
						<a data-toggle="dropdown" href="#">
							{{ substr($group->name,0,10) }}&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down"></i>
						</a>
						<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
							@foreach( SiggySession::accessibleGroups() as $g )
								<li onClick="javascript:$(this).find('form').submit();">
									<a>
									{!! Form::open(['url' => 'access/groups']) !!}
										<input type='hidden' name='group_id' value='{{ $g->id }}' />
										{{ $g->name }}
									{!! Form::close() !!}
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
							<li><a href='/notifications' data-navigo>View all notifications</a></li>
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
						 <img class="navbar-eve-image" src="https://image.eveonline.com/Corporation/{{ SiggySession::getCorporationId() }}_64.png" height="32px"/>
						 <img class="navbar-eve-image" src="https://image.eveonline.com/Character/{{ SiggySession::getCharacterId() }}_64.jpg" height="32px"/>
						{{ SiggySession::getCharacterName() }}
						@if( Auth::check() )
						<i class="fa fa-caret-down"></i>
						@endif
					</a>
					@if( Auth::check() )
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
