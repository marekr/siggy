@extends('layouts.public',['layoutMode' => 'leftMenu', 'title' => 'siggy: connected accounts', 'selectedTab'=>'account'])

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

	<a href="{{url('account/connect')}}" clas="btn btn-primary pull-right"><i class="glyphicon glyphicon-plus"></i>&nbsp;Connect new character</a>
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
			<td><?php echo $character_data[$character->character_id]->id; ?></td>
			<td><?php echo $character_data[$character->character_id]->name; ?></td>
			<td>
				<table>
				@foreach($character->scopes() as $scope)
					<tr>
						<td><?php echo $scope['name']; ?></td>
						<td>
							<?php if ($scope['active']): ?>
								<span class="glyphicon glyphicon-ok success" aria-hidden="true"></span>
							<?php else: ?>
								<span class="glyphicon glyphicon-remove danger" aria-hidden="true"></span>
							<?php endif; ?>
						</td>
					</tr>
				@endforeach
				</table>
			</td>
			<td>{{ $character->valid ? 'Ok' : 'Invalid' }}</td>
			<td>
				{!! Form::open(['url' => 'account/disconnect']) !!}
					<input type="hidden" name="character_id" value="<?php echo $character_data[$character->character_id]->id; ?>" />
					<button class='btn btn-danger btn-xs' type="submit"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Disconnect</button>
					<a href="{{url('account/connect')}}" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-plus"></i>&nbsp;Reconnect character</a>
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