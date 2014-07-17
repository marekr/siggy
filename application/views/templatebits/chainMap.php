<!--- chain map -->
<div id="chain-map-container" class="bordered-wrap">
		<ul id="chain-map-tabs" class="clearfix">
			<li class="active">Default</li>
			<li><i class="fa fa-fw fa-lock"></i> Passworded</li>
			<li> Lowsex</li>
			<li> <i class="fa fa-fw fa-plus"></i></li>
			<li class="minimize"><i class="fa fa-fw fa-minus-square"></i></li>
		</ul>
		<div id='chain-map-inner' <?php echo ( !$mapOpen ? "style='display:none'" : '' ); ?>>
			<p class="loading">Loading....<br /><span style='font-size:0.3em;'>(This may take up to 10 seconds.)</span></p>
			<p class="editing">Editing: Drag systems by clicking on them.</p>
			<p class="deleting">Click on(or mouse drag) wormhole links to select for deletion</p>
			<div id="chain-map-scrolltainer">
				<div id="chain-map">
				</div>
			</div>
			<!-- magic buttons -->
			<div class="buttons">
				<button id="chain-map-save" style="display:none;" class="btn btn-default btn-xs">Save Map Changes</button>
				<button id="chain-map-mass-delete-confirm" style="display:none;" class="btn btn-default btn-xs">Confirm Mass Delete</button>
				<button id="chain-map-mass-delete-cancel" style="display:none;" class="btn btn-default btn-xs">Cancel</button>
			</div>
		</div>
		<!-- magic buttons -->
		<div id='map-footer-bar'>
			<!-- options -->

				<a href="#" id="chain-map-add-wh" class="btn btn-default btn-xs">Add WH</a>
				<a href="#" id="chain-map-edit" class="btn btn-default btn-xs">Edit</a>
				<a href="#" id="chain-map-delete-whs" class="btn btn-default btn-xs">Delete WHs</a>
				<a href="#" id="chain-map-delete-whs" class="btn btn-default btn-xs">Copy WHs</a>
				
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
		
		<!--- wh editor -->
		<div id="wormhole-popup" class="box">
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
				<div class="center-text">
					<button id='jumpLogClose' class="btn btn-default btn-xs">Close</button>
				</div>
			</div>
			<?php endif; ?>
			<div id="wormhole-editor" class="tabcontent">
				<div id="wh-editor-add">
					<h3>Create a wormhole link</h3>
					<br />
					<div style="float:left;text-align:right;">
						From <input type='text' name='from-sys' /><br />
						<label style="display:block;margin-top:3px;">
							<input type='checkbox' name='fromCurrent' value='1' />  Current Location
						</label>
						</div>
					<div style="float:right;text-align:right;">
						To <input type='text' name='to-sys' /><br />
						<label style="display:block;margin-top:3px;">
							<input type='checkbox' name='toCurrent' value='1' />  Current Location
						</label>
					</div>
					<br clear="all" />
				</div>
				<div id="wh-editor-edit" class='center-text'>
					<h3 class="text-left">Editing Wormhole</h3>
					<p>
						<span id="whEditFrom"></span> to <span id="whEditTo"></span>
					</p>
					<button class="btn btn-default btn-xs" id="wormhole-editor-disconnect" >Close Wormhole</button>
				</div>
				<ul class="errors">
				</ul>
				<br />
				<fieldset>
					<legend>Options</legend>
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
					<div class="center-text">
						<button id="wormhole-editor-save" class="btn btn-primary btn-xs">Save</button>
						<button id='wormhole-editor-cancel' class="btn btn-default btn-xs">Cancel</button>
						<br />
					</div>
				</fieldset>
			</div>
		</div>
		<!-- end wh editor -->

		<div id="system-options-popup" class="box">
			<h3>Editing System: <span id="editingSystemName">System</span></h3>
			<div id="system-editor">
				<ul class="errors">
				</ul>
				<b>System label/display name</b>
				<br />
				<input type="text" name="label" />
				
				<br />
				<br />
				<b>Activity Level</b>
				<br />
				<select name='activity'>
					<option value='0'>Don't Know</option>
					<option value='1'>Empty</option>
					<option value='2'>Occupied</option>
					<option value='3'>Active</option>
					<option value='4'>Friendly</option>
				</select>
				<br />
				<br />
				
				
				<div class="center-text form-actions">
					<button id="system-editor-save" class="btn btn-xs btn-primary">Save</button>
					<button id='system-editor-cancel' class="btn btn-default btn-xs">Cancel</button><br />
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
			<li class="showinfo">
				<a href="#showinfo">Show Info</a>
			</li>
		</ul>
		<!--- system context -->
</div>
<!--- end chain map -->