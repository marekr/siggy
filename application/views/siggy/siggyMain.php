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
    <div class="bordered-wrap">
        <div id="system-advanced">
            <h2>
                <span id="system-name"><?php echo !empty($systemName) ? $systemName : 'System'; ?></span>
                <a href='#' target='_blank' class='site-icon site-dotlan click-me'><img src='public/images/dotlan.png' width='16' height='16'/></a>
                <a href='#' target='_blank' class='site-icon site-wormholes click-me'><img src='public/images/wormholes.png' width='16' height='16'/></a>
            </h2>
            <ul class='option-bar tabs'>
                <li class="active"><a href='#system-info'>Info</a></li>
                <li><a href='#sigs'>Recon</a></li>
                <li><a href='#system-options'>Options</a></li>
            </ul>
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
            
            <!---start stats--->
            <div id='system-stats' class="sub-display-group">
                <h2>Statistic</h2>
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
            <!--- end stats -->
            
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
                        <button id="system-options-reset" class="btn btn-default">Reset</button>
                        <button id="system-options-save" class="btn btn-primary">Save</button>
                    </td>
                </tr>
            </table>
        </div>
    
    
        <div id="sigs">
            <table id="system-table" cellspacing="1" class='siggy-table'>
                <tr>
                    <td class="title">Region</td>
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
            </table>
            <div id="sig-add-box" style="padding:10px;">
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
                    <button name='add' class="btn btn-default" style="margin-top: 15px;line-height: 171%;"><i class="icon-plus-sign"></i>  Add</button>
                </form>
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
    
    <div id="mass-add-sig-box" class="box">
        <div>
            <h2>Mass Sig Reader</h2>
            <p>This is for copy pasted signatures from your scanner window. Simply select a signature, hit CTRL+A, then CTRL-C, then paste into the box below. This tool can add AND update signatures.</p>
            <form>
                <textarea name="blob" rows="12" style="width:100%;font-size:11px;"></textarea>
                <div style="text-align:center;">
                    <button name='add' class="btn btn-primary" type="submit">Submit</button>
                    <button name='cancel' class="btn btn-default">Cancel</button>
                </div>
            </form>
        </div>
    </div>
  

    <div id="fatal-error" class="box" style="display:none;">
        <div>
                <h2>Fatal error has ocurred</h2>
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
                <?php if( $initialSystem ): ?>
                initialSystemID: <?php echo $systemData['id']; ?>,
                initialSystemName: '<?php echo $systemData['name']; ?>',
                <?php endif; ?>
                showSigSizeCol: <?php echo ( $group['showSigSizeCol'] ? 'true' : 'false' ); ?>,
                map: {
                    jumpTrackerEnabled: <?php echo ( $group['jumpLogEnabled'] ? 'true' : 'false' ); ?>,
                    jumpTrackerShowNames:  <?php echo ( $group['jumpLogRecordNames'] ? 'true' : 'false' ); ?>,
                    jumpTrackerShowTime:  <?php echo ( $group['jumpLogRecordTime'] ? 'true' : 'false' ); ?>,
                    showActivesShips:  <?php echo ( $group['chain_map_show_actives_ships'] ? 'true' : 'false' ); ?>
                }
            };

            siggy = new siggymain( options );
                <?php if( $initialSystem ): ?>
            siggy.setSystemID(<?php echo $systemData['id']; ?>);
            siggy.systemName = '<?php echo $systemData['name']; ?>';
                <?php endif; ?>
            <?php if( $requested): ?>
            siggy.freeze();
            <?php endif; ?>


            siggy.initialize();



        } );         
    </script>
