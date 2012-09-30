<div id="headerToolsButtonBar">
	<div id="globalNotesButton" class="headerToolButton">Notes &#x25BC;</div>
	
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