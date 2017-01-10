<div class="container">
	<div class="row">
		<?php echo $pagination; ?>
		<table class="table table-striped">
			<tr>
				<th width="5%">Rank</th>
				<th width="80%">Name</th>
				<th width="15%">Total</th>
			</tr>
			<?php foreach($results as $result): ?>
			<tr>
				<td><?php echo (++$rank_offset); ?></td>
				<td class="center">
					<b><a href='javascript:CCPEVE.showInfo(1377, <?php echo $result->charID; ?>)'><?php echo $result->charName; ?></a></b><br />
					<img src='http://image.eveonline.com/Character/<?php echo $result->charID;?>_32.jpg' />
				</td>
				<td><?php echo $result->value; ?></td>
			</tr>
			<?php endforeach; ?>
		</table>
		<?php echo $pagination; ?>
	</div>
</div>