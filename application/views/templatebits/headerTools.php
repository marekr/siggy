<ul id="header-tools-button-bar">
	<li id="global-notes-button">Notes &#x25BC;</li>
	
	<?php if( $group['statsEnabled'] ): ?>
		<li id="stats-button"><a target="_blank" href="<?php echo URL::base(); ?>stats">Stats</a></li>
	<?php endif; ?>
    <li id="settings-button"><i class="icon icon-settings"></i></li>
    <li id="exit-finder-button">Exit Finder</li>
	<?php if( count(Auth::$user->perms) > 0 ): ?>
	<li><a href="<?php echo URL::base(TRUE, TRUE);?>manage">Admin</a></li>
	<?php endif; ?>
    <li><a target="_blank" href="http://wiki.siggy.borkedlabs.com">Guide</a></li>
    <li id="hotkey-button" style="float:right"><i class="fa fa-keyboard-o "></i></li>
</ul>

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

<div id="settings-dialog" class="box" style="display:none;">
	<h3>Settings</h3>
	<div>
        <form action="#" id="settings-form" method="post">
            <label>Theme
                <select name="theme_id">
                <?php foreach($themes as $theme): ?>
                     <option value="<?php echo $theme['theme_id']; ?>" <?php echo( $theme['theme_id'] == $settings['theme_id'] ? 'selected="selected"' : '' ); ?>> <?php echo $theme['theme_name']; ?></option>
                <?php endforeach; ?>
                </select>
            </label>
			<br />
			<br />
			<label>Combine Scan & Intel tabs? <input type="checkbox" name="combine_scan_intel" value="1" <?php echo( $settings['combine_scan_intel'] ? 'checked="checked"' : '' ); ?>/></label>
            <br />
			<br />
			<label>
				EVE Client Language 
				<select name="language">
					<option value="en">English</option>
					<option value="de">Deutsch</option>
				</select>
			</label>
            
            <div class="center-text form-actions">
                <button id="settings-save" type="submit" class="btn btn-primary btn-xs">Save</button>
                <button id='settings-cancel' type="reset" class="btn btn-default btn-xs">Cancel</button><br />
            </div>
        </form>
	</div>
</div>