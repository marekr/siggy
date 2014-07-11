<h2>Configure Access</h2>
<div class="pull-right">
    <?php echo Html::anchor('manage/access/add', __('<i class="icon-plus-sign"></i>&nbsp;Add Access'), array('class' => 'btn btn-primary') ); ?>
</div>
<div class="clearfix"></div>
<br />
<table class='table table-striped'>
	<thead>
		<tr>
			<th>Username</th>
			<th width="12%">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($users as $user): ?>
		<tr>
			<td><?php echo $user['username']; ?></td>
			<td><?php echo Html::anchor('manage/access/edit/'.$user['user_id'], __('<i class="icon-pencil"></i>&nbsp;Edit')); ?>
			<?php echo Html::anchor('manage/access/remove/'.$user['user_id'], __('<i class="icon-trash"></i>&nbsp;Remove')); ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>

</table>