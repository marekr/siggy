    <div id="exit-finder" class="box" style="display:none;">
        <div class='box-header'>Exit Finder</div>
        <div class='box-content'>
            <p>Find's the nearest exit to the given system or your location.</p>
            <form>
				<label>
					System
					<input class="siggy-input" type="text" value="" name="target_system" style="width:150px" />
					<button name='submit' class="btn btn-default btn-xs" type="submit" style="margin-top: -4px;">Search</button> <br />
				</label>
				<?php if( miscUtils::isIGB() ): ?>
				<button name='current_location' class="btn btn-default btn-xs">Exits near my location</button>
				<?php endif; ?>
				<div id="exit-finder-loading" class="box-load-progress" style="display:none;">
					<img src="<?php echo URL::base(TRUE, TRUE);?>public/images/ajax-loader.gif" />
					<span>Calculating....</span>
				</div>
				<div id="exit-finder-results-wrap" style='max-height:210px;overflow: auto;margin-top: 10px;'>
					<h3>Results</h3>
					<ul id="exit-finder-list" class="box-simple-list">
						<li><b>No exits found</b></li>
					</ul>
				</div>
                <div class="text-center form-actions">
                    <button name='cancel' type="button" class="btn btn-default btn-xs">Close</button>
                </div>
            </form>
        </div>
    </div>
