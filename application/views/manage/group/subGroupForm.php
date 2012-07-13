<?php
$form = new Appform();
if(isset($errors)) {
   $form->errors = $errors;
}
if(isset($data)) {
   $form->values = $data;
}

if( $mode == 'edit' )
{
	echo $form->open('manage/group/editSubGroup/'.$id);
}
else
{
	echo $form->open('manage/group/addSubGroup');
}
?>
   <h1><?php echo ($mode == 'edit' ?  __('Edit Sub Group') : __('Add Sub Group') ); ?></h1>
   <div class="content">

	<table class='settings'>
		<tr class='header'>
			<th colspan='2'>General</th>
		</tr>
		<tr>
			<td style='width:50%'><b><?php echo __('Subgroup name'); ?></b></td>
			<td style='width:50%'><?php echo $form->input('sgName') ?></td>
		</tr>
		<tr class='odd'>
			<td style='width:50%'><b><?php echo __('Home systems'); ?></b>
				<p class='desc'>This setting allows siggy to do some advanced witchcraft relating to chain map management,signature deletion and possibly other things in the future. This is not required, and all eve systems are accepted as home systems and as many as you want/need. For more than one home system, use comma delimated format i.e. "Jita,Amarr,Dodixie" (without the quotes). This only affects the default subgroup!</p>
			</td>
			<td style='width:50%'><?php echo $form->textarea('sgHomeSystems', null, array('rows' => '5')); ?></td>
		</tr>
		<tr>
			<td style='width:50%'><b><?php echo __('Purge home system sigs?'); ?></b>
				<p class='desc'>If any home system(s) is/are properly set, the signatures within the system(s) will not be purged automatically at around downtime when they are past 24 hours of age.</p>
			</td>
			<td style='width:50%'><?php echo $form->yes_no('sgSkipPurgeHomeSigs', null) ?></td>
		</tr>
		<tr>
			<td style='width:50%'><b><?php echo __("Show 'red' in use status wormholes in the system list?"); ?></b>
				<p class='desc'>By default both systems set as 'in use' and not in use are shown in the system list just above system info. This option hides red systems until set green either automatically or manually. This is useful if you scan enough to fill the area for the list fast.</p>
			</td>
			<td style='width:50%'><?php echo $form->yes_no('sgSysListShowReds', null) ?></td>
		</tr>
		<tr class='header'>
			<th colspan='2'>Auth</th>
		</tr>
		<tr  class='odd'>
			<td style='width:50%'><b><?php echo __('Sub group password'); ?></b>
				<p class='desc'>This setting only works if group password auth is turned on in the main settings. This setting is <b>NOT REQUIRED</b>, the subgroup will use the main group password by default unless you set a different one here. Unless you need to set a sub group password or change it, leave this field blank.</p>
			</td>
      <td style='width:50%'><?php echo $form->password('password', null); ?></td>
		</tr>
		<tr>
			<td style='width:50%'><b><?php echo __('Confirm sub group password'); ?></b></td>
      <td style='width:50%'><?php echo $form->password('password_confirm', null); ?></td>
		</tr>   
		<tr class='buttonrow'>
			<td colspan='3'>
			<?php 
			if( $mode == 'edit' )
			{
				echo $form->submit(NULL, __('Edit'));
			}
			else
			{
				echo $form->submit(NULL, __('Add'));
			}
			?>
			</td>
		</tr>
	</table>
<?php
echo $form->close();
?>
   </div>