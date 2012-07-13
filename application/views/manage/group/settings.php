<?php
$form = new Appform();
if(isset($errors)) {
   $form->errors = $errors;
}
if(isset($data)) {
   $form->values = $data;
}
echo $form->open('manage/group/settings');

?>
   <h1>Group settings</h1>
   <div class="content">   
	 <table class='settings'>
		<tr class='header'>
			<th colspan='2'>General</th>
		</tr>
		<tr>
			<td style='width:50%'><b><?php echo __('Group Name'); ?></b><p class='desc'>The name of your group be it alliance, corp or whatever. This is not important.</p></td>
			<td style='width:50%'><?php echo $form->input('groupName', null); ?></td>
		</tr>
		<tr class='odd'>
			<td style='width:50%'><b><?php echo __('Group Ticker'); ?></b><p class='desc'>The ticker of your group be it alliance, corp or whatever. This is not important but is displayed on the siggy main page.</p></td>
			<td style='width:50%'><?php echo $form->input('groupTicker', null); ?></td>
		</tr>
		<tr class='header'>
			<th colspan='2'>Statistics</th>
		</tr>
		<tr>
			<td style='width:50%'><b><?php echo __('Record pilot jump statistics?'); ?></b>
			<p class='desc'>If yes, siggy will gather per hour, the character jump totals similar to the eve API jumps for systems and display them together as a comparison statistic. Of course this setting depends on users having siggy open in order for the jumps to be recorded.</p>
			</td>
			<td style='width:50%'><?php echo $form->yes_no('recordJumps', null) ?></td>
		</tr>
		<tr  class='odd'>
			<td style='width:50%'><b><?php echo __('Record scan activity?'); ?></b>
			<p class='desc'>If yes, siggy will record the number of sigs added, number of sigs edited and WHs mapped on a daily basis for each character. The recorded data is only currently avaliable
			as the in group stats board viewable by anyone who has access to your group.</p>
			</td>
			<td style='width:50%'><?php echo $form->yes_no('statsEnabled', null) ?></td>
		</tr>		
		<tr class='header'>
			<th colspan='2'>Miscellaneous</th>
		</tr>
		<tr>
			<td style='width:50%'><b><?php echo __("Enable 'size' for sig entry?"); ?></b>
			<p class='desc'>If yes, an additional column and dropdown for entry will appear for sigs to list its calculator size. </p>
			</td>
			<td style='width:50%'><?php echo $form->yes_no('showSigSizeCol', null) ?></td>
		</tr>		
		<!--
		<tr  class='odd'>
			<td style='width:50%'><b><?php echo __('Record scan activity?'); ?></b>
			<p class='desc'>If yes, siggy tally the scanning activity entered into siggy per character per day. </p>
			</td>
			<td style='width:50%'><?php echo $form->yes_no('recordScanning', null) ?></td>
		</tr>		
		<tr class='header'>
			<th colspan='2'>Logging</th>
		</tr>
		<tr>
			<td style='width:50%'><b><?php echo __('Log sig deletions?'); ?></b>
			<p class='desc'>If yes, siggy will record who, where and when a sig was deleted in a system along with what it was.</p>
			</td>
			<td style='width:50%'><?php echo $form->yes_no('logSigDel', null) ?></td>
		</tr>
		<tr class='odd'>
			<td style='width:50%'><b><?php echo __('Log sig edits?'); ?></b>
			<p class='desc'>If yes, siggy will record who, where and when a sig was edited.</p>
			</td>
			<td style='width:50%'><?php echo $form->yes_no('logSigEdits', null) ?></td>
		</tr>
		<tr>
			<td style='width:50%'><b><?php echo __('Prune after X days'); ?></b>
			<p class='desc'>By default these logs will be deleted after 7 days, otherwise set a different time, you are limited to 30 days at most.</p>
			</td>
			<td style='width:50%'>NUMERIC</td>
		</tr>
		-->
		<tr class='header'>
			<th colspan='2'>Auth</th>
		</tr>
		<tr>
			<td style='width:50%'><b><?php echo __('Auth Mode (in game)'); ?></b>
				<p class='desc'>There are currently three states for auth, no auth and group password. <br />
					<b>No auth:</b> No authenication measures are taken when a person tries to access siggy. This isn't the best mode to use as people can spoof their corporation ID among other things.<br />
					<b>Group Password:</b> The password set below or in individual subgroups will be prompted the first time a user attempts to use siggy on a computer. The password will be remembered by their client until it is changed or client deletes its cookies.<br />
					<b>API based login:<b> Utilizing the same accounts as out of game login, this requires an siggy account with valid API key to access your group.
				</p>
			</td>
      <td style='width:50%'><?php echo $form->select('authMode', array(0 =>'No auth', 1 => 'Group password', 2 => 'API based Login') ); ?></td>
		</tr>
		<tr  class='odd'>
			<td style='width:50%'><b><?php echo __('Group password'); ?></b>
				<p class='desc'>Only enter in a password here and confirm it below if you are trying to set or change the group password, otherwise leave blank and it won't get reset/changed.</p>
			</td>
      <td style='width:50%'><?php echo $form->password('password', null); ?></td>
		</tr>
		<tr>
			<td style='width:50%'><b><?php echo __('Confirm  group password'); ?></b></td>
      <td style='width:50%'><?php echo $form->password('password_confirm', null); ?></td>
		</tr>   		
		<tr class='header'>
			<th colspan='2'>Default subgroup</th>
		</tr>
		<tr class='odd'>
			<td style='width:50%'><b><?php echo __('Home system(s)'); ?></b>
				<p class='desc'>This setting allows siggy to do some advanced witchcraft relating to chain map management,signature deletion and possibly other things in the future. This is not required, and all eve systems are accepted as home systems and as many as you want/need. For more than one home system, use comma delimated format i.e. "Jita,Amarr,Dodixie" (without the quotes). This only affects the default subgroup!</p>
			</td>
			<td style='width:50%'><?php echo $form->textarea('homeSystems', null, array('rows' => '5') ); ?></td>
		</tr>
		<tr>
			<td style='width:50%'><b><?php echo __("Keep home system sigs that are old when purging?"); ?></b>
				<p class='desc'>If set to yes and if any home system(s) is/are properly set, the signatures within the system(s) will not be purged automatically at around downtime when they are past 24 hours of age.</p>
			</td>
			<td style='width:50%'><?php echo $form->yes_no('skipPurgeHomeSigs', null) ?></td>
		</tr>
		<tr>
			<td style='width:50%'><b><?php echo __("Show 'red' in use status wormholes in the system list?"); ?></b>
				<p class='desc'>By default both systems set as 'in use' and not in use are shown in the system list just above system info. This option hides red systems until set green either automatically or manually. This is useful if you scan enough to fill the area for the list fast.</p>
			</td>
			<td style='width:50%'><?php echo $form->yes_no('sysListShowReds', null) ?></td>
		</tr>
		<tr class='buttonrow'>
			<td colspan='2'><?php echo $form->submit(NULL, __('Save')); ?></td>
		</tr>
	 </table>
   <br>
<?php 
echo $form->close();
?>
   </div>