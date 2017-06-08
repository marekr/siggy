@extends('layouts.manage',[
							'title' => 'siggy.manage: activity'
						])

@section('content')
<h3>Activity Logs</h3>

<div class="well">
	<form class="form-inline" action="{{url('manage/logs/activity')}}" method="get">
		<legend>Filter Options</legend>
		<label class="checkbox">Type

			<?php echo Form::select('filter_type', array( 'all' => 'All', 'delwhs' => 'WH Deletions', 'sigdel' => 'Sig Delete', 'editsig' => 'Sig Edit', 'editmap' => 'Map Edit' ), $filterType); ?>
		</label>
		<label class="checkbox">Message Contains

			<?php echo Form::input('search', (isset($_GET['search']) ? $_GET['search'] : '' ) ); ?>
		</label>
		<button type="submit" class="btn pull-right">Filter Results</button>
	</form>
</div>

<?php echo $pagination; ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th width="20%">Log Date</th>
			<th width="10%">Event Type</th>
			<th width="70%">Log Message</th>
		</tr>
	</thead>
	<tbody>
	<?php if( count($logs) > 0 ): ?>
	<?php foreach($logs as $log): ?>
	<tr>
		<td><?php echo date("d/m/y @ h:i:s",$log->entryTime); ?></td>
		<td><?php echo $log->type; ?></td>
		<td style="word-break: break-all; word-wrap: break-word;"><?php echo $log->message; ?></td>
	</tr>
	<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
<?php echo $pagination; ?>
@endsection