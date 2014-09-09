<!--- chain map -->


<!--- TODO: FIX container style, jquery block breaks the dropdown otherwise ---->
<div id="chain-map-container" class="bordered-wrap" style="relative !important">
		<ul id="chain-map-tabs" class="clearfix">
			<li class="tab tab-active dropdown">
				<a data-toggle="dropdown" href="#" id="chain-map-title">
					Maps<span style='font-style:normal;font-weight:bold;'>&#x25BC;</span>
				</a>
				<ul id="chainmap-dropdown" class="dropdown-menu" role="menu" aria-labelledby="dLabel">
					<li>
						Test
					</li>
					<li>
						Test 3
					</li>
				</ul>
				<br clear="all" />
			</li>
			<!--- <li class="tab add"><i class="fa fa-fw fa-plus"></i></li> --->
			<li class="tab minimize"><i class="fa fa-fw fa-minus-square"></i></li>
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
				<button id="chain-map-mass-delete-confirm" style="display:none;" class="btn btn-default btn-danger btn-xs">Confirm Mass Delete</button>
				<button id="chain-map-mass-delete-cancel" style="display:none;" class="btn btn-default btn-primary btn-xs">Cancel</button>
			</div>
		</div>
		<!-- magic buttons -->
		<div id='map-footer-bar'>
			<!-- options -->
			<a href="#" id="chain-map-add-wh" class="btn btn-default btn-xs btn-primary"><i class="fa fa-link"></i> Add Connection</a>
			<a href="#" id="chain-map-edit" class="btn btn-default btn-xs btn-warning"><i class="fa fa-pencil"></i> Edit</a>
			<a href="#" id="chain-map-delete-whs" class="btn btn-default btn-xs btn-danger"><i class="fa fa-chain-broken"></i> Delete Connections</a>
			<!--- end options -->
			<!--- broadcast -->
			<?php if( !$group['alwaysBroadcast'] ): ?>
			<a href="#" id="chainmap-broadcast-button" class="btn btn-default btn-xs" style="float:right"><i class="fa fa-wifi"></i><?php echo ( ( isset($_COOKIE['broadcast']) && $_COOKIE['broadcast'] == 0 ) ? "Enable location broadcast" : "Disable location broadcast" ); ?></a>
			<?php endif; ?>
			<div class="clearfix"></div>
			<!-- end broadcast -->
		</div>
		
		<div id="connection-popup" class="box box-tabbed" style="display:none">
			<ul class="box-tabs">
				<li class="active"><a href="#connection-editor">Edit</a></li>
				<?php if( $group['jumpLogEnabled'] ): ?>
				<li><a href="#jump-log">Jump Log</a></li>
				<?php endif; ?>
			</ul>
			<?php if( $group['jumpLogEnabled'] ): ?>
			<div id="jump-log" style="display:none;padding: 10px !important;" class="box-tab">
				<div class="center-text" style="margin:10px;">
					<a id='jump-log-refresh' class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i> Refresh Log</a>
				</div>
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
			<div id="connection-editor" class="box-tab">
				<div id="connection-editor-add" class="connection-editor-group">
					<h3>Create a connection</h3>
					<div>
						<div style="float:left;text-align:right;">
							From <input type='text' name='from-sys' /><br />
							<label style="display:block;margin-top:3px;">
								<input type='checkbox' name='from-current-location' value='1' />  Current Location
							</label>
						</div>
						<div style="float:right;text-align:right;">
							To <input type='text' name='to-sys' /><br />
							<label style="display:block;margin-top:3px;">
								<input type='checkbox' name='to-current-location' value='1' />  Current Location
							</label>
						</div>
						<div class="clearfix"></div>
						<br />
						<div class="form-group">
							<label>Connection Type
								<select name="connection-editor-type">
									<option value="wormhole">Wormhole</option>
									<option value="stargate">Stargate</option>
									<option value="jumpbridge">Jumpbridge</option>
									<option value="cyno">Cyno</option>
								</select>
							</label>
						</div>
					</div>
				</div>
				<div id="connection-editor-edit" class='connection-editor-group center-text'>
					<h3 class="text-left">Editing Connection</h3>
					<div>
						<p>
							<span id="connection-editor-from"></span> to <span id="connection-editor-to"></span>
						</p>
						<button class="btn btn-default btn-xs btn-danger" id="connection-editor-disconnect"><i class="fa fa-chain-broken"></i> Disconnect Connection</button>
					</div>
				</div>
				<div id="connection-editor-options-wh" class="connection-editor-group" style="background-color:#00279A">
					<h3>Options</h3>
					<ul class="errors">
					</ul>
					<div>
						<div class="form-group">
							<label>WH Type Name <input type='text' name='wh_type_name' style='width:40px'/></label>
						</div>
						<div class="form-group">
							<label>EOL?: 	
									<label class="yes" style="display:inline;float:none;">Yes<input type="radio" name="eol" value="1" /></label>
									<label class="no" style="display:inline;float:none;">No<input type="radio" name="eol" value="0" /></label>
							</label>
						</div>
						<div class="form-group">
							<label>
							Frigate sized?: <label class="yes" style="display:inline;float:none;">Yes<input type="radio" name="frigate_sized" value="1" /></label>
											<label class="no" style="display:inline;float:none;">No<input type="radio" name="frigate_sized" value="0" /></label>
							</label>
						</div>
						<div class="form-group">		
							<label>
								Mass Stage:
								<select name='mass'>
									<option value='0'>Stage 1/Default</option>
									<option value='1'>Stage 2/Reduced</option>
									<option value='2'>Stage 3/Critical</option>
								</select>
							</label>
						</div>
					</div>
				</div>
				<div id="connection-editor-button-bar" class="center-text connection-editor-group">
					<button id="connection-editor-save" class="btn btn-primary btn-xs">Save</button>
					<button id='connection-editor-cancel' class="btn btn-default btn-xs">Cancel</button>
				</div>
			</div>
		</div>

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
			<li class="set-rally">
				<a href="#set-rally">Set Rally</a>
			</li>
			<li class="clear-rally">
				<a href="#clear-rally">Clear Rally</a>
			</li>
			<li class="showinfo">
				<a href="#showinfo">Show Info</a>
			</li>
		</ul>
		<!--- system context -->
		
		<!--- wh context -->
		<ul id="wh-menu" class="contextMenu">
			<li class="set-stage-1">
				<a href="#set-stage-1">Set Stage 1</a>
			</li>
			<li class="set-stage-2">
				<a href="#set-stage-2">Set Stage 2</a>
			</li>
			<li class="set-stage-3">
				<a href="#set-stage-3">Set Stage 3</a>
			</li>
			<li class="set-eol">
				<a href="#set-eol">Set EOL</a>
			</li>
			<li class="clear-eol">
				<a href="#clear-eol">Clear EOL</a>
			</li>
			<li class="set-frigate">
				<a href="#set-frigate">Set as Frigate hole</a>
			</li>
			<li class="unmark-frigate">
				<a href="#unmark-frigate">Unmark as Frigate Hole</a>
			</li>
		</ul>
		<!--- wh context -->
</div>
<!--- end chain map -->
