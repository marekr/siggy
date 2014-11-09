<h2>Manage Blacklist</h2>
<p>Use the blacklist to block specific characters from viewing your siggy. This is useful generally when you kick characters.</p>
<div class="pull-right">
    <?php echo Html::anchor('manage/blacklist/add', __('<i class="icon-plus-sign"></i>&nbsp;Add Character to Blacklist'), array('class' => 'btn btn-primary') ); ?>
</div>
<div class="clearfix"></div>
<br />
<table class='table table-striped'>
	<thead>
		<tr>
			<th width="10%">EVE ID</th>
			<th width="20%">Character</th>
			<th width="45%">Reason</th>
			<th width="15%">Created</th>
			<th width="10%">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($chars as $char): ?>
		<tr>
			<td><?php echo $char['character_id']; ?></td>
			<td>
				<img src="http://image.eveonline.com/Character/<?php echo $char['character_id']; ?>_32.jpg" width="32" height="32" />
				<?php echo $char['character_name']; ?>
			</td>
			<td><?php echo $char['reason']; ?></td>
			<td><?php echo miscUtils::getDateTimeString($char['created']); ?></td>
			<td>
				<?php echo Html::anchor('manage/blacklist/remove/'.$char['id'], __('<i class="icon-trash"></i>&nbsp;Remove'), array('class' => 'btn btn-xs btn-danger')); ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>

</table>