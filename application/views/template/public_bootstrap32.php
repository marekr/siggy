<!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>

    <!-- Le styles -->
    <link href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap3/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo URL::base(TRUE, TRUE);?>public/css/public.css" rel="stylesheet">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap3/js/bootstrap.min.js"></script>

	
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	
    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="<?php echo URL::base(TRUE, TRUE);?>favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/ico/apple-touch-icon-57-precomposed.png">
	
	<script type="text/javascript">
	$(document).ready(function() {
		$('.dropdown-toggle').dropdown()
	});
	</script>
</head>
<body>	
	<div class="navbar navbar-default navbar-inverse navbar-fixed-top">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo URL::base(TRUE, TRUE);?>">siggy</a>
		</div>
		<div class="navbar-collapse collapse navbar-responsive-collapse">
			<ul class="nav navbar-nav">
				<li <?php echo ($selectedTab == 'home' ? 'class="active"' : ''); ?>><a href="<?php echo URL::base(TRUE, TRUE);?>">Home</a></li>

				<li><a href="http://wiki.siggy.borkedlabs.com">Guide</a></li>
				<li><a href="">About</a></li>
				<li><a href="">Announcements</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<?php if( $loggedIn ): ?>
				
					<?php if( count(Auth::$user->perms) > 0 ): ?>
				<li <?php echo ($selectedTab == 'admin' ? 'class="active"' : ''); ?>><a href="<?php echo URL::base(TRUE, TRUE);?>manage">Admin</a></li>
					<?php elseif( !$igb): ?>
				<li><a href="<?php echo URL::base(TRUE, TRUE);?>pages/createGroup">Create Siggy Group</a></li> 
					<?php endif; ?>

				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Logged in as <?php echo $user['username']; ?><b class="caret"></b></a>
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
			</ul>
		</div>
	</div>
	
	<div class="height:40px;"></div>

	<?php if( $layoutMode == 'blank' ): ?>
	<div class="container">
		<div class="content">
			<div class="row">
				<?php echo $content; ?>
			</div>
		</div>
	</div> 
	<?php elseif( $layoutMode == 'leftMenu' ): ?>

	<div class="container">

		<div class="row">
			<div class="sidenav span3">
			<?php echo $leftMenu; ?>
			</div>
			<div class="span9">
			<?php echo $content; ?>
			</div>
		</div>	
		<?php endif; ?>
	</div>
</body>
</html>