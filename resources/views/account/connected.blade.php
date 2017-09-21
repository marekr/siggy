@extends('layouts.public',['layoutMode' => 'leftMenu', 'title' => 'connected accounts', 'selectedTab'=>'account'])

@section('content')
	<h3>
		Connected Characters
	</h3>
	<p>
		These are characters you connected to siggy via EVE SSO.
		<ul>
			<li>If the status of a character is "invalid", you must relogin via SSO on that character to allow siggy to regain access.</li>
		</ul>
	</p>

	<a href="{{url('account/connect')}}" class="btn btn-primary pull-right">
		<i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Connect new character
	</a>
	<br clear='all' />
	<br />
	<table class="table table-striped">
		<tr>
			<th width="10%">ID</th>
			<th width="40%">Name</th>
			<th width="40%">Permissions</th>
			<th width="40%">Status</th>
			<th width="10%">Actions</th>
		</tr>
		@foreach( $characters as $character )
		@if(isset($character_data[$character->character_id]))
		<tr>
			<td>{{$character_data[$character->character_id]->id}}</td>
			<td>{{$character_data[$character->character_id]->name}}</td>
			<td>
				<table>
				@foreach($character->scopes() as $scope)
					<tr>
						<td>{{$scope['name']}}</td>
						<td>
							@if ($scope['active'])
								<i class="fa fa-check success" aria-hidden="true"></i>
							@else
								<i class="fa fa-times danger" aria-hidden="true"></i>
							@endif
						</td>
					</tr>
				@endforeach
				</table>
			</td>
			<td>{{ $character->valid ? 'Ok' : 'Invalid' }}</td>
			<td>
				{!! Form::open(['url' => 'account/disconnect']) !!}
					<input type="hidden" name="character_id" value="<?php echo $character_data[$character->character_id]->id; ?>" />
					<button class='btn btn-danger btn-xs' type="submit"><i class="fa fa-trash" aria-hidden="true"></i> Disconnect</button>
					<a href="{{url('account/connect')}}" class="btn btn-primary btn-xs"><i class="fa fa-refresh" aria-hidden="true"></i>&nbsp;Reconnect character</a>
				{!! Form::close() !!}
			</td>
		</tr>
		@endif
		@endforeach
	</table>
@endsection

@section('left_menu')
@include('account.menu')
@endsection