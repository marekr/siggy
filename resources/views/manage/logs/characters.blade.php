@extends('layouts.manage',[
							'title' => 'siggy.manage: sessions'
						])

@section('content')
<h3>Characters active in last 24 hours</h3>
<p>
	This page shows all access that has occurred in the last 24 hours based as characters. 
	The "Time" field is not live but rather the first time in the 24 hour Psy\VersionUpdater
	the character was recorded as accessing.
</p>

<table class="table table-striped">
	<thead>
		<tr>
			<th width="5%"></th>
			<th width="10%">Id</th>
			<th width="10%">Name</th>
			<th width="75%">Time</th>
		</tr>
	</thead>
	
	@if( count($characters) > 0 )
	@foreach($characters as $character)
	<tr>
		<td><img src="https://image.eveonline.com/Character/{{$character->id}}_32.jpg" width="32" height="32" /> </td>
		<td>{{$character->id}}</td>
		<td>{{$character->name}}</td>
		<td>{{$character->last_group_access_at}}</td>
	</tr>
	@endforeach
	@endif
</table>
@endsection