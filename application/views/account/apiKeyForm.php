
			<?php if( $mode == 'add' ): ?>
				<h2>Add New API Key</h2>
			<?php else: ?>
				<h2>Edit API Key</h2>
			<?php endif; ?>
			<?php if( $mode == 'add' ): ?>
			<form class="form-horizontal" action='<?php echo URL::base(TRUE, TRUE);?>account/addAPI' method='POST'>
			<input type="hidden" name="mode" value="add" />
			<?php else: ?>
			<form class="form-horizontal" action='<?php echo URL::base(TRUE, TRUE);?>account/editAPI/<?php echo $keyData['entryID']; ?>' method='POST'>
			<input type="hidden" name="mode" value="edit" />
			<?php endif; ?>
				<legend>API Key Entry</legend>
				<?php echo formRenderer::input('API ID', 'apiID', $keyData['apiID'], '', $errors); ?>
				<?php echo formRenderer::input('API Key', 'apiKey', $keyData['apiKey'], '', $errors); ?>
				<div class="form-actions">
				<?php if( $mode == 'add' ): ?>
						<button type="submit" name='set' class="btn btn-primary">Add key</button>
				<?php else: ?>
						<button type="submit" name='set' class="btn btn-primary">Save key</button>
				<?php endif; ?>
				</div>
			</form>