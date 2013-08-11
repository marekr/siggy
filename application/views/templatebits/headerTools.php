<div id="headerToolsButtonBar">
	<div id="globalNotesButton" class="headerToolButton">Notes &#x25BC;</div>
	
	<!--- <div id="strengthCalcButton" class="headerToolButton">Strength Calc. &#x25BC;</div> -->
	<?php if( $group['statsEnabled'] ): ?>
		<div id="statsButton" class="headerToolButton"><a href="<?php echo URL::base(); ?>stats">Stats</a></div>
	<?php endif; ?>
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