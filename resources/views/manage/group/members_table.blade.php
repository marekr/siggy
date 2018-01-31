
<h2 class="tableHeader">{{$table['chainmap']['name']}}</h2>
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
	@if( count($table['members']) > 0 )
		@foreach($table['members'] as $m )
		<tr>
			<td>{{ ucfirst($m->memberType) }}</td>
			<td>{{ $m->eveID }}</td>
			<td>
				@if( $m->memberType == 'corp' )
				<img src="https://image.eveonline.com/Corporation/{{ $m->eveID }}_32.png" width="32" height="32" />
				@else
				<img src="https://image.eveonline.com/Character/{{ $m->eveID }}_32.jpg" width="32" height="32" />
				@endif
				&nbsp;&nbsp;
				{{ $m->accessName }}
			</td>
			<td>
				<a href="{{url('manage/chainmaps/access/remove/'.$table['chainmap']['id'].'-'.$m->id)}}" class='btn btn-default btn-xs'><i class="fa fa-trash"></i>&nbsp;Remove</a>
			</td>
		</tr>
		@endforeach
	@else
		<tr>
			<td colspan="4">No members</td>
		</tr>
	@endif
	</tbody>
</table>
