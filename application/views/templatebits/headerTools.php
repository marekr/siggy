<div id="header-tools-button-bar">
	<div id="global-notes-button" class="header-tool-button">Notes &#x25BC;</div>
	
	<!--- <div id="strengthCalcButton" class="headerToolButton">Strength Calc. &#x25BC;</div> -->
	<?php if( $group['statsEnabled'] ): ?>
		<div id="stats-button" class="header-tool-button"><a target="_blank" href="<?php echo URL::base(); ?>stats">Stats</a></div>
	<?php endif; ?>
</div>
<div id="global-notes">
	<h2>Notes <a href="#" id="global-notes-edit" >[edit]</a></h2>
	<textarea style="width:100%;height:100px;display:none;" id="global-notes-edit-box">
	</textarea>
	<div id="global-notes-content">No notes loaded.</div>
	<div class="center">
		<button style="display:none" id="global-notes-save" class="btn btn-default btn-xs">Save</button>
        <button style="display:none" id="global-notes-cancel" class="btn btn-default btn-xs">Cancel</button><br />
		<p>Last update: <span id="global-notes-time">00:00:00</span></p>
	</div>
</div>