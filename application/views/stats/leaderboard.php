<div class="container">
	<div class="row">
		<?php echo $pagination; ?>
		<table class="table table-striped">
			<tr>
				<th width="5%">Rank</th>
				<th width="35%">Name</th>
				<th width="10%">Sig Adds</th>
				<th width="10%">Sig Updates</th>
				<th width="10%">Wormholes Mapped</th>
				<th width="10%">POS Adds</th>
				<th width="10%">POS Edits</th>
				<th width="10%">Score</th>
			</tr>
			<?php foreach($results as $result): ?>
			<tr>
				<td><?php echo (++$rank_offset); ?></td>
				<td class="centered"><img src='http://image.eveonline.com/Character/<?php echo $result['charID'];?>_32.jpg' /><?php echo $result['charName']; ?></td>
				<td><?php echo $result['adds']; ?></td>
				<td><?php echo $result['updates']; ?></td>
				<td><?php echo $result['wormholes']; ?></td>
				<td><?php echo $result['pos_adds']; ?></td>
				<td><?php echo $result['pos_edits']; ?></td>
				<td><?php echo $result['score']; ?></td>
			</tr>
			<?php endforeach; ?>
		</table>
		<?php echo $pagination; ?>
	</div>
</div>