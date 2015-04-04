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
	<link rel="icon" href="<?php echo URL::base(TRUE, TRUE);?>favicon.ico">

    <?php if( Kohana::$environment == Kohana::DEVELOPMENT ): ?>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/handlebars-v2.0.0.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/translate.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery-1.11.2.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery-ui.1.11.2.min.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.tablesorter.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.blockUI.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.autocomplete.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.color.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.flot.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.ui.position.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.contextMenu.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.qtip.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.jsPlumb-1.5.5.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.placeholder.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery/jquery.hotkeys.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/dropdown.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/misc.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.helpers.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.static.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.timer.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.sigtable.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.intel.poses.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.intel.dscan.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.globalnotes.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.charactersettings.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.hotkeyhelper.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.map.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.map.connection.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.activity.thera.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.activity.search.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.activity.siggy.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.activity.scannedsystems.js?<?php echo time(); ?>'></script>
    <?php else: ?>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/thirdparty.compiled.js?<?php echo SIGGY_VERSION; ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.compiled.js?<?php echo SIGGY_VERSION; ?>'></script>
    <?php endif; ?>
	</head>
	<body>
		<div class="navbar navbar-default navbar-fixed-top">
				<ul class="nav navbar-nav">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle siggy-navbar-brand" data-toggle="dropdown" role="button" aria-expanded="false">
						<span id="current-activity">siggy</span> <span id="main_icon" class="glyphicon glyphicon-align-justify"></span> <i class="fa fa-caret-down"></i>
						</a>
						<ul class="dropdown-menu siggy-main-navbar" role="menu">
							<li><a class="activity-menu-option" data-activity="siggy" style="display:none"><span class="glyphicon glyphicon-list"></span> siggy</a></li>
							<li><a class="activity-menu-option" data-activity="scannedsystems" style="display:none"><span class="glyphicon glyphicon-list"></span> Scanned Systems</a></li>
							<li><a class="activity-menu-option" data-activity="thera"><span class="glyphicon glyphicon-magnet"></span> Thera</a></li>
                            <li><a class="activity-menu-option" data-activity="search"><span class="glyphicon glyphicon-search"></span> Search</a></li>

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
							<li><a target="_blank" href="http://wiki.siggy.borkedlabs.com/support">Contact</a></li>
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
					<li class="dropdown">
						<a href="#">
							<?php echo substr($group['groupName'],0,10); ?>
						</a>
					</li>
					<?php endif; ?>
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
							<?php if( Auth::$user->isLocal() ): ?>
							<li><a href="<?php echo URL::base(TRUE, TRUE);?>account/apiKeys">API Keys</a></li>
							<li><a href="<?php echo URL::base(TRUE, TRUE);?>account/characterSelect">Switch Character</a></li>
							<li><a href="<?php echo URL::base(TRUE, TRUE);?>account/changePassword">Change Password</a></li>
							<li class="divider"></li>
							<?php endif; ?>
							<li><a href="<?php echo URL::base(TRUE, TRUE);?>account/logout">Logout</a></li>
						</ul>
						<?php endif; ?>
					</li>
					<li><a></a></li>


				</ul>
		</div>
        <div id="activity-search" class="wrapper" style="display:none">
            <p><b>BETA</b> You may search for POSes owned by corporation name for now. More items coming later.</p>
            <div class="input-group" style="width:100%">
                <input id="activity-search-input" type="text" class="form-control" placeholder="Search for...">
                <span class="input-group-btn">
                    <button id="activity-search-go" class="btn btn-primary" type="button">Go!</button>
                </span>
            </div>
            <div class="clearfix"></div>
            <h2>Results</h2>
            <div id="activity-search-results">
            </div>
        </div>
		<div id="activity-thera" class="wrapper" style="display:none">
			<p>
                Thera wormhole information with data from <a href="http://eve-scout.com">EVE-Scout</a>
                <button class="btn btn-primary pull-right" id="activity-thera-import"><i class="fa fa-arrow-circle-down"></i> Import to chain map</button>
            </p>
            <br />
            <br />
			<table class="siggy-table siggy-table-striped table-with-dropdowns" id="thera-exits-table">
				<thead>
					<tr>
						<th>Region</th>
						<th>System</th>
                        <th>Sec</th>
						<th>Type</th>
						<th>Out Sig</th>
						<th>In Sig</th>
						<th>Life</th>
                        <th>Jumps</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div id="activity-scanned-systems" class="wrapper" style="display:none">
			<p>
                This page lists all systems that have signatures stored and the latest time they were entered as the Last Scan column.</a>
            </p>
            <br />
            <br />
			<table class="siggy-table siggy-table-striped table-with-dropdowns" id="scanned-systems-table">
				<thead>
					<tr>
						<th>Region</th>
						<th>Constellation</th>
						<th>System</th>
						<th>Last Scan</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div id="activity-siggy" class="wrapper">
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
