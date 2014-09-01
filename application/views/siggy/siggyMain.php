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
	<div class="box" style="width:100%;display:none;" id="no-chain-map-warning">
		<h3>No chain-maps configured</h3>
		<div>
			Your group administrators have not configured chain-maps for your corporation or character. Please contact them to fix this.
		</div>
	</div>

	<?php echo $chainMap; ?>

	<br />
    <div id="main-body" class="bordered-wrap">
        <div id="system-advanced">
            <h2>
                <span id="system-name"><?php echo !empty($systemName) ? $systemName : 'System'; ?></span>
                <a href='#' target='_blank' class='site-icon site-dotlan click-me'><img src='public/images/dotlan.png' width='16' height='16'/></a>
                <a href='#' target='_blank' class='site-icon site-wormholes click-me'><img src='public/images/wormholes.png' width='16' height='16'/></a>
                <a href='#' target='_blank' class='site-icon site-evekill click-me'><img src='public/images/evekill.png' width='16' height='16'/></a>
            </h2>
            <ul class='option-bar tabs'>
                <li class="active"><a href='#system-info'>Extra</a></li>
                <li><a href='#sigs'>Scan</a></li>
                <li><a href='#system-intel'>Intel</a></li>
                <li><a href='#system-options'>Options</a></li>
            </ul>
        </div>
        <div id="system-intel" class="tab clear-fix">
            <div id="pos-box" class="sub-display-group">
                <h2>POSes</h2>
                <div>
                    <a href="#" id='system-intel-add-pos' class="btn btn-default btn-xs">Add POS</a><br /><br />
                    <table id="system-intel-poses" cellspacing="1" class='siggy-table bordered-wrap'>
						<thead>
							<tr>
								<th width="4%">Status</th>
								<th width="4%">Location</th>
								<th width="36%">Corp</th>
								<th width="4%">Type</th>
								<th width="4%">Size</th>
								<th width="8%">Added</th>
								<th width="32%">Notes</th>
								<th width="8%">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
                    </table>
                </div>
            </div>

            <div id="dscan-box" class="sub-display-group">
                <h2>DScan</h2>
                <div>
                    <a href="#" id="system-intel-add-dscan" class="btn btn-default btn-xs">Add DScan result</a><br /><br />
                    <table id="system-intel-dscans" cellspacing="1" class='siggy-table bordered-wrap'>
						<thead>
							<tr>
								<th width="60%">Title</th>
								<th>Date Added</th>
								<th>Added by</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
                    </table>
                </div>
            </div>
			<!--
            <div id="system-notes-box" class="sub-display-group">
                <h2>Notes</h2>
                <div>
                    <a href="#" class="btm btn-default btn-xs">Edit</a><br />
                    THIS SYSTEM SUCKS, WTB PORN
                </div>
            </div>
			-->
        </div>

        <div id="system-info" class="tab clear-fix">
            <table id="system-table" cellspacing="1" class='siggy-table'>
                <tr>
                    <td class="title">Planets/Moons/Belts</td>
                    <td class="content" id="planetsmoons"></td>
                    <td class="title">Radius</td>
                    <td class="content" id="radius"></td>
                </tr>
                <tr>
                    <td class="title">True Sec</td>
                    <td class="content" id="truesec"></td>
                    <td class="title">Constellation</td>
                    <td class="content" id="constellation"></td>
                </tr>
            </table>
            <!-- carebear box -->
            <div id="carebear-box" class="sub-display-group">
                <h2>Carebearing</h2>
                <div>
                    <div id="bear-class-links">
                        <a href='#' id='bear-C1'>C1</a> |
                        <a href='#' id='bear-C2'>C2</a> |
                        <a href='#' id='bear-C3'>C3</a> |
                        <a href='#' id='bear-C4'>C4</a> |
                        <a href='#' id='bear-C5'>C5</a> |
                        <a href='#' id='bear-C6'>C6</a>
                    </div>
                    <br />
                    <div id="bear-info-sets">
                        <div id="bear-class-1">
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
                        <div id="bear-class-2">
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
                        <div id="bear-class-3">
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
                        <div id="bear-class-4">
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
                        <div id="bear-class-5">
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
                        <div id="bear-class-6">
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
                    <p>* All links open a new tab to eve-survival</p>
                </div>
            </div>
            <!-- carebear box -->
        </div>
        <div id="system-options" class="tab">
            <table class='siggy-table'>
                <tr>
                    <td class="title">System label/display name<p class="desc">This is completely optional and displays a custom name in the system list for easy identification of chained systems</p></td>
                    <td class="content"><input type="text" name="label" /></td>
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
                    <td class="button-row" colspan="2">
                        <button id="system-options-reset" type="reset" class="btn btn-default">Reset</button>
                        <button id="system-options-save" class="btn btn-primary">Save</button>
                    </td>
                </tr>
            </table>
        </div>
        <div id="sigs">
            <table id="system-table" cellspacing="1" class='siggy-table'>
                <tr>
                    <td class="title">Region / Constellation</td>
                    <td class="content" id="region"></td>
                    <td class="title">System Effect</td>
                    <td class="content" id="system-effect"></td>
                </tr>
                <tr>
                    <td class="title">Hub Jumps</td>
                    <td class="content" id="hub-jumps"></td>
                    <td class="title">Statics</td>
                    <td class="content" id="static-info"></td>
                </tr>
                <tr>
                    <td class="title">POS Summary (Intel)</td>
                    <td class="content" id="pos-summary" colspan="3"></td>
                </tr>
            </table>
            <!-- start stats -->
            <div id='system-stats' class="sub-display-group">
                <h2 class="hover">Statistics</h2>
                <div>
                    <div class="system-stats-graph">
                        <h4>Jumps</h4>
                        <div id='jumps'></div>
                    </div>
                    <div class="system-stats-graph" >
                        <h4>NPC Kills</h4>
                        <div id='npcKills'></div>
                    </div>
                    <div class="system-stats-graph">
                        <h4>Ship Kills</h4>
                        <div id='shipKills'></div>
                    </div>
                    <div>
                        <div class='system-stats-legend-box system-stats-legend-siggy'></div>
                        <span>siggy</span>

                        <div class='system-stats-legend-box system-stats-legend-api'></div>
                        <span>API</span>
                    </div>
                </div>
            </div>
            <!-- end stats -->
            <div id="sig-add-box" class="sub-display-group">
                <h2 class="hover">
					<p style="display:inline-block">Signature Adder</p>
					<textarea name='mass_sigs' style="display:inline-block;width: 400px;margin-left:10px;height:18px;overflow:hidden;" placeholder=" Paste scan results here + Press Enter " type='text'></textarea>
				</h2>
                <div>
                    <a href="#" id="mass-add-sigs" class="btn btn-xs btn-default">Mass Sig Reader</a>
                    <div class="clear"></div>
                    <form>
                        <div style="float:left">
                            <div class="input-group" style="width:50px" >
                                <label>Sig ID</label>
                                <input type="text" name="sig" maxlength="3" />
                            </div>
                            <?php if( $group['showSigSizeCol'] ): ?>
                            <div class="input-group"  style="width:100px;">
                                <label>Sig Size</label>
                                <select name="size">
                                    <option value="" selected="selected"> -- </option>
                                    <option value="1.25">1.25</option>
                                    <option value="2.2">2.2</option>
                                    <option value="2.5">2.5</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6.67">6.67</option>
                                    <option value="10">10</option>
                                </select>
                            </div>
                            <?php endif; ?>
                            <div class="input-group"  style="width:100px;">
                                <label>Type</label>
                                <select name="type">
                                    <option value="none" selected="selected"> -- </option>
                                    <option value="wh">WH</option>
                                    <option value="ladar">Gas</option>
                                    <option value="grav">Ore</option>
                                    <option value="radar">Data</option>
                                    <option value="mag">Relic</option>
                                    <option value="combat">Combat</option>
                                </select>
                            </div>
                            <div class="input-group" style="width: auto;">
                                <label>Site</label>
                                <select name="site">
                                </select>
                            </div>
                            <br />
                            <div class="input-group" style="width:236px;">
                                <label>Description</label>
                                <input type="text" name="desc"  />
                            </div>
                        </div>
                        <button name='add' class="btn btn-default" style="margin-top: 15px;line-height: 171%;"><i class="icon icon-plus-sign"></i>  Add</button>
                    </form>
                </div>
            </div>
			<div class="sub-display-group">
				<h2>
					<label><input id="checkbox-show-anomalies" type="checkbox" value="1" checked /> Show Anomalies? (Combat Sites)</label>
				</h2>
			</div>
            <table id="sig-table" cellspacing="1" class="siggy-table tablesorter">
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
    </div>
    <br />
    <!-- mass add box start -->
    <div id="pos-form" class="box" style="display:none;">
        <h3>Add POS</h3>
        <div>
            <form>
				<div>
					Location(Planet - Moon)<br />
					<input type="text" value="" name="pos_location_planet" size="2" maxlength="2" style="width:auto"/> -
					<input type="text" value="" name="pos_location_moon" size="3" maxlength="4" style="width:auto"/>
				</div>
				<label>
					Owner
					<input type="text" value="" name="pos_owner" />
				</label>
				<label>
					Type
					<select name="pos_type">
						<option value="1">Amarr</option>
						<option value="2">Caldari</option>
						<option value="3">Gallente</option>
						<option value="4">Minmatar</option>
						<option value="5">Dread Guristas</option>
						<option value="6">Shadow Serpentis</option>
						<option value="7">Guristas</option>
						<option value="8">Serpentis</option>
						<option value="9">Angel</option>
						<option value="10">Blood</option>
						<option value="11">Dark Blood</option>
						<option value="12">Domination</option>
						<option value="13">Sansha</option>
						<option value="14">True Sansha</option>
					</select>
				</label>
				<label>
					Size
					<select name="pos_size">
						<option value="small">Small</option>
						<option value="medium">Medium</option>
						<option value="large">Large</option>
					</select>
				</label>
				<label>
					Status
					<select name="pos_status">
						<option value="1">Online</option>
						<option value="0">Offline</option>
					</select>
				</label>
				<label>
					Notes
					<textarea name="pos_notes" rows="6" style="width:100%;font-size:11px;"></textarea>
				</label>
                <div class="center-text form-actions">
                    <button name='submit' class="btn btn-primary" type="submit">Submit</button>
                    <button name='cancel' type="button" class="btn btn-default">Cancel</button>
                </div>
            </form>
        </div>
    </div>


    <div id="mass-add-sig-box" class="box">
        <h3>Mass Sig Reader</h3>
        <div>
            <p>This is for copy pasted signatures from your scanner window. Simply select a signature, hit CTRL+A, then CTRL-C, then paste into the box below. This tool can add AND update signatures.</p>
            <form>
                <textarea name="blob" rows="12" style="width:100%;font-size:11px;"></textarea>
                <div class="center-text form-actions">
                    <button name='add' class="btn btn-primary" type="submit">Submit</button>
                    <button name='cancel' type="button" class="btn btn-default">Cancel</button>
                </div>
            </form>
        </div>
    </div>


    <div id="dscan-form" class="box" style="display:none;">
        <h3>DScan Results</h3>
        <div>
            <p>This is for copy pasted dscan results from your scanner window. Simply select a entry, hit CTRL+A, then CTRL-C, then paste into the box below. </p>
            <form>
				<label>
					Title
					<input type="text" value="" name="dscan_title" />
				</label><br /><br />
				<label>
					Scan
					<textarea name="blob" rows="12" style="width:100%;font-size:11px;"></textarea>
				</label>
                <div class="center-text form-actions">
                    <button name='submit' class="btn btn-primary" type="submit">Submit</button>
                    <button name='cancel' type="button" class="btn btn-default">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div id="exit-finder" class="box" style="display:none;">
        <h3>Exit Finder</h3>
        <div>
            <p>Find's the nearest exit to the given system or your location.</p>
            <form>
				<label>
					System
					<input type="text" value="" name="target_system" style="width:150px" />
					<button name='submit' class="btn btn-default btn-xs" type="submit" style="margin-top: -4px;">Search</button> <br />
				</label>
				<?php if( $igb ): ?>
				<button name='current_location' class="btn btn-default btn-xs">Exits near my location</button>
				<?php endif; ?>
				<div id="exit-finder-loading" class="box-load-progress" style="display:none;">
					<img src="<?php echo URL::base(TRUE, TRUE);?>public/images/ajax-loader.gif" />
					<span>Calculating....</span>
				</div>
				<div id="exit-finder-results-wrap" style='max-height:210px;overflow: auto;margin-top: 10px;'>
					<h3>Results</h3>
					<ul id="exit-finder-list" class="box-simple-list">
						<li><b>No exits found</b></li>
					</ul>
				</div>
                <div class="center-text form-actions">
                    <button name='cancel' type="button" class="btn btn-default">Close</button>
                </div>
            </form>
        </div>
    </div>


    <div id="confirm-dialog" class="box" style="display:none;">
        <h3>Confirm action!</h3>
        <div>
            <p id="confirm-dialog-message"></p>
			<p class="center-text">
				<button id="confirm-dialog-yes">Yes</button>
				<button id="confirm-dialog-no">No</button>
			</p>
        </div>
    </div>

    <div id="fatal-error" class="box" style="display:none;">
        <h3>Fatal error has ocurred</h3>
        <div>
            <br />
            <p id="fatal-error-message"></p>
            <br />
            <p style='text-align:center'>
                <button id="fatal-error-refresh" class="btn btn-default">Refresh</button>
            </p>
        </div>
    </div>

    <!--- system context menu -->
    <ul id="system-simple-context" class="contextMenu">
        <li class="setdest">
            <a href="#setdest">Set Destination</a>
        </li>
        <li class="showinfo">
            <a href="#showinfo">Show Info</a>
        </li>
    </ul>
    <script type='text/javascript'>
        $(document).ready(function() {

            var options = {
                baseUrl: '<?php echo URL::base(TRUE, TRUE);?>',
                initialSystemID: <?php echo $systemData['id']; ?>,
                initialSystemName: '<?php echo $systemData['name']; ?>',
				sigtable: {
                	showSigSizeCol: <?php echo ( $group['showSigSizeCol'] ? 'true' : 'false' ); ?>
				},
                map: {
                    jumpTrackerEnabled: <?php echo ( $group['jumpLogEnabled'] ? 'true' : 'false' ); ?>,
                    jumpTrackerShowNames:  <?php echo ( $group['jumpLogRecordNames'] ? 'true' : 'false' ); ?>,
                    jumpTrackerShowTime:  <?php echo ( $group['jumpLogRecordTime'] ? 'true' : 'false' ); ?>,
                    showActivesShips:  <?php echo ( $group['chain_map_show_actives_ships'] ? 'true' : 'false' ); ?>
                }
            };

            siggy = new siggymain( options );
            <?php if($requested): ?>
            siggy.freeze();
            <?php endif; ?>


            siggy.initialize();



        } );
    </script>
