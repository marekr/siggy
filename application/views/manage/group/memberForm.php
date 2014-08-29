<?php

if( $mode == 'edit' )
{
	$formUrl = URL::base(TRUE,TRUE).'manage/group/editMember/'.$id;
}
else
{
	$formUrl =  URL::base(TRUE,TRUE).'manage/group/addMember';
}

$select = array();
foreach($chainmaps as $c )
{
	$select[$c['chainmap_id']] = $c['chainmap_name'];
}

$type = array('corp' => 'Corp', 'char' => 'Character');

?>
   <form class="form-horizontal" action="<?php echo $formUrl; ?>" method="post">
   <h2><?php echo ($mode == 'edit' ?  __('Edit Group Member') : __('Add Group Member') ); ?></h2>
		<?php echo formRenderer::select('Member Type', 'memberType', $type, $data['memberType'], '', $errors); ?>
		<?php echo formRenderer::input('EVE/Corp ID', 'eveID', $data['eveID'], 'ID Number from EVE for the character or corp', $errors); ?>
		<?php echo formRenderer::input('Access Name/Corp Ticker', 'accessName', $data['accessName'], '', $errors); ?>
		<?php if( count($subgroups) > 0 ): ?>
			<?php echo formRenderer::select('Subgroup', 'subGroupID', $select, $data['subGroupID'], '', $errors); ?>
		<?php endif; ?>

		<div class="form-actions">
			<?php 
			if( $mode == 'edit' ):
			?>
			<button type="submit" class="btn btn-primary">Edit member</button>
			<?php
			else: 
			?>
			<button type="submit" class="btn btn-primary">Add member</button>
			<?php endif; ?>
			
			<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
		</div>
   </form>