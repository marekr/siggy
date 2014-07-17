<!DOCTYPE html>
<html lang="en">
  <head>
    <title>siggy</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php if( Kohana::$environment == Kohana::DEVELOPMENT ): ?>
	<link type="text/css" href="<?php echo URL::base(TRUE, TRUE);?>theme.php?id=<?php echo $settings['theme_id']; ?>" rel="stylesheet" media="screen" /> 
    <?php else: ?>
	<link type="text/css" href="<?php echo URL::base(TRUE, TRUE);?>theme.php?id=<?php echo $settings['theme_id']; ?>" rel="stylesheet" media="screen" /> 
    <?php endif; ?>
    <link href="<?php echo URL::base(TRUE, TRUE);?>public/font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet">
    
	<?php if( isset($_GET['nyan']) ): ?>
	<link type="text/css" href="<?php echo URL::base(TRUE, TRUE);?>public/css/rengas.css?28" rel="stylesheet" media="screen" /> 
	<?php endif; ?>
    <?php if( Kohana::$environment == Kohana::DEVELOPMENT ): ?>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery-1.10.1.min.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery-ui-1.10.3.min.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery-migrate-core.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.tablesorter.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.blockUI.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.autocomplete.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.color.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.flot.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.contextMenu.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.qtip.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.jsPlumb-1.5.5-min.js'></script> 
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/dropdown.js'></script> 
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.helpers.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.static.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggymap.js?<?php echo time(); ?>'></script>
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
		<?php if( $loggedIn = true || Kohana::$environment == Kohana::DEVELOPMENT): ?>
		<div id="floating-header">
			<div id="top-bar">
				<div>
					<img src="https://image.eveonline.com/Corporation/<?php echo $corpID; ?>_64.png" height="32px" />
					<img src="https://image.eveonline.com/Character/<?php echo $charID; ?>_64.jpg" height="32px"/>
					<p class="name"><?php echo $charName; ?> <?php if( $apilogin ): ?>[<a href='<?php echo URL::base(TRUE, TRUE);?>account/logout'>Logout</a>]<?php endif;?>
					<br />
					<?php if( count( $group['access_groups']) > 1  ):?>							
						<div class="dropdown" style="display:inline-block;">
								<a data-toggle="dropdown" href="#">
									<?php echo $group['groupName']; ?>&nbsp;&nbsp;&nbsp;<span style='font-style:normal;font-weight:bold;'>&#x25BC;</span>
								</a>
								<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
									<?php foreach( $group['access_groups'] as $g ): ?>
										<li>
											<a href="<?php echo URL::base(); ?>access/switch_membership/?k=<?php echo md5($g['group_id']); ?>">
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
		<div id="wrapper">
			<div style="height:70px; width: 100%"></div>

			<?php echo $content; ?>
			<div id="footer-link" style="text-align:center;font-size:0.9em;margin-top:100px;">
				<?php if( $apilogin ): ?>
				&nbsp;&middot;&nbsp;
				<a href="<?php echo URL::base(TRUE, TRUE);?>account/apiKeys">API Keys</a>
				&nbsp;&middot;&nbsp;
				<a href="<?php echo URL::base(TRUE, TRUE);?>account/characterSelect">Switch Character</a>
				&nbsp;&middot;&nbsp;
				<a href="<?php echo URL::base(TRUE, TRUE);?>account/changePassword">Change Password</a>
				<?php endif; ?>
				<br />
				<div>
					<p style="width:33%;float:left;text-align:left;">
					&nbsp;
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
		</div>
	</body>
</html>