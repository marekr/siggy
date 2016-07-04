<div class="container">
	<div class="row">
		<div class="well">
			<h2>siggy Group Creation</h2>
			<?php if(count($errors)): ?>
			<div class="alert alert-error">
				<strong>You must fix the following errors before proceeding:</strong>
				<ul>
				<?php foreach($errors as $error): ?>
					<li><?php echo $error; ?></li>
				<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>
			<form method="POST" action="<?php echo URL::base(TRUE, TRUE); ?>pages/createGroup/2">
				<fieldset>
					<legend>General Info</legend>
					<?php echo formRenderer::input('Group Name', 'groupName', '', '', $errors); ?>
					<?php echo formRenderer::input('Group Ticker', 'groupTicker', '', '', $errors); ?>
				</fieldset>
				<fieldset>
				<legend>Basic Settings</legend>
					<?php echo formRenderer::yesNo("Group password required?", 'group_password_required', '', 'If yes, siggy will prompt for a password from all users, this is highly recommended.', $errors); ?>

				
					<?php echo formRenderer::password('Group Password', 'group_password', '', '', $errors); ?>
					<?php echo formRenderer::password('Confirm Group Password', 'confirm_group_password', '', '', $errors); ?>
				</fieldset>
				<div class="form-actions">
					<button type="submit" class="btn btn-primary">Create Group</button>
				</div>
			</form>
		</div>
	</div>
</div>

