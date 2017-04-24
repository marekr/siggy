@extends('layouts.legacy',[
							'title' => 'siggy: dscan',
							'selectedTab' => 'dscan'
						])

@section('content')
<style type="text/css">
	.dscan-col
	{
	}
	.dscan-group h4
	{
		font-size:12px;
		background-color: #000;
		padding: 5px;
		margin-bottom:0;
	}
	.dscan-group table
	{
		width:100%;
		background-color:#202020;
	}
</style>

<h2>Directional Scan Result</h2>
<p>
	<a style="float:right" class="btn" href="<?php echo URL::base(); ?>">&lt; Back to scanning</a>
	<strong>Title:</strong><?php echo $dscan->dscan_title; ?><br />
	<strong>System:</strong> <?php echo $dscan->system_name; ?> <br />
	<strong>Date:</strong> <?php echo date("Y-m-d H:i:s", $dscan->dscan_date); ?><br />
	<strong>Submitter:</strong> <?php echo $dscan->dscan_added_by; ?>
</p>
<div class="dscan-col span6">
	<h3>All</h3>
	<?php foreach($all as $group): ?>
	<div class="dscan-group">
		<h4>
		<?php echo $group['record_count']; ?>x - 
		<?php echo $group['group_name']; ?>
		</h4>
		<table style="display:none">
			<?php foreach($group['records'] as $rec): ?>
				<tr>
					<td width="50%"><?php echo $rec['record_name']; ?></td>
					<td width="50%"><?php echo $rec['type_name']; ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</div>
	<?php endforeach; ?>
</div>

<div class="dscan-col span6">
	<h3>On Grid</h3>
	<?php foreach($ongrid as $group): ?>
	<div class="dscan-group">
		<h4>
		<?php echo $group['record_count']; ?> - 
		<?php echo $group['group_name']; ?>
		</h4>
		<table style="">
			<?php foreach($group['records'] as $rec): ?>
				<tr>
					<td width="42%"><?php echo $rec['record_name']; ?></td>
					<td width="42%"><?php echo $rec['type_name']; ?></td>
					<td width="15%"><?php echo $rec['distance']; ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</div>
	<?php endforeach; ?>
</div>

<script type="text/javascript">
	$(document).ready( function() {
		$("div.dscan-col h4").click( function() {
			$("table",$(this).parent()).toggle();
		});
	} );
</script>
@endsection