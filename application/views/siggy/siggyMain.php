	<?php if( $group['iskBalance'] < 0 ): ?>
	<div class="box" style="background-color:rgb(179, 52, 52)">
		<h2>Balance warning</h2>
		<p>
			The balance for this siggy group has gone negative. Payment must be made according to the information in the management panel or service may be discontinued at any time. Contact <b>Jack Tronic</b> if assitance is needed.
		</p>
	</div>
	<br />
	<br />
	<?php endif; ?>
	
	<?php echo $chainMap; ?>
		
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
				<td class="title">Constellation</td>
				<td class="content" id="constellation"></td>
			</tr>
			<tr>
				<td class="title">Planets/Moons/Belts</td>
				<td class="content" id="planetsmoons"></td>
				<td class="title">Radius</td>
				<td class="content" id="radius"></td>
			</tr>
			<tr>
				<td class="title">True Sec</td>
				<td class="content" id="truesec"></td>
				<td class="title">System Effect</td>
				<td class="content" id="systemEffect"></td>
			</tr>
			<tr>
				<td class="title">Hub Jumps</td>
				<td class="content" id="hubJumps"></td>
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
								<option value='4'>Friendly</option>
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
		<a href="#" id="massAddSigs">[+] Mass Sig Reader</a>
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
		
		<!-- mass add box start -->
		
		<div id="massAddSigBox" class="box" style="display:none;width:300px;">
			<div>
				<h2>Mass Sig Reader</h2>
				<p>This is for copy pasted signatures from your scanner window. Simply select a signature, hit CTRL+A, then CTRL-C, then paste into the box below. This tool can add AND update signatures.</p>
				<form>
					<textarea name="blob" rows="12" style="width:100%;font-size:11px;"></textarea>
					<div style="text-align:center;">
						<button name='add' type="submit">Submit</button>
            <button name='cancel'>Cancel</button>
					</div>
				</form>
			</div>
		</div>
      
		<!-- carebear box -->
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
		<!-- carebear box -->
			<div id="fatalError" class="box" style="display:none;">
				<div>
							<h2>Fatal error has ocurred</h2>
							<br />
							<p id="fatalErrorMessage"></p>
							<br />
							<p style='text-align:center'><button id="refreshFromFatal">Refresh</button></p>
				</div>      
			</div>  	
			<script type='text/javascript'>
			$(document).ready(function() { 
				  
				  var options = {
                    baseUrl: '<?php echo URL::base(TRUE, TRUE);?>',
                    <?php if( $initialSystem ): ?>
                    initialSystemID: <?php echo $systemData['id']; ?>,
                    initialSystemName: '<?php echo $systemData['name']; ?>',
                    <?php endif; ?>
                    sessionID: '<?php echo $sessionID; ?>',
										showSigSizeCol: <?php echo ( $group['showSigSizeCol'] ? 'true' : 'false' ); ?>,
										map: {
                      jumpTrackerEnabled: <?php echo ( $group['jumpLogEnabled'] ? 'true' : 'false' ); ?>,
                      jumpTrackerShowNames:  <?php echo ( $group['jumpLogRecordNames'] ? 'true' : 'false' ); ?>,
                      jumpTrackerShowTime:  <?php echo ( $group['jumpLogRecordTime'] ? 'true' : 'false' ); ?>
										}
				  };
				  
				  siggy = new siggymain( options );
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