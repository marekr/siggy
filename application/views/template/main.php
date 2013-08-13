<!DOCTYPE html> 
<html>
  <head>
    <title>siggy</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link type="text/css" href="<?php echo URL::base(TRUE, TRUE);?>public/css/siggy.compiled.css?32" rel="stylesheet" media="screen" /> 
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
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jsBezier-0.6-min.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jsPlumb-util.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jsPlumb-dom-adapter.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jsPlumb.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jsPlumb-drag.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jsPlumb-endpoint.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jsPlumb-connection.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jsPlumb-anchors.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jsPlumb-defaults.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jsPlumb-connectors-bezier.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jsPlumb-connectors-statemachine.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jsPlumb-renderers-svg.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jsPlumb-renderers-canvas.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jsPlumb-renderers-vml.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.jsPlumb.js'></script> 
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.js?2'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggymap.js?3'></script>
    <?php else: ?>
	<?php if( isset($_GET['beta']) ): ?>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/beta-thirdparty.compiled.js?<?php echo time(); ?>'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/beta-siggy.compiled.js?<?php echo time(); ?>'></script>
    <?php else: ?>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/thirdparty.compiled.js?13'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.compiled.js?74'></script>
	<?php endif; ?>
    <?php endif; ?>
  </head>
  <body>
		<?php if( $loggedIn = true || Kohana::$environment == Kohana::DEVELOPMENT): ?>
		<div id="floatingHeader">
			<div id="topBar">
				<div>
					<img src="http://image.eveonline.com/Corporation/<?php echo $corpID; ?>_64.png" height="32px" />
					<img src="http://image.eveonline.com/Character/<?php echo $charID; ?>_64.jpg" height="32px"/>
					<p class="name"><?php echo $charName; ?> <?php if( $apilogin ): ?>[<a href='<?php echo URL::base(TRUE, TRUE);?>account/logout'>Logout</a>]<?php endif;?>
					<br />
					<?php if( ( count( $group['groups']) > 1  ) || (count( current( $group['groups'] ) ) > 1 ) ):?>							
									<div class='dropDown' id='accessMenu' style='position:absolute;left:83px;top:25px;'>
											<span style="font-size:0.9em;font-style:italic;font-weight: normal;padding:3px 6px;background-color: #092665;"><?php echo $group['accessName']; ?> - <?php echo ($group['subGroupID'] != 0 ) ? $group['sgName'].' - ' : '' ?><?php echo $group['groupTicker'] ?>&nbsp;&nbsp;&nbsp;<span style='font-style:normal;font-weight:bold;'>&#x25BC;</span></span>
											<ul class='menu'>
												<?php foreach( $group['groups'] as $g => $sgs ): ?>
													<?php foreach( $sgs as $sg ): ?>
														<?php if( $sg == 0 ): ?>
																<li id='<?php echo md5($g.'-'.$sg); ?>'><?php echo $group['groupDetails']['group'][ $g ]['groupName'] ?></li>
														<?php else: ?>
																<li id='<?php echo md5($g.'-'.$sg); ?>'><?php echo $group['groupDetails']['subgroup'][ $sg ]['accessName']; ?> - <?php echo $group['groupDetails']['subgroup'][ $sg ]['subGroupName'] ?> - <?php echo $group['groupDetails']['group'][ $g ]['groupName'] ?></li>										
														<?php endif; ?>
													<?php endforeach; ?>
												<?php endforeach; ?>
												
											</ul>
											<br clear='all' />
									</div>
							<?php else: ?>
									<span style="font-size:0.9em;font-style:italic;font-weight: normal;" title="Current access"><?php echo $group['accessName']; ?> - <?php echo $group['groupTicker'] ?></span>
							<?php endif; ?>
					</p>
				</div>
				<?php if( $siggyMode ): ?>
					<div id="updateTime">
						<span id="loading" style="display:none;"><img src="<?php echo URL::base(TRUE, TRUE);?>public/images/ajax-loader.gif" />&nbsp;</span>
						Selected System: <span id="currentsystem"><b>System</b></span><br />
						<?php if( $igb ): ?>
							Your Location: <span id="acsname" title='Your current location'><b>System</b></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<div id="headerTools">
				<?php echo $headerTools; ?>
			</div>
		</div>
		<br />
		<?php endif; ?>
		<div id="wrapper">
			<div style="height:70px; width: 100%"></div>
			
			<?php echo $content; ?>
			<div id="footerLinks" style="text-align:center;font-size:0.9em;margin-top:100px;">
					<a href="http://siggy.borkedlabs.com/info/">Usage Guide</a>
					<?php if( $apilogin ): ?>
					&nbsp;&middot;&nbsp;
					<a href="<?php echo URL::base(TRUE, TRUE);?>account/apiKeys">API Keys</a>
					&nbsp;&middot;&nbsp;
					<a href="<?php echo URL::base(TRUE, TRUE);?>account/characterSelect">Switch Character</a>
					&nbsp;&middot;&nbsp;
					<a href="<?php echo URL::base(TRUE, TRUE);?>account/changePassword">Change Password</a>
					<?php endif; ?>
					<br />
					<?php if( $siggyMode ): ?>
					Last Update: <span class="updateTime" title='Last update recieved'>00:00:00</span><br />
					<?php endif; ?>
			</div>    
		</div>
		<?php if( defined('MESSMODE') ) { echo View::factory('profiler/stats'); } ?>
  </body>
</html>