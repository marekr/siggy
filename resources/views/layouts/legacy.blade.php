<!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>

    <!-- Le styles -->
    <link href="<?php echo URL::base(TRUE, TRUE);?>bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo URL::base(TRUE, TRUE);?>bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/thirdparty.compiled.js?13'></script>

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="<?php echo URL::base(TRUE, TRUE);?>favicon.ico">
</head>
<body data-spy="scroll">
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="<?php echo URL::base(TRUE, TRUE);?>">siggy</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li <?php echo ($selectedTab == 'home' ? 'class="active"' : ''); ?>><a href="<?php echo URL::base(TRUE, TRUE);?>">Home</a></li>
            </ul>
            
            <?php if( Auth::loggedIn() ): ?>
            <p class="navbar-text pull-right">
              Logged in as <a href="#" class="navbar-link"><?php echo Auth::$user->username; ?></a> <a href="<?php echo URL::base(TRUE, TRUE);?>account/logout" />Logout</a>
            </p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

	<div class="container">
		<div class="content">
			<div class="row">
				@yield('content')
			</div>
		</div>
	</div> 
</body>
</html>