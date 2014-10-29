
		<?php if( $mode == 'add' ): ?>
		<h3>Add New API Key</h3>
		<?php else: ?>
		<h3>Edit API Key</h3>
		<?php endif; ?>

		<p>
			You must provide an proper working API key in order to use siggy. The following are requirements of a key:
			<ul>
				<li>Must be a new API Key(customizable keys).</li>
				<li>No permissions are required, except you must either set it to include the character with access or all characters</li>
				<li>Keys must be set to never expire or you will lose access</li>
				<li>Please do not use API keys that do not belong to you</li>
			</ul>
		</p>
		<?php if( $mode == 'add' ): ?>
		<form action='<?php echo URL::base(TRUE, TRUE);?>account/addAPI' method='POST'>
			<input type="hidden" name="mode" value="add" />
		<?php else: ?>
		<form action='<?php echo URL::base(TRUE, TRUE);?>account/editAPI/<?php echo $keyData['entryID']; ?>' method='POST'>
			<input type="hidden" name="mode" value="edit" />
		<?php endif; ?>
			<?php echo formRenderer::input('API ID', 'apiID', $keyData['apiID'], '', $errors); ?>
			<?php echo formRenderer::input('API Key', 'apiKey', $keyData['apiKey'], '', $errors); ?>
			<div class="form-group">
				<div class="controls">
			<?php if( $mode == 'add' ): ?>
					<button type="submit" name='set' class="btn btn-primary">Add key</button>
			<?php else: ?>
					<button type="submit" name='set' class="btn btn-primary">Save key</button>
			<?php endif; ?>
				</div>
			</div>
		</form>