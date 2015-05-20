    <div id="exit-finder" class="box" style="display:none;">
        <div class='box-header'>Exit Finder</div>
        <div class='box-content'>
            <p>Find's the nearest exit to the given system or your location.</p>
            <form class='form-inline'>
                <div class="form-group">
                    <label for="exit-finder-target-system">System  </label>
                    <input id='exit-finder-target-system' class="typeahead system-typeahead form-control" type="text" value="" name="target_system" style="width:150px" />
                </div>
                <button name='submit' class="btn btn-default btn-xs" type="submit" style="margin-top: -4px;">Search</button> <br />
				<?php if( miscUtils::isIGB() ): ?>
				<button name='current_location' class="btn btn-default btn-xs">Exits near my location</button>
				<?php endif; ?>
            </form>

                <div id="exit-finder-loading" class="box-load-progress" style="display:none;">
					<img src="<?php echo URL::base(TRUE, TRUE);?>public/images/ajax-loader.gif" />
					<span>Calculating....</span>
				</div>
				<div id="exit-finder-results-wrap" style='max-height:210px;overflow: auto;margin-top: 10px;'>
					<h4>Results</h4>
					<ul id="exit-finder-list" class="box-simple-list">
						<li><b>No exits found</b></li>
					</ul>
				</div>
                <div class="text-center form-actions">
                    <button name='cancel' type="button" class="btn btn-default btn-xs dialog-cancel">Close</button>
                </div>
        </div>
    </div>
