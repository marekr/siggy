<?php

if( $mode == 'edit' )
{
	$formUrl = ('manage/group/editSubGroup/'.$id);
}
else
{
	$formUrl = ('manage/group/addSubGroup');
}

?>
   <h2><?php echo ($mode == 'edit' ?  __('Edit Sub Group') : __('Add Sub Group') ); ?></h2>
	<form class="form-horizontal" action="<?php echo URL::base(TRUE,TRUE); ?><?php echo $formUrl; ?>" method="post">
	<legend>General</legend>
	<?php echo formRenderer::input('Subgroup name', 'sgName', $data['sgName'], '', $errors); ?>
	<?php echo formRenderer::textarea('Home system(s)', 'sgHomeSystems', $data['sgHomeSystems'], 'This setting allows siggy to do some advanced witchcraft relating to chain map management,signature deletion and possibly other things in the future. This is not required, and all eve systems are accepted as home systems and as many as you want/need. For more than one home system, use comma delimated format i.e. "Jita,Amarr,Dodixie" (without the quotes). This only affects the default subgroup!', $errors); ?>
	<?php echo formRenderer::yesNo('Purge home system sigs?', 'sgSkipPurgeHomeSigs', $data['sgSkipPurgeHomeSigs'], 'If yes, siggy will gather per hour, the character jump totals similar to the eve API jumps for systems and display them together as a comparison statistic. Of course this setting depends on users having siggy open in order for the jumps to be recorded.', $errors); ?>
	<?php echo formRenderer::yesNo('Record pilot jump statistics?', 'sgSysListShowReds', $data['sgSysListShowReds'], "By default both systems set as 'in use' and not in use are shown in the system list just above system info. This option hides red systems until set green either automatically or manually.", $errors); ?>
  
	
	<legend>Auth</legend>
	<?php echo formRenderer::password('Sub group password', 'password', '', "This setting only works if group password auth is turned on in the main settings. This setting is <b>NOT REQUIRED</b>, the subgroup will use the main group password by default unless you set a different one here. Unless you need to set a sub group password or change it, leave this field blank.", $errors); ?>
	<?php echo formRenderer::password('Confirm sub group password', 'password_confirm', '', "Only enter in a password here and confirm it below if you are trying to set or change the group password, otherwise leave blank and it won't get reset/changed.", $errors); ?>

	 <div class="form-actions">
			<?php 
			if( $mode == 'edit' ): ?>
				<button type="submit" class="btn btn-primary">Save changes</button>
			<?php
			else: ?>
				<button type="submit" class="btn btn-primary">Create subgroup</button>
			<?php endif; ?>
			<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
	</div> 					
	</form>