
			<h2>Manage API Key</h2>
			<p>
					You must provide an proper working API key in order to use siggy. The following are requirements of a key:
					<ul>
						<li>Must be a new API Key(customizable keys), the old style limited/full access API keys are not accepted</li>
						<li>The following permissions are required:
							<ul>
								<li>CharacterInfo Public</li>
								<li>CharacterInfo Private</li>
							</ul>
						</li>
						<li>Keys must be set to never expire or you will lose access immediately</li>
						<li>Please don't use API keys that do not belong to any of your accounts</li>
					</ul>
					
			</p>
			<h3>API Key Status</h3>
			<div class="well">
					<?php if( $status == 'missing'): ?>
							Nothing currently set
					<?php elseif ($status == 'invalid'): ?>
							Invalid Key Combination
					<?php elseif ($status == 'failed'): ?>
							Failed more than 3 times, this may be due to invalid key permissions.
					<?php else: ?>
							Set and valid
					<?php endif; ?>
			</div>
			<form class="form-horizontal" action='<?php echo URL::base(TRUE, TRUE);?>account/setAPI' method='POST'>
				<legend>New API Key Entry</legend>
				<div class="control-group">
						<label class="control-label" for="inputID">API ID</label>
						<div class="controls">
						<input type="text" id="inputID" name="apiID" placeholder="ID">
					</div>
				</div>
				<div class="control-group">
						<label class="control-label" for="inputKey">API Key</label>
						<div class="controls">
						<input type="text" id="inputKey" name="apiKey" placeholder="Key">
					</div>
				</div>
				<div class="form-actions">
					<button type="submit" name='set' class="btn btn-primary">Save changes</button>
				</div>
			</form>