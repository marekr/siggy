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
	echo $form->open('manage/group/editMember/'.$id);
}
else
{
	echo $form->open('manage/group/addMember');
}

$select = array();
$select[0] = 'Default/No sub group';
$subgroups = $group->subgroups->find_all()->as_array();
foreach($subgroups as $s )
{
	$select[$s->subGroupID] =$s->sgName;
}

$type = array('corp' => 'Corp', 'char' => 'Character');

?>
   <h1><?php echo ($mode == 'edit' ?  __('Edit Group Member') : __('Add Group Member') ); ?></h1>
   <div class="content">
	 <table class='settings'>
		<tr class='header'>
			<th colspan='2'>General</th>
		</tr>
    <tr>
			<td style='width:50%'><b><?php echo __('Member Type'); ?></b></td>
			<td style='width:50%'><?php echo $form->select('memberType', $type); ?></td>
    </tr>
		<tr class='odd'>
			<td style='width:50%'><b><?php echo __('EVE/Corp ID'); ?></b>
				<p class='desc'>
				This is EVE's ingame ID for the corp OR character, it can be found by trying to access http://siggy.borkedlabs.com with a character in a corporation with no access or at DOTLAN(if the corp is in an alliance) or in the corp's api info. 
				</p>
			</td>
			<td style='width:50%'><?php echo $form->input('eveID', null); ?></td>
		</tr>
		<tr>
			<td style='width:50%'><b><?php echo __('Access Name/Corp Ticker'); ?></b>
			</td>
			<td style='width:50%'><?php echo $form->input('accessName') ?></td>
		</tr>
      <?php if( count($subgroups) > 0 ): ?>
		<tr class='odd'>
			<td style='width:50%'><b><?php echo __('Subgroup'); ?></b></td>
			<td style='width:50%'><?php echo $form->select('subGroupID', $select); ?></td>
		</tr>		
      <?php endif; ?>
      <tr class='buttonrow'>
			<td colspan='2'>
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
   <br>
<?php 
echo $form->close();
?>
   </div>