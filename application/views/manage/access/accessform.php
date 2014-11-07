<?php

if( $mode == 'edit' )
{
	$formUrl = URL::base(TRUE,TRUE).'manage/access/edit/'.$id;
}
else
{
	$formUrl =  URL::base(TRUE,TRUE).'manage/access/add';
}


?>
   <form role="form" action="<?php echo $formUrl; ?>" method="POST">
   <h2><?php echo ($mode == 'edit' ?  __('Editing Access') : __('Adding Access') ); ?> for <?php echo $data['username']; ?></h2>
        <?php if( $mode == 'add' ): ?>
        
        <?php echo formRenderer::input('Username', 'username', $data['username'], 'Siggy account username to add. Must be valid and already exist.', $errors); ?>
        <?php endif; ?>
   
		<?php echo formRenderer::checkbox('Can view logs?', 'can_view_logs', $data['can_view_logs'], '', $errors); ?>
		<?php echo formRenderer::checkbox('Can manage group members?', 'can_manage_group_members', $data['can_manage_group_members'], '', $errors); ?>
		<?php echo formRenderer::checkbox('Can manage settings?', 'can_manage_settings', $data['can_manage_settings'], '', $errors); ?>
		<?php echo formRenderer::checkbox('Can view financial info?', 'can_view_financial', $data['can_view_financial'], '', $errors); ?>
		<?php echo formRenderer::checkbox('Can manage admin access?', 'can_manage_access', $data['can_manage_access'], '', $errors); ?>

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