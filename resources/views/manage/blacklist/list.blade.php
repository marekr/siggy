@extends('layouts.manage',[
							'title' => 'siggy.manage: blacklist'
						])

@section('content')
<h2>Manage Blacklist</h2>
<p>Use the blacklist to block specific characters from viewing your siggy. This is useful generally when you kick characters.</p>
<div class="pull-right">
	<a href="{{url('manage/blacklist/add')}}" class='btn btn-primary'><i class="fa fa-plus-circle fa-fw"></i>&nbsp;Add Character to Blacklist</a>
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
		@foreach($chars as $char)
		<tr>
			<td>{{ $char->character_id }}</td>
			<td>
				<img src="https://image.eveonline.com/Character/{{$char->character_id}}_32.jpg" width="32" height="32" />
				{{ $char->character()->name }}
			</td>
			<td>{{ $char->reason }}</td>
			<td>{{ $char->created_at }}</td>
			<td>
				<a href="{{url('manage/blacklist/remove/'.$char->id)}}" class='btn btn-xs btn-danger'><i class="fa fa-trash"></i>&nbsp;Remove</a>
			</td>
		</tr>
		@endforeach
	</tbody>

</table>
@endsection