@extends('layouts.manage',[
							'title' => 'siggy.manage: apikeys'
						])

@section('content')
<h2>Manage Api Keys</h2>
<p>These api keys provide access to siggy's data outside the interface. THESE ARE NOT EVE API KEYS.</p>
<div class="pull-right">
	<a href="{{url('manage/apikeys/add')}}" class='btn btn-primary'><i class="fa fa-plus-circle fa-fw"></i>&nbsp;New Key</a>
</div>
<div class="clearfix"></div>
<br />
	@foreach($keys as $key)
	<div class="panel panel-default">
		<div class='panel-heading'>
			{{$key->name}}
			<div class='pull-right'>
				{!! Form::open(['url' => 'manage/apikeys/remove/'.$key->id,'style'=>'display:inline;']) !!}
					<button class='btn btn-xs btn-danger'><i class="fa fa-trash"></i>&nbsp;Remove</button>
				{!! Form::close() !!}
			</div>
		</div>
		<div class="panel-body">
			<h5>Credentials</h5>
			<div class="form-group">
				<label for="apiId{{$key->id}}">Id</label>
				<input type='text' readonly='readonly' value="{{$key->id}}" style='width:100%' class="form-control" id="apiId{{$key->id}}" />
			</div>
			<div class="form-group">
				<label for="apiSecret{{$key->id}}">Secret</label>
				<input type='text' readonly='readonly' value="{{$key->secret}}" style='width:100%' class="form-control" id="apiSecret{{$key->id}}" />
			</div>
			<h5>Scopes</h5>
			<table>
				@if($key->scopes != null)
					@foreach($key->scopes as $scope)
						<tr>
							<td>{{$scope}}</td>
						</tr>
					@endforeach
				@endif
			</table>
		</div>
	</div>
	@endforeach
@endsection