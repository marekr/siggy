@extends('layouts.siggy')

@section('content')
<div id="activity-siggy" class="wrapper" style="display:none">
	<?php
	if(isset($_SERVER['HTTP_X_SSL_PROTOCOL']) && trim($_SERVER['HTTP_X_SSL_PROTOCOL']) == 'SSLv3' ): ?>
	<div class="box" style="width:100%;background-color:rgb(179, 52, 52)">
		<div class='box-header'>Security warning!</div>
		<div class='box-content'>
			You connected to siggy using SSLv3! This protocol is now insecure and being removed from browsers in the future. TLS is now the defacto standard for HTTPS connections. <br />
			If you are getting this message in the EVE IGB or Internet Explorer, go to this guide on how to remove this message: <a href="http://wiki.siggy.borkedlabs.com/support:connection_reset">http://wiki.siggy.borkedlabs.com/support:connection_reset</a><br />
			If you were getting a -101 connection reset error in the IGB, <b>THIS APPLIES TO YOU.</b> SSLv3 was re-enabled on the server temporarily
			because users have broken computer configurations. (TLS should have been enabled on Windows as long ago as Windows XP). <br />
			This message will go away once you connect to siggy using TLS.
		</div>
	</div>
	<?php endif; ?>

	<div class="box" style="width:100%;display:none;" id="no-chain-map-warning">
		<div class='box-header'>No chain-maps configured</div>
		<div class='box-content'>
			Your group administrators have not configured chain-maps for your corporation or character. Please contact them to fix this.
		</div>
	</div>

	@include('siggy.chainmap')

	<br />
    <div id="main-body" class="bordered-wrap">
        <div id="system-advanced">
            <div>
                <span id="system-name"><?php echo !empty($systemName) ? $systemName : 'System'; ?></span>
                <a href='#' target='_blank' class='site-icon site-dotlan click-me'><img src='<?php echo URL::base(TRUE, TRUE);?>images/dotlan.png' width='16' height='16'/></a>
                <a href='#' target='_blank' class='site-icon site-wormholes click-me'><img src='<?php echo URL::base(TRUE, TRUE);?>images/wormholes.png' width='16' height='16'/></a>
                <a href='#' target='_blank' class='site-icon site-evekill click-me'><img src='<?php echo URL::base(TRUE, TRUE);?>images/evekill.png' width='16' height='16'/></a>
                <a href='#' target='_blank' class='site-icon site-zkillboard click-me'><img src='<?php echo URL::base(TRUE, TRUE);?>images/wreck.png' width='16' height='16'/></a>
            </div>
            <ul class='option-bar tabs' role='tablist'>
                <li role='presentation'><a href='#system-info' aria-controls='home' role='tab' data-toggle='tab'>Extra</a></li>
                <li role='presentation' class='active'><a href='#sigs' aria-controls='home' role='tab' data-toggle='tab'>Scan</a></li>
                <li role='presentation'><a href='#system-intel' aria-controls='home' role='tab' data-toggle='tab'>Intel</a></li>
                <li role='presentation'><a href='#system-options' aria-controls='home' role='tab' data-toggle='tab'><?php echo ___('Options'); ?></a></li>
            </ul>
        </div>
		<div class='tab-content'>
        	<div role="tabpanel" id="system-intel" class="tab-pane clear-fix">
				@include('siggy/displaygroups/poses')
				@include('siggy/displaygroups/dscan')
        	</div>

        	<div role="tabpanel" id="system-info" class="tab-pane clear-fix">
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
	                    <td class="title"><?php echo ___('Constellation'); ?></td>
	                    <td class="content" id="constellation"></td>
	                </tr>
	            </table>
	            <!-- carebear box -->
	            <div id="carebear-box" class="sub-display-group">
	                <div class='sub-display-group-header'>Carebearing</div>
	                <div class='sub-display-group-content'>
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
	        <div role="tabpanel"id="system-options" class="tab-pane">
	            <table class='siggy-table'>
	                <tr>
	                    <td class="title">System label/display name<p class="desc">This is completely optional and displays a custom name in the system list for easy identification of chained systems</p></td>
	                    <td class="content"><input type="text" name="label"  class="siggy-input" /></td>
	                </tr>
	                <tr>
	                    <td class="title">Activity Level</td>
	                    <td class="content">
	                    <select name='activity' class="siggy-input">
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
	        <div role="tabpanel" id="sigs" class="tab-pane active">
	            <table id="system-table" cellspacing="1" class='siggy-table'>
	                <tr>
	                    <td class="title">Region / <?php echo ___('Constellation'); ?></td>
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
	                <div class='sub-display-group-header hover'>Statistics<i class="expand-collapse-indicator fa fa-caret-down pull-right fa-lg"></i></div>
	                <div class='sub-display-group-content'>
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
	                <div class='sub-display-group-header'>
						<span>Signature Adder</span>
						<textarea name='mass_sigs' placeholder=" Paste scan results here + Press Enter " type='text' class="siggy-input"></textarea>
						<i class="expand-collapse-indicator fa fa-caret-down pull-right fa-lg"></i>
					</div>
	                <div class='sub-display-group-content'>
	                    <a href="#" id="mass-add-sigs" class="btn btn-xs btn-default">Mass Sig Reader</a>
	                    <div class="clear"></div>
	                    <form>
	                        <div style="float:left">
	                            <div class="input-group" style="width:50px">
	                                <label>Sig ID</label>
	                                <input type="text" class="siggy-input" name="sig" maxlength="3" placeholder="ABC" />
	                            </div>
	                            <?php if( $group->show_sig_size_col ): ?>
	                            <div class="input-group"  style="width:100px;">
	                                <label>Sig Size</label>
	                                <select name="size" class="siggy-input">
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
	                                <select name="type" class="siggy-input">
	                                    <option value="none" selected="selected"> -- </option>
	                                    <option value="wh"><?php echo ___('WH');?></option>
	                                    <option value="gas"><?php echo ___('Gas');?></option>
	                                    <option value="ore"><?php echo ___('Ore');?></option>
	                                    <option value="data"><?php echo ___('Data');?></option>
	                                    <option value="relic"><?php echo ___('Relic');?></option>
	                                    <option value="anomaly"><?php echo ___('Combat');?></option>
	                                </select>
	                            </div>
	                            <div class="input-group" style="width: auto;">
	                                <label>Site</label>
	                                <select name="site" class="siggy-input">
	                                </select>
	                            </div>
	                            <br />
	                            <div class="input-group" style="width:236px;">
	                                <label>Description</label>
	                                <input type="text" name="desc" class="siggy-input" />
	                            </div>
	                        </div>
	                        <button name='add' class="btn btn-default" style="margin-top: 15px;line-height: 171%;"><i class="icon icon-plus-sign"></i>  Add</button>
	                    </form>
	                </div>
	            </div>
				<div class="sub-display-group">
					<div class='sub-display-group-header'>
						<span id='number-sigs'>0</span> /
							<span id='total-sigs'>0</span> <?php echo ___('signature(s) shown'); ?>
						<div id="sig-filter" class="button-group pull-right">
							<button type="button" class="btn btn-xs dropdown-toggle btn-info" data-toggle="dropdown"><span class="glyphicon glyphicon-filter"></span> Filter <i class="fa fa-caret-down"></i></button>
							<ul class="dropdown-menu">
								<li><a href="#" class="small" data-value="wh" tabIndex="-1"><input type="checkbox"/>&nbsp;<?php echo ___('Wormhole');?></a></li>
								<li><a href="#" class="small" data-value="ore" tabIndex="-1"><input type="checkbox"/>&nbsp;<?php echo ___('Ore');?></a></li>
								<li><a href="#" class="small" data-value="gas" tabIndex="-1"><input type="checkbox"/>&nbsp;<?php echo ___('Gas');?></a></li>
								<li><a href="#" class="small" data-value="data" tabIndex="-1"><input type="checkbox"/>&nbsp;<?php echo ___('Data');?></a></li>
								<li><a href="#" class="small" data-value="relic" tabIndex="-1"><input type="checkbox"/>&nbsp;<?php echo ___('Relic');?></a></li>
								<li><a href="#" class="small" data-value="anomaly" tabIndex="-1"><input type="checkbox"/>&nbsp;<?php echo ___('Combat');?></a></li>
								<li><a href="#" class="small" data-value="none" tabIndex="-1"><input type="checkbox"/>&nbsp;<?php echo ___('None');?></a></li>
							</ul>
						</div>
						<div class='clear'></div>
					</div>
				</div>
	            <table id="sig-table" cellspacing="1" class="siggy-table siggy-table-striped">
	                <thead>
	                    <tr>
	                        <th width="2%">&nbsp;</th>
	                        <th width="5%">Sig</th>
	                        <?php if( $group->show_sig_size_col) :?>
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
    </div>
    <br />

	@include('siggy/boxes/confirm')
	@include('siggy/boxes/hotkey_helper')
	@include('siggy/boxes/group_notes')
	@include('siggy/boxes/character_settings')
	@include('siggy/boxes/dialog_import_thera')
	@include('siggy/boxes/dialog_exit_finder')
	@include('siggy/boxes/dialog_pos')
	@include('siggy/boxes/dialog_dscan')
	@include('siggy/boxes/dialog_notice')
	@include('siggy/boxes/dialog_notifier')
	@include('siggy/boxes/dialog_mass_sig')
	@include('siggy/handlebars/sig_table_row')
	@include('siggy/handlebars/effect_tooltip')
	@include('siggy/handlebars/statics_tooltip')
	@include('siggy/handlebars/site_tooltip')
	@include('siggy/handlebars/thera_table_row')
	@include('siggy/handlebars/search_result_pos')
	@include('siggy/handlebars/search_result_legacy_pos')
	@include('siggy/handlebars/scanned_system_table_row')
	@include('siggy/handlebars/notification_history_table_row')
	@include('siggy/handlebars/notification_setting_table_row')
	@include('siggy/handlebars/notifier_form_mapped_system')
	@include('siggy/handlebars/notifier_form_resident_found')
	@include('siggy/handlebars/notifier_form_site_found')
	@include('siggy/handlebars/chainmap_table_row')
	@include('siggy/handlebars/sig_create_new_wormhole_form')
	@include('siggy/handlebars/jump_log_entry')
	@include('siggy/handlebars/dialog_base')

	<script type='text/javascript'>
		$(document).ready(function() {

			var options = {
				baseUrl: '<?php echo URL::base(TRUE, TRUE);?>',
				initialSystemID: <?php echo ($systemData != null ? $systemData->id : 0); ?>,
				igb: false,
				<?php if($requested): ?>
				freezeSystem: true,
				<?php endif; ?>
				negativeBalance: {{ $group->isk_balance < 0 ? 'true' : 'false' }},

				defaultActivity:  '<?php echo $group->default_activity; ?>',
				sessionID: '<?php echo Auth::$session->id; ?>',
				charsettings: {
					themeID: <?php echo $settings->theme_id; ?>,
					combineScanIntel: <?php echo $settings->combine_scan_intel; ?>,
					zoom: 1,
					language: '<?php echo $settings->language; ?>',
					defaultActivity: '<?php echo $settings->default_activity; ?>'
				},
				sigtable: {
					showSigSizeCol: <?php echo ( $group->show_sig_size_col ? 'true' : 'false' ); ?>
				},
				map: {
					jumpTrackerEnabled: <?php echo ( $group->jump_log_enabled ? 'true' : 'false' ); ?>,
					jumpTrackerShowNames:  <?php echo ( $group->jump_log_record_names ? 'true' : 'false' ); ?>,
					jumpTrackerShowTime:  <?php echo ( $group->jump_log_record_time ? 'true' : 'false' ); ?>,
					showActivesShips:  <?php echo ( $group->chain_map_show_actives_ships ? 'true' : 'false' ); ?>,
					allowMapHeightExpand: <?php echo $group->allow_map_height_expand ? 'true' : 'false'; ?>,
					alwaysShowClass: <?php echo $group->chainmap_always_show_class ? 'true' : 'false'; ?>,
					maxCharactersShownInSystem: <?php echo (int)($group->chainmap_max_characters_shown); ?>
				}
			};

			var siggy = new siggy2.Core( options );


			siggy.initialize();
		} );
	</script>
</div>
@endsection