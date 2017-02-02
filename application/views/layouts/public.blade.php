<!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>

    <!-- Le styles -->
    <link href="<?php echo URL::base(TRUE, TRUE);?>bootstrap3/css/bootstrap.min.css?<?php echo SIGGY_VERSION; ?>" rel="stylesheet">
    <link href="<?php echo URL::base(TRUE, TRUE);?>css/frontend.css?<?php echo SIGGY_VERSION; ?>" rel="stylesheet">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="<?php echo URL::base(TRUE, TRUE);?>bootstrap3/js/bootstrap.min.js"></script>


    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="<?php echo URL::base(TRUE, TRUE);?>favicon.ico">

	<script type="text/javascript">
	$(document).ready(function() {
		$('.dropdown-toggle').dropdown()
	});
	</script>
</head>
<body>
	<div class="navbar navbar-default navbar-fixed-top">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo URL::base(TRUE, TRUE);?>"><img alt="Brand" width="24" src="<?php echo URL::base(TRUE, TRUE);?>images/siggy.png">siggy</a>
		</div>
		<div class="navbar-collapse collapse navbar-responsive-collapse">
			<ul class="nav navbar-nav">
				<li <?php echo ($selectedTab == 'home' ? 'class="active"' : ''); ?>><a href="<?php echo URL::base(TRUE, TRUE);?>">Home</a></li>

				<li><a href="http://wiki.siggy.borkedlabs.com">Guide</a></li>
				<li<?php echo ($selectedTab == 'about' ? ' class="active"' : ''); ?>><a href="<?php echo URL::base(TRUE, TRUE);?>pages/about">About</a></li>
				<li<?php echo ($selectedTab == 'announcements' ? ' class="active"' : ''); ?>><a href="<?php echo URL::base(TRUE, TRUE);?>announcements">Announcements</a></li>
				<li<?php echo ($selectedTab == 'changelog' ? ' class="active"' : ''); ?>><a href="<?php echo URL::base(TRUE, TRUE);?>changelog">Changelog</a></li>
				<li><a href="http://wiki.siggy.borkedlabs.com/support/contact/">Support</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<?php if( Auth::loggedIn() ): ?>

					<?php if( count(Auth::$user->perms()) > 0 ): ?>
				<li <?php echo ($selectedTab == 'admin' ? 'class="active"' : ''); ?>><a href="<?php echo URL::base(TRUE, TRUE);?>manage">Admin</a></li>
					<?php endif; ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Logged in as <?php echo Auth::$user->username; ?><b class="caret"></b></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="<?php echo URL::base(TRUE, TRUE);?>account/overview">Account</a></li>
						<li class="divider"></li>
						<li><a href="<?php echo URL::base(TRUE, TRUE);?>account/logout">Logout</a></li>
					</ul>
				</li>
				<?php else: ?>
				<li  <?php echo ($selectedTab == 'register' ? 'class="active"' : ''); ?>><a href="<?php echo URL::base(TRUE, TRUE);?>account/register">Register</a></li>
				<li  <?php echo ($selectedTab == 'login' ? 'class="active"' : ''); ?>><a href="<?php echo URL::base(TRUE, TRUE);?>account/login">Login</a></li>
				<?php endif; ?>
				<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
			</ul>
		</div>
	</div>
	<?php if( $layoutMode == 'blank' ): ?>
		<?php if(Message::count() > 0): ?>
			<div class="row">
				<?php echo Message::output(); ?>
			</div>
		<?php endif; ?>
		@yield('content')
	<?php elseif( $layoutMode == 'leftMenu' ): ?>

		<div class="container">
			<div class="row">
				<div class="sidenav col-lg-3">
				<?php echo $leftMenu; ?>
				</div>
				<div class=" col-lg-9">
					<?php if(Message::count() > 0): ?>
						<div class="row">
							<?php echo Message::output(); ?>
						</div>
					<?php endif; ?>
					@yield('content')
				</div>
			</div>
		</div>
		<?php endif; ?>
		
	<hr class="featurette-divider">
	<div class="container">
		<div class="row">
			<!-- FOOTER -->
			<footer>
				<p class="pull-right"><a href="#">Back to top</a></p>
				<p>&copy; 2011-<?php echo date("Y"); ?> borkedLabs.<br />
					All artwork, screenshots, characters, vehicles, storylines, world facts or other recognizable features of the intellectual property relating to these trademarks are likewise the intellectual property of CCP hf. "EVE", "EVE Online", "CCP", and all related logos and images are trademarks or registered trademarks of CCP hf.
				</p>
			</footer>
		</div>
	</div>
</body>
</html>
