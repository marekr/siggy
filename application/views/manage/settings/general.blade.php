@extends('layouts.manage',[
							'title' => 'siggy.manage: chainmap settings'
						])

@section('content')
<form role="form" action="<?php echo URL::base(TRUE,TRUE); ?>manage/settings/general" method="POST">
	<fieldset>
		<legend>Group Settings</legend>
		<?php echo formRenderer::input('Group Name', 'groupName', $group->name, 'The name of your group be it alliance, corp or whatever. This is not important', $errors); ?>
		<?php echo formRenderer::input('Group Ticker', 'groupTicker', $group->ticker, 'The ticker of your group be it alliance, corp or whatever.', $errors); ?>
	</fieldset>

	<fieldset>
		<legend>Miscellaneous</legend>
		<?php echo formRenderer::yesNo("Enable 'size' for sig entry?", 'showSigSizeCol', $group->show_sig_size_col, 'If yes, an additional column and dropdown for entry will appear for sigs to list its calculator size. ', $errors); ?>
		<?php echo formRenderer::select('Default activity', 'default_activity', ['' => 'None', 'siggy' => 'Scan', 'thera' => 'Thera', 'scannedsystems' => 'Scanned Systems'], $group->default_activity, "", $errors); ?>
	</fieldset>
  	
	<fieldset>
	<legend>Auth</legend>
		<?php echo formRenderer::yesNo("Group password required?", 'group_password_required', $group->password_required, 'If yes, siggy will prompt for a password from all users, this is highly recommended.', $errors); ?>

		<?php echo formRenderer::password('Group password', 'password', '', "Only enter in a password here and confirm it below if you are trying to set or change the group password, otherwise leave blank and it won't get reset/changed.", $errors); ?>
		<?php echo formRenderer::password('Confirm group password', 'password_confirm', '', "Only enter in a password here and confirm it below if you are trying to set or change the group password, otherwise leave blank and it won't get reset/changed.", $errors); ?>
	
	</fieldset>
  			
	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Save changes</button>
		<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
	</div> 			
</form>
@endsection