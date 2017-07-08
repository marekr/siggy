@extends('layouts.manage',[
							'title' => 'siggy.manage: activity'
						])

@section('content')
<h3>Activity Logs</h3>

<div class="well">
	{!! Form::open(['url' => 'manage/logs/activity','class'=>'form-inline','method' => 'get']) !!}
		<legend>Filter Options</legend>
		<label class="checkbox">Type
			{!! Form::select('filter_type', array( 'all' => 'All', 'delwhs' => 'WH Deletions', 'sigdel' => 'Sig Delete', 'editsig' => 'Sig Edit', 'editmap' => 'Map Edit' ), $filterType) !!}
		</label>
		<label class="checkbox">Message Contains
			{!! Form::input('search', (isset($_GET['search']) ? $_GET['search'] : '' ) ) !!}
		</label>
		<button type="submit" class="btn pull-right">Filter Results</button>
	{!! Form::close() !!}
</div>

{!! $pagination !!}
<table class="table table-striped">
	<thead>
		<tr>
			<th width="20%">Log Date</th>
			<th width="10%">Event Type</th>
			<th width="70%">Log Message</th>
		</tr>
	</thead>
	<tbody>
	@if( count($logs) > 0 )
		@foreach($logs as $log)
		<tr>
			<td>{{ date("d/m/y @ h:i:s",$log->entryTime) }}</td>
			<td>{{ $log->type }}</td>
			<td style="word-break: break-all; word-wrap: break-word;">{{ $log->message }}</td>
		</tr>
		@endforeach
	@endif
	</tbody>
</table>
{!! $pagination !!}
@endsection