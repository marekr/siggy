<?php

if( $mode == 'edit' )
{
	$formUrl = ('manage/chainmaps/edit/'.$id);
}
else
{
	$formUrl = ('manage/chainmaps/add');
}

?>
<h2><?php echo ($mode == 'edit' ?  ___('Edit Chain Map') : ___('Add Chain Map') ); ?></h2>
<form role="form" action="<?php echo URL::base(TRUE,TRUE); ?><?php echo $formUrl; ?>" method="POST">
	<legend>General</legend>
	<?php echo formRenderer::input('Name', 'chainmap_name', $chainmap->chainmap_name, '', $errors); ?>
	<?php echo formRenderer::textarea('Home system(s)', 'chainmap_homesystems', $chainmap->chainmap_homesystems, 'This setting allows siggy to do some advanced witchcraft relating to chain map management,signature deletion and possibly other things in the future. This is not required, and all eve systems are accepted as home systems and as many as you want/need. For more than one home system, use comma delimated format i.e. "Jita,Amarr,Dodixie" (without the quotes). ', $errors); ?>
	<?php echo formRenderer::yesNo('Do not purge home system sigs?', 'chainmap_skip_purge_home_sigs', $chainmap->chainmap_skip_purge_home_sigs, 'If yes, any signatures in your home system will not be deleted. The exception is wormholes which are deleted after 48 hours. If no, your home system sigs are treated like any other system.', $errors); ?>

	<div class="form-actions">
		<?php
		if( $mode == 'edit' ): ?>
			<button type="submit" class="btn btn-primary">Save changes</button>
		<?php
		else: ?>
			<button type="submit" class="btn btn-primary">Create chainmap</button>
		<?php endif; ?>
		<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
	</div>
</form>
