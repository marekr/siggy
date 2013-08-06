      <!--- chain map -->
      <div id="chainMapContainer">
				<h2 class="header" style="position:relative;">
					<p class='left'>Chain map</p>
					<p id="chainMapec" style="display:inline-block;" class="center"><?php echo ( !$mapOpen ? "Click to show" : 'Click to hide' ); ?></p>
				</h2>
				<div id='chainMapInner' <?php echo ( !$mapOpen ? "style='display:none'" : '' ); ?>>
					<p class="loading">Loading....<br /><span style='font-size:0.3em;'>(This may take up to 10 seconds.)</span></p>
					<p class="editing">Editing: Drag systems by clicking on them.</p>
					<p class="deleting">Click on(or mouse drag) wormhole links to select for deletion</p>
                    <div id="chainMapScrolltainer">
                        <div id="chainMap">
                        </div>
                    </div>
					<!-- magic buttons -->
					<div class="buttons">
						<button id="chainMapSave" style="display:none;">Save Map Changes</button>
						<button id="chainMapMassDeleteConfirm" style="display:none;">Confirm Mass Delete</button>
						<button id="chainMapMassDeleteCancel" style="display:none;">Cancel</button>
					</div>
					<!-- magic buttons -->
					<div id='map-footer-bar'>
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
						<div class="clear"></div>
						<!--- end options -->
						<!--- broadcast -->
						<p style='float:right;'>
							<?php if( !$group['alwaysBroadcast'] ): ?>
							<?php echo ( ( isset($_COOKIE['broadcast']) && $_COOKIE['broadcast'] == 0 ) ? "<span id='broadcastText'>Location broadcasting is disabled.</span> <button id='chainMapBroadcast'>Enable</button>" : "<span id='broadcastText'>Location broadcasting is enabled.</span> <button id='chainMapBroadcast'>Disable</button>" ); ?>
							<?php endif; ?>
						</p>
						<!-- end broadcast -->
						<br clear="all" />
					</div>
				</div>
				<!--- wh editor -->
				<div id="wormholePopup" class="box">
					<div id="wormholeTabs">
						<div id="whEdit">Edit</div>
						<?php if( $group['jumpLogEnabled'] ): ?>
						<div id="jumpLog">Jump Log</div>
						<?php endif; ?>
					</div>
					<?php if( $group['jumpLogEnabled'] ): ?>
					<div id="jumpLogViewer" class="tabcontent">
						<div style='text-align:center;margin-bottom: 10px;'><span id='refreshJumpLog' style='font-weight:bold;color: #FF4C00;'>X Refresh Log</span></div>
						<b>Recorded and Approximate Mass:</b> <span id='totalJumpedMass'>0.00</span>mil<br /><br />
						<div id="jumpLogWrap" style='height:210px;overflow: auto;'>
							<ul id="jumpLogList" class="itemList">
								<li><b>No jumps recorded</b></li>
							</ul>
						
						</div>
						<div class="center"><button id='jumpLogClose'>Close</button></div>
					</div>
					<?php endif; ?>
					<div id="wormholeEditor" class="tabcontent">
						<div id="whEditorAdd">
							<h3>Create a wormhole link</h3>
							<br />
							<div style="float:left;text-align:right;">
								From <input type='text' name='fromSys' /><br />
								<label style="display:block;margin-top:3px;">
									<input type='checkbox' name='fromCurrent' value='1' />  Current Location
								</label>
								</div>
							<div style="float:right;text-align:right;">
								To <input type='text' name='toSys' /><br />
								<label style="display:block;margin-top:3px;">
									<input type='checkbox' name='toCurrent' value='1' />  Current Location
								</label>
							</div>
							<br clear="all" />
						</div>
						<div id="whEditorEdit">
							<h3>Editing Wormhole</h3>
							<p class='center'>
								<span id="whEditFrom"></span> to <span id="whEditTo"></span>
							</p>
							<button class="centered" id="wormholeEditorDisconnect">Close Wormhole</button>
						</div>
						<ul class="errors">
						</ul>
						<br />
						<fieldset><legend>Options</legend>
							EOL?: <label class="yes" style="display:inline;float:none;">Yes<input type="radio" name="eol" value="1" /></label>
								<label class="no" style="display:inline;float:none;">No<input type="radio" name="eol" value="0" /></label>
								<br />
							<br />
							Mass Stage: 
							<select name='mass'>
								<option value='0'>Stage 1/Default</option>
								<option value='1'>Stage 2/Reduced</option>
								<option value='2'>Stage 3/Critical</option>
							</select><br /><br />
							<div class="center">
								<button id="wormholeEditorSave">Save</button>
								<button id='wormholeEditorCancel'>Cancel</button>
								<br />
							</div>
						</fieldset>
					</div>
				</div>
				<!-- end wh editor -->

				<div id="systemOptionsPopup" class="box">
					<div id="systemEditor" class="tabcontent">
						<h3>Editing System: <span id="editingSystemName">System</span></h3>
						<div style="padding:10px;">
								<ul class="errors">
								</ul>
								<b>System label/display name</b><br />
								<input type="text" name="label" />
								
								<br /><br />
								<b>Is currently connected/used hole?</b><br />
								<label class="yes" style="display:inline;float:none;">Yes<input type="radio" name="inUse" value="1" /></label>
								<label class="no" style="display:inline;float:none;">No<input type="radio" name="inUse" value="0" /></label>
								<br /><br />
								<b>Activity Level</b><br />
								<select name='activity'>
												<option value='0'>Don't Know</option>
												<option value='1'>Empty</option>
												<option value='2'>Occupied</option>
												<?php if( $group['groupID'] == 7 ): ?>
												<option value='99'>Rengas</option>
												<?php endif; ?>
												<option value='3'>Active</option>
												<option value='4'>Friendly</option>
								</select><br /><br />
								
								
								<div class="center"><button id="systemEditorSave">Save</button><button id='systemEditorCancel'>Cancel</button><br />
								</div>
							</div>							
							
							
					</div>
				</div>
				<!-- end wh editor -->				
				
			<!--- system context -->
				
				<ul id="systemMenu" class="contextMenu">
					<li class="edit">
						<a href="#edit">Edit</a>
					</li>
					<li class="setdest">
						<a href="#setdest">Set Destination</a>
					</li>
<!---
					<li class="routefrom">
						<a href="#routefrom">Route From Here</a>
					</li>--->
				</ul>
			<!--- system context -->
      </div>
      <!--- end chain map -->