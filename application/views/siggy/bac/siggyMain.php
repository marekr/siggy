<!DOCTYPE html> 
<html>
  <head>
    <title>siggy</title>
       <style type='text/css'></style>
     <link type="text/css" href="<?php echo URL::base(TRUE, TRUE);?>public/css/siggy.css?2" rel="stylesheet" media="screen" /> 
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery-1.4.4.min.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.tablesorter.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.ezpz_tooltip.min.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.blockUI.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.js?2'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggycalc.js?2'></script>
  </head>
  <body>
    <div id="wrapper">
      <?php if( $trusted==true || Kohana::$environment == Kohana::DEVELOPMENT): ?>
      <div id="topBar">
        <div>
          <img src="http://image.eveonline.com/Corporation/<?php echo $_SERVER['HTTP_EVE_CORPID']; ?>_64.png" height="32px" />
          <img src="http://image.eveonline.com/Character/<?php echo $_SERVER['HTTP_EVE_CHARID']; ?>_64.jpg" height="32px"/>
          <p class="name"><?php echo $_SERVER['HTTP_EVE_CHARNAME']; ?> <?php if(isset($group['groupTicker']) ) { print('<br /><span style="font-size:0.8em;font-style:italic;font-weight: normal;">Accessing as '.$group['accessName'].' as part of the '.$group['groupTicker'].' group </span>'); } ?></p>
        </div>
        <div class="centerBar">
          <div id="freezeOpt" style="<?php echo ($requested == true) ? "display:none":"" ?>">
          Automatic system loading enabled: <a href="#" id="freezeLink">disable</a>
          </div
          <div id="unfreezeOpt" style="<?php echo ($requested == true) ? "":"display:none" ?>">
          Automatic system loading disabled: <a href="#" id="unfreezeLink">enable</a>
          </div
        </div>
        <div id="updateTime"><span id="loading" style="display:none;"><img src="<?php echo URL::base(TRUE, TRUE);?>public/images/ajax-loader.gif" />&nbsp;</span>
          Last Update: <span class="time">00:00:00</span>
        </div>
      </div>
      <div id="globalNotesContainer">
        <div id="globalNotes">
          <h2>Notes <a href="#" id="gNotesEdit" >[edit]</a></h2>
          <textarea style="width:100%;height:100px;display:none;" id="gNotesEditBox">
          </textarea>
          <div id="thegnotes">No notes loaded.</div>
          <div class="center">
            <button style="display:none" id="gNotesSave">Save</button><br />
            <p>Last update: <span id="gNotesTime">00:00:00</span></p>
          </div>
        </div>
        <div id="globalNotesButton">Notes &darr;</div>
      </div>
      <br />
      <?php endif; ?>
      <div id="systemAdvanced">
        <div id="systemList">
          <ul>
          </ul>
        </div>
        <div id="systemInfoButton" class="selected">Info</div>
        <div id="systemOptionsButton">Options</div>
        <div class="clear"></div>
      </div>
      <div id="systemInfo">
        <table id="systemTable" cellspacing="1">
          <tr>
            <th class="header" colspan="4"><span id="systemName"><?php echo !empty($systemName) ? $systemName : 'System'; ?></span><p><a href='#' target='_blank' id='dotlan'><img src='public/images/dotlan.png' width='16' height='16'/></a><img src='public/images/carebear.gif' id='carebear' width='16' height='16'/></p></th>
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
            <td class="title">Probable Statics</td>
            <td class="content" id="staticInfo"></td>
          </tr>
        </table>
      </div>
      <div id="systemOptions">
        <table>
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
            <td class="buttonRow" colspan="2"><button class="reset">Reset</button><button class="save">Save</button></td>
          </tr>
        </table>
      </div>
      <div>
          <div id="sigCalculator">
            <div style="position:relative">
              <h2 class="jqDrag">Sig Strength Calculator</h2>
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
                <table>
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
        <div id="strengthCalcButton">Strength Calc. &darr;</div>
      </div>
      <br />
      <div id="sigAddBox">
        <form>
          <input type="text" name="sig" style="width:50px;float:left;margin-right:5px;" maxlength="3" />
          <select name="type" style="float:left;display:block;margin-right:5px;">
            <option value="none" selected="selected"> -- </option>
            <option value="wh">WH</option>
            <option value="ladar">Ladar</option>
            <option value="grav">Grav</option>
            <option value="radar">Radar</option>
            <option value="mag">Mag</option>
          </select>
          <div style="float:left;overflow:hidden;margin-right:5px;">
            <select name="site">
            </select><br />
            <input type="text" name="desc" style="width:240px" />
          </div>
          <input type="submit" value="Add" style="float:left;" />
        </form>
      </div>
      <div id="sigs">
        <table id="sigTable" cellspacing="1" class="tablesorter">
          <thead> 
            <tr>
              <th width="2%">&nbsp;</th>
              <th width="5%">Sig</th>
              <th width="5%">Type</th>
              <th width="77%">Name/Description</th>
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
      

			<div id="carebearBox" class="box" style="display:none;width:300px">
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
      <script type='text/javascript'>
      
      function setCookie(c_name,value,exdays)
      {
        var exdate=new Date();
        exdate.setDate(exdate.getDate() + exdays);
        var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
        document.cookie=c_name + "=" + c_value;
      }

              
        $(document).ready(function() { 
              
              $('#loading').ajaxStart( function() {
                $(this).show();
              });
              
              $('#loading').ajaxStop( function() {
                $(this).hide();
              } );
              
              siggy = new siggymain();
              siggy.baseUrl = '<?php echo URL::base(TRUE, TRUE);?>';
              <?php if( $initialSystem ): ?>
              siggy.setSystemID(<?php echo $systemData['id']; ?>);
              siggy.systemName = '<?php echo $systemData['name']; ?>';
                <?php if( $requested): ?>
              siggy.freeze();
                <?php endif; ?>
              <?php endif; ?>
              
              siggy.initialize();
              
              
              sigCalc = new siggyCalc();
              sigCalc.baseUrl = '<?php echo URL::base(TRUE, TRUE);?>';
              sigCalc.initialize();
         } );         
      </script>
  </body>
</html>