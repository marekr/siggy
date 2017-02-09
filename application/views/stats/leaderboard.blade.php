@extends('stats.wrapper')

@section('stats_content')
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
				<td class="center">
					<b><a href='javascript:CCPEVE.showInfo(1377, <?php echo $result->charID; ?>)'><?php echo $result->charName; ?></a></b><br />
					<img src='https://image.eveonline.com/Character/<?php echo $result->charID;?>_32.jpg' />
				</td>
				<td>{{$result->adds}}</td>
				<td>{{$result->updates}}</td>
				<td>{{$result->wormholes}}</td>
				<td>{{$result->pos_adds}}</td>
				<td>{{$result->pos_updates}}</td>
				<td>{{$result->score}}</td>
			</tr>
			<?php endforeach; ?>
		</table>
		<?php echo $pagination; ?>
	</div>
</div>

<div class="container">
	<div class="row">
		<h3>Point Legend</h3>
		<table class="table table-striped">
			<tr>
				<th>Item </th>
				<th>Points</th>
			</tr>
			<tr>
				<td>Sig Additions</td>
				<td><?php echo $sig_add; ?>x</td>
			</tr>
			<tr>
				<td>Sig Updates</td>
				<td><?php echo $sig_update; ?>x</td>
			</tr>
			<tr>
				<td>Wormholes</td>
				<td><?php echo $wormhole; ?>x</td>
			</tr>
			<tr>
				<td>POS Adds</td>
				<td><?php echo $pos_add; ?>x</td>
			</tr>
			<tr>
				<td>POS Edits</td>
				<td><?php echo $pos_update; ?>x</td>
			</tr>
		</table>
	</div>
</div>
@endsection