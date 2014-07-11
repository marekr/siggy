
<?php if( !$member_count && (Auth::$user->data['admin'] || $perms['can_manage_group_members'] || $perms['can_manage_access']) ): ?>
<h3>Complete your setup!</h3>
<div class="well">
	Looks like you didn't add any group members yet, if you want to be able to use siggy you must do so.
	
	<?php echo Html::anchor('manage/group/members', __('Manage Group Members'),array('class' => 'btn btn-primary btn-sm')); ?>
</div>
<?php endif; ?>

<h3>Updates and Announcements</h3>

<?php foreach($news as $n): ?>
<div class="well">
	<p class="pull-right"><small><?php echo date("d/m/y g:m", $n['datePublished']); ?></small></p>
	<h4><?php echo $n['title']; ?></h4>
	<p><?php echo $n['content']; ?></p>
</div>
<?php endforeach; ?>

<?php if( defined("SIGGY_VERSION") ): ?>
<p class="pull-right"><small>siggy version: <?php echo SIGGY_VERSION; ?></small></p>
<?php endif; ?>