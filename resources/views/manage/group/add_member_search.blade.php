@extends('layouts.manage',[
							'title' => 'siggy.manage: add member'
						])

@section('content')

	@if(count($results) > 0 )
	<h3>Add New Member Search Results</h3>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th width="80%">Name</th>
				<th width="10%">Ingame ID</th>
				<th width="5%">Options</th>
			</tr>
		</thead>
		<tbody>
			@foreach( $results as $result )
			<tr>
				@if( $memberType == 'corp' )
				<td><img src="https://image.eveonline.com/Corporation/{{$result->id}}_64.png" width="64" height="64" /></td>
				<td>{{$result->name}}</td>
				<td>{{$result->id}}</td>
				<td>
					<form class="form-inline" action="{{url('manage/group/members/add/details')}}" method="post">
						<input type="hidden" name="eveID" value="{{$result->id}}" />
						<input type="hidden" name="accessName" value="{{$result->name}}" />
						<input type="hidden" name="memberType" value="{{$memberType}}" />
						<button type="submit" class="btn btn-primary">Select</button>
					</form>
				</td>
				@else
				<td><img src="https://image.eveonline.com/Character/{{$result->id}}_64.jpg" width="64" height="64" /></td>
				<td>{{$result->name}}</td>
				<td>{{$result->id}}</td>
				<td>
					<form class="form-inline" action="{{url('manage/group/members/add/details')}}" method="post">
						<input type="hidden" name="eveID" value="{{$result->id}}" />
						<input type="hidden" name="accessName" value="{{$result->name}}" />
						<input type="hidden" name="memberType" value="{{$memberType}}" />
						<button type="submit" class="btn btn-primary">Select</button>
					</form>
				</td>
				@endif
			</tr>
			@endforeach
		</tbody>
	</table>
	@endif

{!! Form::open(['url' => 'manage/group/members/add', 'class'=>'form-horizontal']) !!}
	@if(count($results) > 0 )
	<h3>Or search again</h3>
	@else
	<h3>Add New Member By Search</h3>
	@endif
	{!! Form::bsSelect('memberType', 'Member Type',  ['corp' => 'Corp', 'char' => 'Character'], '') !!}
	{!! Form::bsText('searchName', 'Name', 'Exact name from EVE ingame for the character or corp') !!}
	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Search</button>
		
		<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
	</div>
{!! Form::close() !!}
@endsection
