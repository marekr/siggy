<div id="settings-dialog" class="box" style="display:none;">
	<h3><i class="fa fa-gear"></i> Settings</h3>
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