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
        <link href="<?php echo URL::base(TRUE, TRUE);?>font-awesome-4.2.0/css/font-awesome.min.css" rel="stylesheet">
    	<link rel="icon" href="<?php echo URL::base(TRUE, TRUE);?>favicon.ico">

        <?php if( Kohana::$environment == Kohana::DEVELOPMENT ): ?>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/handlebars-v2.0.0.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/handlebars.form-helpers.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/handlebars.helpers.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/translate.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/jquery/jquery-1.11.2.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/jquery/jquery-ui.1.11.4.min.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/jquery/jquery.tablesorter.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/jquery/jquery.blockUI.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/jquery/jquery.color.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/jquery/jquery.flot.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/jquery/jquery.ui.position.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/jquery/jquery.contextMenu.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/jquery/jquery.qtip.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/jquery/jquery.jsPlumb-1.6.4.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/jquery/jquery.placeholder.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/jquery/jquery.hotkeys.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/jquery/jquery.simplePagination.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/dropdown.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/tab.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/misc.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/typeahead.bundle.js'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.helpers.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.static.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.timer.js?<?php echo time(); ?>'></script>
		<script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.eve.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.sigtable.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.intel.poses.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.intel.dscan.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.globalnotes.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.charactersettings.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.hotkeyhelper.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.dialog.sigcreatewormhole.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.map.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.map.connection.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.notifications.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.activity.thera.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.activity.search.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.activity.homestead.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.activity.siggy.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.activity.scannedsystems.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.activity.notifications.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.activity.astrolabe.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.activity.chainmap.js?<?php echo time(); ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/vendor/moment.js?<?php echo time(); ?>'></script>
        <?php else: ?>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/thirdparty.compiled.js?<?php echo SIGGY_VERSION; ?>'></script>
        <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>js/siggy.compiled.js?<?php echo SIGGY_VERSION; ?>'></script>
        <?php endif; ?>
		<meta name="csrf-token" content="<?php echo Auth::$session->sessionData['csrf_token']; ?>">
	</head>
	<body>
		<div class="navbar navbar-default navbar-fixed-top">
			<ul class="nav navbar-nav">
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
						<li><a class="activity-menu-option" data-activity="homestead"><span class="glyphicon glyphicon-globe"></span> Homestead</a></li>
                        <li><a class="activity-menu-option" data-activity="notifications"><span class="glyphicon glyphicon-bell"></span> Notifications</a></li>
                        <li><a class="activity-menu-option" data-activity="search"><span class="glyphicon glyphicon-search"></span> Search</a></li>
                        <li role="separator" class="divider"></li>

						<li><a id="global-notes-button"><span class="glyphicon glyphicon-folder-close"></span> Notes</a></li>
						<li><a target="_blank" href="<?php echo URL::base(TRUE, TRUE); ?>stats"><span class="glyphicon glyphicon-list"></span> Stats</a></li>
						<li id="settings-button"><a><span class="glyphicon glyphicon-cog"></span> Settings</a></li>
						<?php if( count(Auth::$user->perms) > 0 ): ?>
						<li><a href="<?php echo URL::base(TRUE, TRUE);?>manage"><span class="glyphicon glyphicon-home"></span> Admin</a></li>
						<?php endif; ?>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Support <i class="fa fa-caret-down"></i>
					</a>
					<ul class="dropdown-menu" role="menu">
						<li><a target="_blank" href="http://wiki.siggy.borkedlabs.com">Guide</a></li>
						<li><a target="_blank" href="<?php echo URL::base(TRUE, TRUE);?>announcements">Changelog</a></li>
						<li><a target="_blank" href="http://wiki.siggy.borkedlabs.com/support/contact/">Contact</a></li>
					</ul>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<?php if( count($group['access_groups']) > 1 ): ?>
				<li class="dropdown">
					<a data-toggle="dropdown" href="#">
						<?php echo substr($group['groupName'],0,10); ?>&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down"></i>
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
				<?php else: ?>
				<li>
					<a href="#">
						<?php echo substr($group['groupName'],0,10); ?>
					</a>
				</li>
				<?php endif; ?>


				<li id="notification-header-dropdown" class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" id="notifications-button">
                        <i class="fa fa-bell-o fa-lg"></i>
                        <span id="notification-count"></span>
					</a>
					<ul id="notifications-menu" class="dropdown-menu" role="menu">
						<li class='notification-dropdown-view-link'><a>View all notifications</a></li>
					</ul>
				</li>

				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
						 <img class="navbar-eve-image" src="https://image.eveonline.com/Corporation/<?php echo Auth::$session->corpID; ?>_64.png" height="32px"/>
						 <img class="navbar-eve-image" src="https://image.eveonline.com/Character/<?php echo Auth::$session->charID; ?>_64.jpg" height="32px"/>
						<?php echo Auth::$session->charName; ?>
						<?php if( Auth::loggedIn() ): ?>
						<i class="fa fa-caret-down"></i>
						<?php endif; ?>
					</a>
					<?php if( Auth::loggedIn() ): ?>
					<ul class="dropdown-menu" role="menu">
						<li><a href="<?php echo URL::base(TRUE, TRUE);?>account/characters">Switch Character</a></li>
						<li class="divider"></li>
						<li><a href="<?php echo URL::base(TRUE, TRUE);?>account/connected">Connected Characters</a></li>
						<li><a href="<?php echo URL::base(TRUE, TRUE);?>account/changePassword">Change Password</a></li>
						<li class="divider"></li>
						<li><a href="<?php echo URL::base(TRUE, TRUE);?>account/logout">Logout</a></li>
					</ul>
					<?php endif; ?>
				</li>
				<li><a></a></li>
			</ul>
		</div>
    	<?php echo View::factory('siggy/activities/search'); ?>
    	<?php echo View::factory('siggy/activities/thera'); ?>
    	<?php echo View::factory('siggy/activities/scanned_systems'); ?>
    	<?php echo View::factory('siggy/activities/notifications'); ?>
    	<?php echo View::factory('siggy/activities/astrolabe'); ?>
    	<?php echo View::factory('siggy/activities/chainmap'); ?>
		<div id="activity-siggy" class="wrapper" style="display:none">
			<?php echo $content; ?>
		</div>
		<?php if(isset($alt_content)){ echo $alt_content; } ?>

        <div id="footer-link" class="wrapper" style="font-size:0.9em;">
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
	</body>
</html>
