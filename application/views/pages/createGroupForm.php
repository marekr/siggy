<div class="container">
	<div class="well">
		<h2>Siggy Group Creation</h2>
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
		<form class="form-horizontal" method="POST" action="<?php echo URL::base(TRUE, TRUE); ?>pages/createGroup/2">
			<legend>General Info</legend>
			<div class="control-group">
				<label class="control-label" for="inputGroupName">Group Name</label>
				<div class="controls">
					<input type="text" id="inputGroupName" name="groupName" placeholder="" value="<?php echo( isset($_POST['groupName']) ? $_POST['groupName'] : '' ); ?>">
					<span class="help-block">Recognizable name to reference your group, no requirement to be an actual corp or alliance but preferred.</span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="inputGroupName">Group Ticker</label>
				<div class="controls">
					<input type="text" id="inputGroupTicker" name="groupTicker" value="<?php echo( isset($_POST['groupTicker']) ? $_POST['groupTicker'] : '' ); ?>">
					<span class="help-block">Ticker to reference your group.</span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="inputContact"  value="<?php echo( isset($_POST['ingameContact']) ? $_POST['ingameContact'] : '' ); ?>">Ingame Contact</label>
				<div class="controls">
					<input type="text" id="inputContact" name="ingameContact"  placeholder="">
					<span class="help-block">Character ingame that should be contacted if any problem or need arises.</span>
				</div>
			</div>
			<legend>Basic Settings</legend>

			<div class="control-group">
				<label class="control-label" for="selectAuthMode">Auth Method</label>
				<div class="controls">
					<select name="authMode" id="selectAuthMode">
					  <option value="0">No Auth</option>
					  <option value="1" selected="selected">Group Password</option>
					  <option value="2">EVE API</option>
					</select>
					<span class="help-block"><strong>No Auth</strong> = No verification is performed to check if a user is really part of your group.</span>
					<span class="help-block"><strong>Group Password</strong> = Password that must be entered by all users of your siggy group. Prevents easy spoofing of character identity.</span>
					<span class="help-block"><strong>EVE API</strong> = Users must log in using siggy accounts associated with valid API keys that prove their identity.</span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="inputGroupPassword">Group Password</label>
				<div class="controls">
					<input type="password" id="inputGroupPassword" name="groupPassword" autocomplete="off">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="inputConfirmGroupPassword">Confirm Group Password</label>
				<div class="controls">
					<input type="password" id="inputConfirmGroupPassword" name="confirmGroupPassword" autocomplete="off">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="textareaHomesystems">Homesystem(s)</label>
				<div class="controls">
					<textarea id="textareaHomesystems" name="homeSystems">
					</textarea>
					<span class="help-block">Comma separated list of home system names. Home systems appear on the chain map at all time and do not disappear when they have no connections like other systems.</span>
				</div>
			</div>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary">Create Group</button>
			</div>
		</form>
	</div>
</div>
