<table class="table table-striped" width="100%">
	<thead>
		<tr>
			<th width="5%">Type</th>
			<th width="10%">EVE ID</th>
			<th width="60%">Access Name</th>
			<th width="25%">Options</th>
		</tr>
	</thead>
	<tbody>
	<?php if( count($members) > 0 ): ?>
		<?php foreach( $members as $m ): ?>
		<tr>
			<td><?php echo ucfirst($m['memberType']) ?></td>
			<td><?php echo $m['eveID'] ?></td>
			<td>
				<?php if( $m['memberType'] == 'corp' ): ?>
				<img src="http://image.eveonline.com/Corporation/<?php echo $m['eveID']; ?>_32.png" width="32" height="32" />
				<?php else: ?>
				<img src="http://image.eveonline.com/Character/<?php echo $m['eveID']; ?>_32.jpg" width="32" height="32" />
				<?php endif; ?>
				&nbsp;&nbsp;
				<?php echo $m['accessName'] ?>
			</td>
			<td><?php echo Html::anchor('manage/group/editMember/'.$m['id'], __('<i class="icon-edit"></i>&nbsp;Edit')); ?> <?php echo Html::anchor('manage/group/removeMember/'.$m['id'], __('<i class="icon-trash"></i>&nbsp;Remove')); ?></td>
		</tr>
		<?php endforeach ?>
	<?php else: ?>
		<tr>
			<td colspan="4">No members</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>