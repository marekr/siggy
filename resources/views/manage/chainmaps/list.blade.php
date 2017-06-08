@extends('layouts.manage',[
							'title' => 'siggy.manage: chainmap list'
						])

@section('content')
<h1>Chainmaps List</h1>
	<div class="info">
	This page lists all the chainmaps that are setup. There is a "default" chain map which cannot ever be deleted but can have its settings modified. All other chainmaps can be freely modified and removed.
	</div>


	<a href="{{url('manage/chainmaps/add')}}" class='btn btn-primary pull-right'><i class="fa fa-plus-circle fa-fw"></i>&nbsp;Add New Chainmap</a>
	<br />
	<br />
	<br />

@if( count( $chainmaps ) > 0 )
<table class="table table-striped" width="100%">
	<thead>
		<tr>
			<th width="60%">Chainmap</th>
			<th width="15%"># Members</th>
			<th width="25%">Options</th>
		</tr>
	</thead>
	<tbody>
		@foreach( $chainmaps as $s )
		<tr>
			<td><?php echo $s->chainmap_name ?></td>
			<td>---</td>
			<td>
				<a href="{{url('manage/chainmaps/edit/'.$s->chainmap_id)}}" class='btn btn-default btn-xs'><i class="icon-edit"></i>&nbsp;Edit</a>
				@if( $s->chainmap_type != 'default' )
				<a href="{{url('manage/chainmaps/remove/'.$s->chainmap_id)}}" class='btn btn-default btn-xs'><i class="icon-trash"></i>&nbsp;Remove</a>
				@endif
			</td>
		</tr>
		@endforeach
	</tbody>
</table>
@else
<p>No chainmaps currently exist.</p>
@endif

@endsection