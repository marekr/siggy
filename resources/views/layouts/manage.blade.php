<!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <!-- Le styles -->
    <link href="{{ asset('bootstrap3/css/bootstrap-yeti.min.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome-4.2.0/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/manage.css') }}" rel="stylesheet">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="{{ asset('bootstrap3/js/bootstrap.min.js') }}'"></script>
    <script src="{{ asset('bootstrap3/js/bootstrap-checkbox.min.js') }}"></script>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

	<script type="text/javascript">
	$(function() {

	//	$('#side-menu').metisMenu();

	});

	$(function() {
		$(window).bind("load resize", function() {
			width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
			if (width < 768) {
				$('div.sidebar-collapse').addClass('collapse')
			} else {
				$('div.sidebar-collapse').removeClass('collapse')
			}
		})
	})
	</script>


    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="{{asset('favicon.ico')}}">
</head>
<body>
	<nav class="navbar navbar-inverse navbar-fixed-top navbar-siggy" role="navigation" style="margin-bottom: 0">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">siggy</a>
		</div>
		<!-- /.navbar-header -->

		<ul class="nav navbar-top-links navbar-right">
			<li>
				<a href="{{ url('/') }}">Back to scanning!</a>
			</li>
			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">
					<i class="fa fa-user fa-fw"></i> {{ Auth::$user->username }} <i class="fa fa-caret-down"></i>
				</a>
				<ul class="dropdown-menu dropdown-user">
					<li>
						@if( count($avaliableGroups) > 1 )
						<form action='{{ url('manage/admin/changeGroup') }}' method='post' style="padding: 6px;">
							<select name='group' class="form-control input-sm " onchange='submit();'>
							<?php
								$selected = Auth::$user->group->id; ?>
							@foreach( $avaliableGroups as $m )
								<option value="{{$m->id}}" <?php echo ( ($selected == $m->id) ? "selected='selected'" : ''); ?>>{{$m->name}}</option>
							@endforeach
							</select>
						</form>
						@endif
					</li>
					<li class="divider"></li>
					<li>
						<a href="{{url('account/logout')}}"><i class="fa fa-sign-out fa-fw"></i>Log out</a>
					</li>
				</ul>
				<!-- /.dropdown-user -->
			</li>
		</ul>
		<!-- /.navbar-top-links -->
		<div class="navbar-inverse navbar-static-side navbar-siggy" role="navigation">
			<div class="sidebar-collapse">
				<ul class="nav" id="side-menu">
					<li>
						<a href="{{url('manage/dashboard')}}"><i class="fa fa-sitemap fa-fw"></i>Admin Dashboard</a>
					</li>
					@if( Auth::$user->admin || $perms->can_manage_group_members || $perms->can_manage_access )
					<li class="active <?php echo ($controllerName == "Group"?" active" : "") ?>">
						<a href="#"><i class="fa fa-chain fa-fw"></i> Chainmaps<span class="fa arrow"></span></a>
						<ul class="nav nav-second-level">
							<li>
								<a href="{{url('manage/chainmaps/list')}}">Manage</a>
							</li>
						</ul>
						<!-- /.nav-second-level -->
					</li>
					<li class="active <?php echo ($controllerName == "Group"?" active" : "") ?>">
						<a href="#"><i class="fa fa-key fa-fw"></i> Access<span class="fa arrow"></span></a>
						<ul class="nav nav-second-level">
							@if( Auth::$user->admin || $perms->can_manage_group_members )
							<li>
								<a href="{{url('manage/group/members')}}">Group Members</a>
							</li>
							<li>
								<a href="{{url('manage/blacklist/list')}}">Character Blacklist</a>
							</li>
							@endif
							@if( Auth::$user->admin || $perms->can_manage_access )
							<li>
								<a href="{{url('manage/access/configure')}}">Admin Access</a>
							</li>
							@endif
						</ul>
						<!-- /.nav-second-level -->
					</li>
					@endif
					@if( Auth::$user->admin || $perms->can_manage_settings )
					<li class="active <?php echo ($controllerName == "Settings"?" active" : "") ?>">
						<a href="#"><i class="fa fa-wrench fa-fw"></i>Settings<span class="fa arrow"></span></a>
						<ul class="nav nav-second-level">
							<li>
								<a href="{{url('manage/settings/general')}}">General</a>
							</li>
							<li>
								<a href="{{url('manage/settings/chainmap')}}">Chain Map</a>
							</li>
							<li>
								<a href="{{url('manage/settings/statistics')}}">Statistics</a>
							</li>
						</ul>
						<!-- /.nav-second-level -->
					</li>
					@endif
					@if( Auth::$user->admin || $perms->can_view_logs )
					<li class="active <?php echo ($controllerName == "Logs"?" active" : "") ?>">
						<a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Activity<span class="fa arrow"></span></a>
						<ul class="nav nav-second-level">
							<li>
								<a href="{{url('manage/logs/activity')}}">Usage Logs</a>
							</li>
							<li>
								<a href="{{url('manage/logs/sessions')}}">Active Sessions</a>
							</li>
						</ul>
						<!-- /.nav-second-level -->
					</li>
					@endif
					@if( Auth::$user->admin || $perms->can_view_financial )
					<li class="active <?php echo ($controllerName == "Billing"?" active" : "") ?>">
						<a href="#"><i class="fa fa-university fa-fw"></i>Financial<span class="fa arrow"></span></a>
						<ul class="nav nav-second-level">
							<li>
								<a href="{{url('manage/billing/overview')}}">Billing Overview</a>
							</li>
						</ul>
						<!-- /.nav-second-level -->
					</li>
					@endif
				</ul>
				<!-- /#side-menu -->
			</div>
			<!-- /.sidebar-collapse -->
		</div>
		<!-- /.navbar-static-side -->
	</nav>
	<div id="page-wrapper">
		@include('flash::message')

		<div class="row">
			<div class="col-lg-12">
				@yield('content')
			</div>
		</div>
	</div>

	<script type='text/javascript'>
		$('input[type="checkbox"].yesno').checkboxpicker();
	</script>
</body>
</html>
