<!DOCTYPE html>
<html lang="en">
  <head>
    <title>siggy</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php if( Kohana::$environment == Kohana::DEVELOPMENT ): ?>
	<link type="text/css" href="<?php echo URL::base(TRUE, TRUE);?>theme.php?id=<?php echo $settings['theme_id']; ?>&<?php echo time(); ?>" id="theme-css" rel="stylesheet" media="screen" />
    <?php else: ?>
	<link type="text/css" href="<?php echo URL::base(TRUE, TRUE);?>theme.php?id=<?php echo $settings['theme_id']; ?>" id="theme-css" rel="stylesheet" media="screen" />
    <?php endif; ?>
    <link href="<?php echo URL::base(TRUE, TRUE);?>public/font-awesome-4.2.0/css/font-awesome.min.css" rel="stylesheet">

    <?php if( Kohana::$environment == Kohana::DEVELOPMENT ): ?>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/handlebars-v2.0.0.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/translate.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery-1.11.1.min.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery-ui.1.11.2.min.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.tablesorter.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.blockUI.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.autocomplete.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.color.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.flot.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.ui.position.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.contextMenu.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.qtip.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.jsPlumb-1.5.5-min.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.placeholder.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.hotkeys.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/lazyload.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/dropdown.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.static.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.helpers.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.timer.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.sigtable.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.intel.poses.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.intel.dscan.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.globalnotes.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.charactersettings.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.hotkeyhelper.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.map.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.map.connection.js?<?php echo time(); ?>'></script>
    <?php else: ?>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/thirdparty.compiled.js?<?php echo SIGGY_VERSION; ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.compiled.js?<?php echo SIGGY_VERSION; ?>'></script>
    <?php endif; ?>
	</head>
	<body>
		<div class="navbar navbar-default navbar-fixed-top">
			<div class="navbar-collapse collapse navbar-responsive-collapse">
				<ul class="nav navbar-nav">
					<li><a id="menu-toggle" href="#"><span id="main_icon" class="glyphicon glyphicon-align-justify"></span> siggy <i class="fa fa-caret-down"></i></a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Support <i class="fa fa-caret-down"></i>
						</a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="#">Guide</a></li>
							<li><a href="#">Changelog</a></li>
							<li><a href="#">Contact</a></li>
						</ul>
					</li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a data-toggle="dropdown" href="#">
							<?php echo $group['groupName']; ?>&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down"></i>
						</a>
						<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
							<?php foreach( $group['access_groups'] as $g ): ?>
								<li>
									<a href="<?php echo URL::base(TRUE, TRUE); ?>access/switch_membership/?k=<?php echo md5($g['group_id']); ?>">
									<?php echo $g['group_name']; ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
							 <img class="navbar-eve-image" src="https://image.eveonline.com/Corporation/<?php echo Auth::$session->corpID; ?>_64.png" height="32px"/>
							 <img class="navbar-eve-image" src="https://image.eveonline.com/Character/<?php echo Auth::$session->charID; ?>_64.jpg" height="32px"/>
							<?php echo Auth::$session->charName; ?> <i class="fa fa-caret-down"></i>
						</a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="<?php echo URL::base(TRUE, TRUE);?>account/apiKeys">API Keys</a></li>
							<li><a href="<?php echo URL::base(TRUE, TRUE);?>account/characterSelect">Switch Character</a></li>
							<li><a href="<?php echo URL::base(TRUE, TRUE);?>account/changePassword">Change Password</a></li>
							<li class="divider"></li>
							<li><a href="#">Logout</a></li>
						</ul>
					</li>
					<li><a></a></li>
					

				</ul>
			</div>
		</div>
		
		<script type='text/javascript'>
		$("#menu-toggle").click(function(e) {
			e.preventDefault();
			$('#sidebar-wrapper').toggleClass("active");
		});
		</script>
		<div id="siggy-content" class="wrapper">
		
			<div id="sidebar-wrapper">
				<ul class="sidebar-nav" id="sidebar">
					<li><a target="_blank" href="<?php echo URL::base(); ?>stats"><span class="sub_icon glyphicon glyphicon-list"></span> Stats</a></li>
					<li id="settings-button"><a><span class="sub_icon glyphicon glyphicon-cog"></span> Settings</a></li>
					<!---
					<li><a><span class="sub_icon glyphicon glyphicon-search"></span> Exit Finder</a></li>
					<li><a><span class="sub_icon glyphicon glyphicon-flag"></span> Astrolabe</a></li>
					<li><a><span class="sub_icon glyphicon glyphicon-eye-open"></span> Alexandria</a></li>
					<li><a><span class="sub_icon glyphicon glyphicon-tower"></span> eet</a></li>
					--->
				</ul>
			</div>
			<?php echo $content; ?>
			<div id="footer-link" style="text-align:center;font-size:0.9em;margin-top:100px;">
				<p style="width:33%;float:left;text-align:left;">
				&copy; 2011-<?php echo date("Y"); ?> borkedLabs
				</p>
				<p style="width:33%;float:left;text-align:center;">
					Last Update: <span class="updateTime" title='Last update received'>00:00:00</span>
				</p>
				<p style="width:33%;float:left;text-align:right;">
				<?php if( defined("SIGGY_VERSION") ): ?>
					siggy version: <?php echo SIGGY_VERSION; ?>
				<?php endif; ?>
				</p>
			</div>
		</div>
	</body>
</html>
