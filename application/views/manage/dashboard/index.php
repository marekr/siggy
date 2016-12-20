
<?php if( !$member_count && (Auth::$user->data['admin'] || $perms['can_manage_group_members'] || $perms['can_manage_access']) ): ?>
<h3>Complete your setup!</h3>
<div class="well">
	<p>
	Looks like you didn't add any group members yet, if you want to be able to use siggy you must do so.
	</p>
	<?php echo Html::anchor('manage/group/members', __('Manage Group Members'),array('class' => 'btn btn-primary btn-xs')); ?>
</div>
<?php endif; ?>


<?php if( !$group->password_required ): ?>
<h3>Security warning!</h3>
<div class="alert alert-warning">
	<p>
	You don't have a group password set! It is highly encouraged you add one for your own security. People <i>may</i> use stolen API keys or spoof browser headers to otherwise pretend a legitimate user. 
	This is optional and you may ignore this warning.
	</p>
	<br />
	<?php echo Html::anchor('manage/settings/general', __('Edit Settings'),array('class' => 'btn btn-primary btn-xs')); ?>
</div>
<?php endif; ?>

<h3>Updates and Announcements</h3>

<?php foreach($news as $n): ?>
<div class="well">
	<p class="pull-right"><small> <?php echo date("Y-m-d g:m", $n['datePublished']); ?></small></p>
	<h4><?php echo $n['title']; ?></h4>
	<p><?php echo $n['content']; ?></p>
</div>
<?php endforeach; ?>

<?php if( defined("SIGGY_VERSION") ): ?>
<p class="pull-right"><small>siggy version: <?php echo SIGGY_VERSION; ?></small></p>
<?php endif; ?>