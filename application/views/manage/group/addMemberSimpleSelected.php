<?php
$select = array();
foreach($chainmaps as $c )
{
	$select[$c['chainmap_id']] = $c['chainmap_name'];
}

$type = array('corp' => 'Corp', 'char' => 'Character');
?>


<form class="form-horizontal" action="<?php echo URL::base(TRUE,TRUE).'manage/group/addMember/2'; ?>" method="post">
	<h3>Add Group Member</h3>
	<input type="hidden" name="act" value="doAdd" />
	<input type="hidden" name="eveID" value="<?php echo $eveID; ?>" />
	<input type="hidden" name="accessName" value="<?php echo $accessName; ?>" />
	<input type="hidden" name="memberType" value="<?php echo $memberType; ?>" />

	<?php if( $memberType == 'corp' ): ?>
	<p>
		<img src="http://image.eveonline.com/Corporation/<?php echo $eveID; ?>_64.png" width="64" height="64" />&nbsp;&nbsp;<strong><?php echo $accessName; ?></strong>
	</p>
	<?php else: ?>
	<p>
		<img src="http://image.eveonline.com/Character/<?php echo $eveID; ?>_64.jpg" width="64" height="64" />&nbsp;&nbsp;<strong><?php echo $accessName; ?></strong>
	</p>
	<?php endif; ?>

	<?php if( count($chainmaps) > 0 ): ?>
		<?php echo formRenderer::select('Chainmaps', 'chainmap_id', $select, 0, ''); ?>
	<?php endif; ?>
	<br />
	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Add member</button>
		<button type="button" class="btn" onclick="history.go(-2);return false;">Cancel</button>
	</div>
</form>
