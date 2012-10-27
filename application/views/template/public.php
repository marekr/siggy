<!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>

    <!-- Le styles -->
    <link href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/css/main.css" rel="stylesheet">
    <link href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/js/google-code-prettify/prettify.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/ico/apple-touch-icon-57-precomposed.png">


</head>
<body data-spy="scroll">
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="<?php echo URL::base(TRUE, TRUE);?>">siggy</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li <?php echo ($selectedTab == 'home' ? 'class="active"' : ''); ?>><a href="<?php echo URL::base(TRUE, TRUE);?>">Home</a></li>
              
              <li><a href="http://siggy.borkedlabs.com/info">Guide</a></li>
            <?php if( $loggedIn ): ?>
              <li <?php echo ($selectedTab == 'account' ? 'class="active"' : ''); ?>><a href="<?php echo URL::base(TRUE, TRUE);?>account/overview">Account</a></li>
              <?php if( $user->gadmin ): ?>
				<li <?php echo ($selectedTab == 'admin' ? 'class="active"' : ''); ?>><a href="<?php echo URL::base(TRUE, TRUE);?>manage">Admin</a></li>
			  <?php else: ?>
			  
				<!-- <li><a href="<?php echo URL::base(TRUE, TRUE);?>pages/createGroup">Create Siggy Group</a></li> -->
              <?php endif; ?>
            <?php else: ?>
              <li  <?php echo ($selectedTab == 'register' ? 'class="active"' : ''); ?>><a href="<?php echo URL::base(TRUE, TRUE);?>account/register">Register</a></li>
              <li  <?php echo ($selectedTab == 'login' ? 'class="active"' : ''); ?>><a href="<?php echo URL::base(TRUE, TRUE);?>account/login">Login</a></li>
            <?php endif; ?>
            </ul>
            
            <?php if( $loggedIn ): ?>
            <p class="navbar-text pull-right">
              Logged in as <a href="#" class="navbar-link"><?php echo $charName; ?></a> <a href="<?php echo URL::base(TRUE, TRUE);?>account/logout" />Logout</a>
            </p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

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

</body>
</html>