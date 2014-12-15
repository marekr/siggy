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
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.static.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.helpers.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.timer.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.sigtable.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.intel.poses.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.intel.dscan.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.globalnotes.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.charactersettings.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.hotkeyhelper.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggymap.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.map.connection.js?<?php echo time(); ?>'></script>
    <?php else: ?>
	<?php if( isset($_GET['beta']) ): ?>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/beta-thirdparty.compiled.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/beta-siggy.compiled.js?<?php echo time(); ?>'></script>
    <?php else: ?>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/thirdparty.compiled.js?<?php echo SIGGY_VERSION; ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.compiled.js?<?php echo SIGGY_VERSION; ?>'></script>
	<?php endif; ?>
    <?php endif; ?>
	</head>
	<body>
		<?php if( 1 ): ?>
		<div id="floating-header">
			<div id="top-bar">
				<div>
					<img src="https://image.eveonline.com/Corporation/<?php echo Auth::$session->corpID; ?>_64.png" height="32px" />
					<img src="https://image.eveonline.com/Character/<?php echo Auth::$session->charID; ?>_64.jpg" height="32px"/>
					<p class="name"><?php echo Auth::$session->charName; ?> <?php if( $apilogin ): ?>[<a href='<?php echo URL::base(TRUE, TRUE);?>account/logout'>Logout</a>]<?php endif;?>
					<br />
					<?php if( count( $group['access_groups']) > 1  ):?>
						<div class="dropdown" style="display:inline-block;">
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
								<br clear='all' />
						</div>
						<?php else: ?>
						<span style="font-size:0.9em;font-style:italic;font-weight: normal;" title="Current access"><?php echo $group['groupName']; ?></span>
						<?php endif; ?>
					</p>
				</div>
				<div id="update-time">
					<span id="loading" style="display:none;"><img src="<?php echo URL::base(TRUE, TRUE);?>public/images/ajax-loader.gif" />&nbsp;</span>
					Selected System: <span id="currentsystem"><b>System</b></span><br />
					<?php if( $igb ): ?>
						Your Location: <span id="acsname" title='Your current location'><b>System</b></span>
					<?php endif; ?>
				</div>
			</div>
			<div id="header-tools">
				<?php echo $headerTools; ?>
			</div>
		</div>
		<br />
		<?php endif; ?>
		<div id="siggy-content" class="wrapper">
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
