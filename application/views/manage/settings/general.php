<form class="form-horizontal" action="<?php echo URL::base(TRUE,TRUE); ?>manage/settings/general" method="post">
	<legend>Group Settings</legend>
	<?php echo formRenderer::input('Group Name', 'groupName', $data['groupName'], 'The name of your group be it alliance, corp or whatever. This is not important', $errors); ?>
	<?php echo formRenderer::input('Group Ticker', 'groupTicker', $data['groupTicker'], 'The ticker of your group be it alliance, corp or whatever.', $errors); ?>
	<legend>Statistics</legend>
	<?php echo formRenderer::yesNo('Record pilot jump statistics?', 'recordJumps', $data['recordJumps'], 'If yes, siggy will gather per hour, the character jump totals similar to the eve API jumps for systems and display them together as a comparison statistic. Of course this setting depends on users having siggy open in order for the jumps to be recorded.', $errors); ?>
    <?php echo formRenderer::yesNo('Record scan activity?', 'statsEnabled', $data['statsEnabled'], 'If yes, siggy will record the number of sigs added, number of sigs edited and WHs mapped on a daily basis for each character. The recorded data is only currently avaliable as the in group stats board viewable by anyone who has access to your group.', $errors); ?>
		
	<legend>Miscellaneous</legend>
	<?php echo formRenderer::yesNo("Enable 'size' for sig entry?", 'showSigSizeCol', $data['showSigSizeCol'], 'If yes, an additional column and dropdown for entry will appear for sigs to list its calculator size. ', $errors); ?>
  	
	<legend>Auth</legend>
	
	<?php echo formRenderer::select('Auth Mode', 'authMode', array(0 =>'No auth', 1 => 'Group password', 2 => 'API based Login'), $data['authMode'], "
	There are currently three states for auth, no auth and group password. <br />
					<b>No auth:</b> No authenication measures are taken when a person tries to access siggy. This isn't the best mode to use as people can spoof their corporation ID among other things.<br />
					<b>Group Password:</b> The password set below or in individual subgroups will be prompted the first time a user attempts to use siggy on a computer. The password will be remembered by their client until it is changed or client deletes its cookies.<br />
					<b>API based login:</b> Utilizing the same accounts as out of game login, this requires an siggy account with valid API key to access your group.
					", $errors); ?>
	<?php echo formRenderer::password('Group password', 'password', '', "Only enter in a password here and confirm it below if you are trying to set or change the group password, otherwise leave blank and it won't get reset/changed.", $errors); ?>
	<?php echo formRenderer::password('Confirm group password', 'password_confirm', '', "Only enter in a password here and confirm it below if you are trying to set or change the group password, otherwise leave blank and it won't get reset/changed.", $errors); ?>
		
	<legend>Default subgroup</legend>
	<?php echo formRenderer::textarea('Home system(s)', 'homeSystems', $data['homeSystems'], 'This setting allows siggy to do some advanced witchcraft relating to chain map management,signature deletion and possibly other things in the future. This is not required, and all eve systems are accepted as home systems and as many as you want/need. For more than one home system, use comma delimated format i.e. "Jita,Amarr,Dodixie" (without the quotes). This only affects the default subgroup!', $errors); ?>
	<?php echo formRenderer::yesNo('Keep home system sigs that are old when purging?', 'skipPurgeHomeSigs', $data['skipPurgeHomeSigs'], 'If set to yes and if any home system(s) is/are properly set, the signatures within the system(s) will not be purged automatically at around downtime when they are past 24 hours of age.', $errors); ?>
	<?php echo formRenderer::yesNo("Show 'red' in use status wormholes in the system list?", 'sysListShowReds', $data['sysListShowReds'], "By default both systems set as 'in use' and not in use are shown in the system list just above system info. This option hides red systems until set green either automatically or manually. This is useful if you scan enough to fill the area for the list fast.", $errors); ?>
  			
	 <div class="form-actions">
		<button type="submit" class="btn btn-primary">Save changes</button>
		<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
	</div> 			
</form>