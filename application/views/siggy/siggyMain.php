<!DOCTYPE html> 
<html>
  <head>
    <title>siggy</title>
       <style type='text/css'></style>
     <link type="text/css" href="<?php echo URL::base(TRUE, TRUE);?>public/css/siggy.css?24" rel="stylesheet" media="screen" /> 
    <?php if( Kohana::$environment == Kohana::DEVELOPMENT ): ?>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery-1.7.1.min.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.tablesorter.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.ezpz_tooltip.min.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.blockUI.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.autocomplete.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.color.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.flot.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/raphael-1.5.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.js?2'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggycalc.js?2'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggymap.js?2'></script>
    <?php else: ?>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/thirdparty.compiled.js?12'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.compiled.js?49'></script>
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
				<div class="centerBar">
				<!---
				  <div id="freezeOpt" style="<?php echo ($requested == true) ? "display:none":"" ?>">
				  Automatic system loading enabled: <a href="#" id="freezeLink">disable</a>
				  </div>
				  <div id="unfreezeOpt" style="<?php echo ($requested == true) ? "":"display:none" ?>">
				  Automatic system loading disabled: <a href="#" id="unfreezeLink">enable</a>
				  </div>
				  <button>Load Current System</button>
				  --->
				</div>
				<div id="updateTime">
					<span id="loading" style="display:none;"><img src="<?php echo URL::base(TRUE, TRUE);?>public/images/ajax-loader.gif" />&nbsp;</span>
					Selected System: <span id="currentsystem"><b>System</b></span><br />
					<?php if( $igb ): ?>
						Your Location: <span id="acsname" title='Your current location'><b>System</b></span>
					<?php endif; ?>
				</div>
			</div>
			<div id="headerTools">
				<div id="headerToolsButtonBar">
					<div id="globalNotesButton" class="headerToolButton">Show Notes &#x25BC;</div>
					<div id="strengthCalcButton" class="headerToolButton">Strength Calc. &#x25BC;</div>
				</div>
				<div id="globalNotes">
					<h2>Notes <a href="#" id="gNotesEdit" >[edit]</a></h2>
					<textarea style="width:100%;height:100px;display:none;" id="gNotesEditBox">
					</textarea>
					<div id="thegnotes">No notes loaded.</div>
					<div class="center">
					<button style="display:none" id="gNotesSave">Save</button><button style="display:none" id="gNotesCancel">Cancel</button><br />
					<p>Last update: <span id="gNotesTime">00:00:00</span></p>
					</div>
				</div>
				
				<!--- start calc --->
				
				  <div>
					  <div id="sigCalculator">
						<div style="position:relative">
						  <h2>Sig Strength Calculator</h2>
						  <div>
							Selected profile:
							<select name="scanProfiles" id="scanProfiles">
							</select><br />
							<button id="createProfile">Create</button>
							<button disabled="disabled" id="editProfile">Edit</button>
							<button disabled="disabled" id="deleteProfile">Delete</button>
							<br />
							<br />
							<div id="profileInfo" style="font-size:0.8em;">
							  <h3 style="font-weight:bold;font-size:1.1em;">Profile Settings</h3>
							  <span style="font-weight:bold;">Covert Ops: </span><span id="infoCovertOps"></span><br />
							  <span style="font-weight:bold;">Astrometric Rangefinding: </span><span id="infoRangeFinding"></span><br />
							  <span style="font-weight:bold;">Rigs: </span><span id="infoRigs"></span><br />
							  <span style="font-weight:bold;">Sisters Launcher: </span><span id="infoSistersLauncher"></span><br />
							  <span style="font-weight:bold;">Sisters Probes: </span><span id="infoSistersProbes"></span><br />
							  <span style="font-weight:bold;">Prospector Implants: </span><span id="infoProspector"></span><br />
							</div>                
							<fieldset id="scanProfileOptions" style="display:none">
							  <legend id="profileMode">Profile Options</legend>
							  <label>Name:</label><input type="text" id="inputProfileName" /><br clear="all" />
							  <label>Covert Ops/T3 Electronic Subsystem:</label>
							  <select id="selectCovertOps" name="covertOps">
								<option value="0">--</option>
								<option value="1">Level 1</option>
								<option value="2">Level 2</option>
								<option value="3">Level 3</option>
								<option value="4">Level 4</option>
								<option value="5">Level 5</option>
							  </select>
							  <br clear="all" />
							  <label>Astrometric Rangefinding:</label>
							  <select id="selectRangeFinding" name="rangefinding">
								<option value="0">--</option>
								<option value="1">Level 1</option>
								<option value="2">Level 2</option>
								<option value="3">Level 3</option>
								<option value="4">Level 4</option>
								<option value="5">Level 5</option>
							  </select>
							  <br clear="all" />
							  <label>Rigs:</label>
							  <select id="selectRigs" name="rigs">
								<option value="0">--</option>
								<option value="1">T1 - 1 rig</option>
								<option value="2">T1 - 2 rigs</option>
								<option value="3">T2 - 1 rig</option>
								<option value="4">T2 - 2 rigs</option>
								<option value="5">T1 & T2 mixed</option>
							  </select>
							  <br clear="all" />
							  <label>Prospector Implants:</label>
							  <select id="selectProspector" name="prospector">
								<option value="0">--</option>
								<option value="1">PPH-0</option>
								<option value="2">PPH-1</option>
								<option value="3">PPH-2</option>
							  </select>
							  <br clear="all" />
							  <label>Sisters Launchers:</label><label class="yes">Yes<input type="radio" name="sistersLauncher" value="1" /></label><label class="no">No<input type="radio" name="sistersLauncher" value="0" /></label>
							  <br clear="all" />
							  <label>Sisters Probes:</label><label class="yes">Yes<input type="radio" name="sistersProbes" value="1" /></label><label class="no">No<input type="radio" name="sistersProbes" value="0" /></label>
							   <br clear="all" />
							  <label>Default Profile?:</label><label class="yes">Yes<input type="radio" name="preferred" value="1" /></label><label class="no">No<input type="radio" name="preferred" value="0" /></label>
							  <br clear="all" />
							  <div style="text-align:center"><button id="saveScanProfile">Save & Calculate</button><button id="cancelProfile">Cancel</button></div>
							</fieldset>
							<br />
						  </div>
						  <br clear="all" />
						  <div id="strengthTable">
							<table class='siggyTable'>
							  <thead>
								<tr>
								  <th width="10%">&nbsp;</th>
								  <th width="10%">1.25</th>
								  <th width="10%">1.67</th>
								  <th width="10%">2.2</th>
								  <th width="10%">2.5</th>
								  <th width="10%">4</th>
								  <th width="10%">5</th>
								  <th width="10%">6.67</th>
								  <th width="10%"><span style="color:green;font-weight:normal;">K162</span><br />10</th>
								  <th width="10%">20</th>
								</tr>
							  </thead>
							  <tbody>
								<tr>
								  <td class="smallLeft">Core (32AU)</td>
								  <td id="core-125" class="center"></td>
								  <td id="core-167" class="center"></td>
								  <td id="core-22" class="center"></td>
								  <td id="core-25" class="center"></td>
								  <td id="core-4" class="center"></td>
								  <td id="core-5" class="center"></td>
								  <td id="core-667" class="center"></td>
								  <td id="core-10" class="center"></td>
								  <td id="core-20" class="center"></td>
								</tr>
								<tr>
								  <td class="smallLeft">Combat (64AU)</td>
								  <td id="combat-125" class="center"></td>
								  <td id="combat-167" class="center"></td>
								  <td id="combat-22" class="center"></td>
								  <td id="combat-25" class="center"></td>
								  <td id="combat-4" class="center"></td>
								  <td id="combat-5" class="center"></td>
								  <td id="combat-667" class="center"></td>
								  <td id="combat-10" class="center"></td>
								  <td id="combat-20" class="center"></td>
								</tr>
								<tr>
								  <td class="smallLeft">Deep Space (128AU)</td>
								  <td id="deep-125" class="center"></td>
								  <td id="deep-167" class="center"></td>
								  <td id="deep-22" class="center"></td>
								  <td id="deep-25" class="center"></td>
								  <td id="deep-4" class="center"></td>
								  <td id="deep-5" class="center"></td>
								  <td id="deep-667" class="center"></td>
								  <td id="deep-10" class="center"></td>
								  <td id="deep-20" class="center"></td>
								</tr>
							  </tbody>
							</table>
						  </div>
						</div>
					  </div>      
				  </div>
				  
				  <!---- end calc -->
				
				
				
				
				
				
			</div>
		</div>
		<br />
		<?php endif; ?>
    <div id="wrapper">
      <!--- chain map -->
      <div id="chainMapContainer">
				<h2 class="header" style="position:relative;"><p class='left'>Chain map</p><p id="chainMapec" style="display:inline-block;" class="center"><?php echo ( !$mapOpen ? "Click to show" : 'Click to hide' ); ?></p></h2>
				<div id='chainMapInner' <?php echo ( !$mapOpen ? "style='display:none'" : '' ); ?>>
					<p class="loading">Loading....<br /><span style='font-size:0.3em;'>(This may take up to 10 seconds.)</span></p>
					<p class="editing">Editing: Drag systems by clicking on them.</p>
					<p class="deleting">Click on(or mouse drag) wormhole links to select for deletion</p>
					<div id="chainMap">
					</div>
					<!-- magic buttons -->
					<p class="buttons">
						<button id="chainMapSave" style="display:none;">Save Map Changes</button><button id="chainMapMassDeleteConfirm" style="display:none;">Confirm Mass Delete</button><button id="chainMapMassDeleteCancel" style="display:none;">Cancel</button>
					</p>
					<!-- magic buttons -->
					<div id='chainPanTrackX'>
						<div id='chainPanBarX'>
							<p class='left'> &lt; </p>
							<p class='right'> &gt; </p>
						</div>
					</div>
					<div style='position:relative;'>
						<!-- options -->
						<div class='dropUp' id='chainMapOptions' style='position:absolute;'>
							<span>Options</span>
							<ul class='menu'>
								<li id='addWHManual'>Add Wormhole Manually</li>
								<li id='editWHMap'>Edit Map</li>
								<li id='massDeleteWHs'>Mass Delete Wormholes</li>
							</ul>
							<br clear='all' />
						</div>
						<!--- end options -->
						<!--- broadcast -->
						<p style='float:right;'>
							<?php echo ( ( isset($_COOKIE['broadcast']) && $_COOKIE['broadcast'] == 0 ) ? "<span id='broadcastText'>Location broadcasting is disabled.</span> <button id='chainMapBroadcast'>Enable</button>" : "<span id='broadcastText'>Location broadcasting is enabled.</span> <button id='chainMapBroadcast'>Disable</button>" ); ?>
						</p>
						<!-- end broadcast -->
						<br clear="all" />
					</div>
				</div>
				<!--- wh editor -->
				<div id="wormholePopup" class="box">
					<div id="wormholeTabs">
						<div id="whEdit">Edit</div>
						<div id="jumpLog">Jump Log
						<span style='color: #FF4C00;font-weight:bold;font-size:0.8em;'>BETA</span></div>
					</div>
					<div id="jumpLogViewer" class="tabcontent">
						<div style='text-align:center;margin-bottom: 10px;'><span id='refreshJumpLog' style='font-weight:bold;color: #FF4C00;'>X Refresh Log</span></div>
						<b>Recorded and Approximate Mass:</b> <span id='totalJumpedMass'>0.00</span>mil<br /><br />
						<div id="jumpLogWrap" style='height:210px;overflow: auto;'>
							<ul id="jumpLogList">
								<li><b>No jumps recorded</b></li>
							</ul>
						
						</div>
						<div class="center"><button id='jumpLogClose'>Close</button></div>
					</div>
					<div id="wormholeEditor" class="tabcontent">
						<div id="whEditorAdd">
							<h3>Create a wormhole link</h3>
							<br />
							<div style="float:left;text-align:right;">From <input type='text' name='fromSys' /><br /><label style="display:block;margin-top:3px;"><input type='checkbox' name='fromCurrent' value='1' />  Current Location</label></div>
							<div style="float:right;text-align:right;">To <input type='text' name='toSys' /><br /><label style="display:block;margin-top:3px;"><input type='checkbox' name='toCurrent' value='1' />  Current Location</label></div>
							<br clear="all" />
						</div>
						<div id="whEditorEdit">
						<h3>Editing Wormhole</h3>
						<p class='center'><span id="whEditFrom"></span> to <span id="whEditTo"></span></p>
							<button class="centered" id="wormholeEditorDisconnect">Close Wormhole</button>
						</div>
						<br />
						<ul class="errors">
						</ul>
						<br />
						<fieldset><legend>Options</legend>
							EOL?: <label class="yes" style="display:inline;float:none;">Yes<input type="radio" name="eol" value="1" /></label><label class="no" style="display:inline;float:none;">No<input type="radio" name="eol" value="0" /></label><br />
							<br />
							Mass Stage: 
							<select name='mass'>
								<option value='0'>Stage 1/Default</option>
								<option value='1'>Stage 2/Reduced</option>
								<option value='2'>Stage 3/Critical</option>
							</select><br /><br />
							<div class="center"><button id="wormholeEditorSave">Save</button><button id='wormholeEditorCancel'>Cancel</button><br />
							
							</div>
						</fieldset>
					</div>
				</div>
				<!-- end wh editor -->
      </div>
      <!--- end chain map -->
      <br />
      <div id="systemAdvanced">
        <div id="systemList">
          <ul>
          </ul>
        </div>
        <div id="systemInfoButton" class="selected">Info</div>
        <div id="systemOptionsButton">Options</div>
        <div class="clear"></div>
      </div>
      
      <div id="systemInfo-collasped"<?php echo ( isset($_COOKIE['sysInfoCollasped']) && $_COOKIE['sysInfoCollasped'] == 1 ) ? '' : "style='display:none'" ?>>
				<h2>
					<p class='systemName'>System</p>
					<p class='spacer'>|</p>
					<p id='collaspedInfoEffectStatic'></p>
					<p class='systemButtons'><a href='#' target='_blank' class='evekill'><img src='public/images/evekill.png' width='16' height='16'/></a><a href='#' target='_blank' class='dotlan'><img src='public/images/dotlan.png' width='16' height='16'/></a><img src='public/images/carebear.gif' class='carebear' width='16' height='16'/><img id='systemInfo-expand' src='public/images/expand.png'  width='16' height='16'/></p>
				</h2>
			</div>
      <div id="systemInfo"<?php echo ( isset($_COOKIE['sysInfoCollasped']) && $_COOKIE['sysInfoCollasped'] == 1 ) ? "style='display:none'" : '' ?>>
        <table id="systemTable" cellspacing="1" class='siggyTable'>
          <tr>
            <th class="header" colspan="4"><span class="systemName"><?php echo !empty($systemName) ? $systemName : 'System'; ?></span><p><a href='#' target='_blank' class='evekill'><img src='public/images/evekill.png' width='16' height='16'/></a><a href='#' target='_blank' class='dotlan'><img src='public/images/dotlan.png' width='16' height='16'/></a><img src='public/images/carebear.gif' class='carebear' width='16' height='16'/><img id='systemInfo-collaspe' src='public/images/collaspe.png'  width='16' height='16'/></p></th>
          </tr>
          <tr>
            <td class="title">Region</td>
            <td class="content" id="region"></td>
            <td class="title">Planets/Moons</td>
            <td class="content" id="planetsmoons"></td>
          </tr>
          <tr>
            <td class="title">Constellation</td>
            <td class="content" id="constellation"></td>
            <td class="title">Belts</td>
            <td class="content" id="belts"></td>
          </tr>
          <tr>
            <td class="title">True Sec</td>
            <td class="content" id="truesec"></td>
            <td class="title">System Effect</td>
            <td class="content" id="systemEffect"></td>
          </tr>
          <tr>
            <td class="title">Radius</td>
            <td class="content" id="radius"></td>
            <td class="title">Statics</td>
            <td class="content" id="staticInfo"></td>
          </tr>
        </table>
      </div>
      <div id="systemOptions">
        <table class='siggyTable'>
          <tr>
            <th class="header" colspan="2" id="systemName">System Options</th>
          </tr>
          <tr>
            <td class="title">System label/display name<p class="desc">This is completely optional and displays a custom name in the system list for easy identification of chained systems</p></td>
            <td class="content"><input type="text" name="label" /></td>
          </tr>
          <tr>
            <td class="title">Is currently connected/used hole?</td>
            <td class="content"><label class="yes">Yes<input type="radio" name="inUse" value="1" /></label><label class="no">No<input type="radio" name="inUse" value="0" /></label></td>
          </tr>
          <tr>
            <td class="title">Activity Level</td>
            <td class="content">
            <select name='activity'>
							<option value='0'>Don't Know</option>
							<option value='1'>Empty</option>
							<option value='2'>Occupied</option>
							<option value='3'>Active</option>
            </select>
            </td>
          </tr>
          <tr>
            <td class="buttonRow" colspan="2"><button class="reset">Reset</button><button class="save">Save</button></td>
          </tr>
        </table>
      </div>
			<!---start stats--->
			<div id='stats' class='fauxTable'>
				<h3 class='clickMe'>Statistics (click to toggle)</h3>
				<div <?php echo ( isset($_COOKIE['statsOpened']) && $_COOKIE['statsOpened'] == 1 ) ? '' : "style='display:none'" ?>>
					<div style='display:inline-block'>
						<h4>Jumps</h4>
						<div id='jumps' style='width:250px;height:160px;'></div>
					</div>
					<div style='display:inline-block'>
						<h4>NPC Kills</h4>
						<div id='npcKills' style='width:250px;height:160px;'></div>
					</div>
					<div style='display:inline-block'>
						<h4>Ship Kills</h4>
						<div id='shipKills' style='width:250px;height:160px;'></div>
					</div>
					<div>
						<div class='statColor' style='background-color:#AFD8F8'></div>
						<span>siggy</span>
						<div class='statColor' style='background-color:#EDC240'></div>
						<span>API</span>
					</div>
				</div>
			</div>
			<!--- end stats -->
      <br clear='all' />
      <div id="sigAddBox">
        <form>
          <input type="text" name="sig" style="width:50px;float:left;margin-right:5px;" maxlength="3" />
          <?php if( $group['showSigSizeCol'] ): ?>
          <select name="size" style="float:left;display:block;margin-right:5px;">
            <option value="" selected="selected"> -- </option>
            <option value="1.25">1.25</option>
            <option value="2.2">2.2</option>
            <option value="2.5">2.5</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6.67">6.67</option>
            <option value="10">10</option>
          </select>
					<?php endif; ?>
          <select name="type" style="float:left;display:block;margin-right:5px;">
            <option value="none" selected="selected"> -- </option>
            <option value="wh">WH</option>
            <option value="ladar">Ladar</option>
            <option value="grav">Grav</option>
            <option value="radar">Radar</option>
            <option value="mag">Mag</option>
          </select>
					<select name="site">
					</select>
          <input name ='add' type="submit" value="Add" style="float:right;width:40px;height:40px;" />
					<br />
					<input type="text" name="desc" style="width:236px;" />
        </form>
      </div>
      <div id="sigs">
        <table id="sigTable" cellspacing="1" class="siggyTable tablesorter">
          <thead> 
            <tr>
              <th width="2%">&nbsp;</th>
              <th width="5%">Sig</th>
              <?php if( $group['showSigSizeCol']) :?>
              <th width="3%">Size</th>
              <th width="5%">Type</th>
              <th width="74%">Name/Description</th>
              <?php else: ?>
              <th width="5%">Type</th>
              <th width="77%">Name/Description</th>
              <?php endif; ?>
              <th width="2%">&nbsp;</th>
              <th width="7%">Age</th>
              <th width="2%">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
           </tbody>
        </table>
      </div>
      <br />
      

			<div id="carebearBox" class="box" style="display:none;">
				<div>
						<h2 class="jqDrag">Carebearing</h2>
							<div id="bearClassLinks"><a href='#' id='bearC1'>C1</a> | <a href='#' id='bearC2'>C2</a> |  <a href='#' id='bearC3'>C3</a> |  <a href='#' id='bearC4'>C4</a> |  <a href='#' id='bearC5'>C5</a> |  <a href='#' id='bearC6'>C6</a></div><br />
							<br />
							<div id="bearInfoSets">
								<div id="bearClass1">
									<h3>Cosmic Anomaly</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=PerimeterAmbushPoint' target='_blank'>Perimeter Ambush Point</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=PerimeterCamp' target='_blank'>Perimeter Camp</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=PhaseCatalystNode' target='_blank'>Phase Catalyst Node</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=TheLine' target='_blank'>The Line</a><br />

									<h3>Magnetometric</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=ForgottenPerimeterCoronationPlatform' target='_blank'>Forgotten Perimeter Coronation Platform</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=ForgottenPerimeterPowerArray' target='_blank'>Forgotten Perimeter Power Array</a><br />

									<h3>Radar</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=UnsecuredPerimeterAmplifier' target='_blank'>Unsecured Perimeter Amplifier</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=UnsecuredPerimeterInformationCenter' target='_blank'>Unsecured Perimeter Information Center</a><br />
								</div>
								<div id="bearClass2">
									<h3>Cosmic Anomaly</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=PerimeterCheckpoint' target='_blank'>Perimeter Checkpoint</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=PerimeterHangar' target='_blank'>Perimeter Hangar</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=RuinsofCohort27' target='_blank'>The Ruins of Enclave Cohort 27</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=SleeperDataSanctuary' target='_blank'>Sleeper Data Sanctuary</a><br />

									<h3>Magnetometric</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=ForgottenPerimeterGateway' target='_blank'>Forgotten Perimeter Gateway</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=ForgottenPerimeterHabitationCoils' target='_blank'>Forgotten Perimeter Habitation Coils</a><br />

									<h3>Radar</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=UnsecuredPerimeterCommsRelay' target='_blank'>Unsecured Perimeter Comms Relay</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=UnsecuredPerimeterTransponderFarm' target='_blank'>Unsecured Perimeter Transponder Farm</a><br />
								</div>
								<div id="bearClass3">
									<h3>Cosmic Anomaly</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=FortificationFrontierStronghold' target='_blank'>Fortification Frontier Stronghold</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=OutpostFrontierStronghold' target='_blank'>Outpost Frontier Stronghold</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=SolarCell' target='_blank'>Solar Cell</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=TheOruzeConstruct' target='_blank'>The Oruze Construct</a><br />

									<h3>Magnetometric</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=ForgottenFrontierQuarantineOutpost' target='_blank'>Forgotten Frontier Quarantine Outpost</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=ForgottenFrontierRecursiveDepot' target='_blank'>Forgotten Frontier Recursive Depot</a><br />

									<h3>Radar</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=UnsecuredFrontierDatabase' target='_blank'>Unsecured Frontier Database</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=UnsecuredFrontierReceiver' target='_blank'>Unsecured Frontier Receiver</a><br />
								</div>
								<div id="bearClass4">
									<h3>Cosmic Anomaly</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=FrontierBarracks' target='_blank'>Frontier Barracks</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=FrontierCommandPost' target='_blank'>Frontier Command Post</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=IntegratedTerminus' target='_blank'>Integrated Terminus</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=SleeperInformationSanctum' target='_blank'>Sleeper Information Sanctum</a><br />
									
									<h3>Magnetometric</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=ForgottenFrontierConversionModule' target='_blank'>Forgotten Frontier Conversion Module</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=ForgottenFrontierEvacuationCenter' target='_blank'>Forgotten Frontier Evacuation Center</a><br />

									<h3>Radar</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=UnsecuredFrontierDigitalNexus' target='_blank'>Unsecured Frontier Digital Nexus</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=UnsecuredFrontierTrinaryHub' target='_blank'>Unsecured Frontier Trinary Hub</a><br />
								</div>
								<div id="bearClass5">
									<h3>Cosmic Anomaly</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=CoreGarrison' target='_blank'>Core Garrison</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=CoreStronghold' target='_blank'>Core Stronghold</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=OruzeOsobnyk' target='_blank'>Oruze Osobnyk</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=QuarantineArea' target='_blank'>Quarantine Area</a><br />

									<h3>Magnetometric</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=ForgottenCoreDataField' target='_blank'>Forgotten Core Data Field</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=ForgottenCoreInformationPen' target='_blank'>Forgotten Core Information Pen</a><br />

									<h3>Radar</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=UnsecuredFrontierEnclaveRelay' target='_blank'>Unsecured Frontier Enclave Relay</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=UnsecuredFrontierServerBank' target='_blank'>Unsecured Frontier Server Bank</a><br />
								</div>
								<div id="bearClass6">
									<h3>Cosmic Anomaly</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=CoreCitadel' target='_blank'>Core Citadel</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=CoreBastion' target='_blank'>Core Bastion</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=StrangeEnergyReadings' target='_blank'>Strange Energy Readings</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=TheMirror' target='_blank'>The Mirror</a><br />

									<h3>Magnetometric</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=ForgottenCoreAssemblyHall' target='_blank'>Forgotten Core Assembly Hall</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=ForgottenCoreCircuitryDisassembler' target='_blank'>Forgotten Core Circuitry Disassembler</a><br />

									<h3>Radar</h3>
									<a href='http://eve-survival.org/wikka.php?wakka=UnsecuredCoreBackupArray' target='_blank'>Unsecured Core Backup Array</a><br />
									<a href='http://eve-survival.org/wikka.php?wakka=UnsecuredCoreEmergence' target='_blank'>Unsecured Core Emergence</a><br />
								</div>
							</div>
							<br />
							<p style="font-size:0.7em">*All links open a new tab to eve-survival</p>
				</div>      
		  </div>  
			<div id="fatalError" class="box" style="display:none;">
				<div>
							<h2>Fatal error has ocurred</h2>
							<br />
							<p id="fatalErrorMessage"></p>
							<br />
							<p style='text-align:center'><button id="refreshFromFatal">Refresh</button></p>
				</div>      
		  </div>  		  
		  
		<div id="footerLinks" style="text-align:center;font-size:0.9em;margin-top:100px;">
				<a href="http://siggy.borkedlabs.com/info/">Usage Guide</a>
				<?php if( $apilogin ): ?>
				&nbsp;&middot;&nbsp;
				<a href="<?php echo URL::base(TRUE, TRUE);?>account/setAPI">Set API</a>
				&nbsp;&middot;&nbsp;
				<a href="<?php echo URL::base(TRUE, TRUE);?>account/characterSelect">Switch Character</a>
				&nbsp;&middot;&nbsp;
				<a href="<?php echo URL::base(TRUE, TRUE);?>account/changePassword">Change Password</a>
				<?php endif; ?>
				<br />
				Last Update: <span class="updateTime" title='Last update recieved'>00:00:00</span><br />
		</div>    
      <script type='text/javascript'>
        $(document).ready(function() { 
              
              $('#loading').ajaxStart( function() {
                $(this).show();
              });
              
              $('#loading').ajaxStop( function() {
                $(this).hide();
              } );
              
              var options = {
									showSigSizeCol: <?php echo ( $group['showSigSizeCol'] ? 'true' : 'false' ); ?>
              };
              
              siggy = new siggymain( options );
              siggy.baseUrl = '<?php echo URL::base(TRUE, TRUE);?>';
              <?php if( $initialSystem ): ?>
              siggy.setSystemID(<?php echo $systemData['id']; ?>);
              siggy.systemName = '<?php echo $systemData['name']; ?>';
                <?php if( $requested): ?>
              siggy.freeze();
                <?php endif; ?>
              <?php endif; ?>
              
              
              siggy.initialize();
              
              
              
         } );         
      </script>
   </div>
   <?php if( defined('MESSMODE') ) { echo View::factory('profiler/stats'); } ?>
  </body>
</html>