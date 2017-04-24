
@extends('layouts.manage',[
							'title' => 'siggy.manage: Add Access'
						])

@section('content')

@if($mode == 'edit')
{!! Form::model($data, ['url' => 'manage/access/edit/'.$id]) !!}
@else
{!! Form::open(['url' => 'manage/access/add/']) !!}
@endif
<h2>
	@if($mode == 'edit')
	Editing Access for {{$data['username']}}
	@else
	Adding Access
	@endif
</h2>

@if($mode == 'add')
{!! Form::bsText('username', 'Username', 'Siggy account username to add. Must be valid and already exist.') !!}
@endif
{!! Form::yesNo('can_view_logs', 'Can view logs?') !!}
{!! Form::yesNo('can_manage_group_members', 'Can manage group members?') !!}
{!! Form::yesNo('can_manage_settings', 'Can manage settings?') !!}
{!! Form::yesNo('can_view_financial', 'Can view financial info?') !!}
{!! Form::yesNo('can_manage_access', 'Can view logs?') !!}
<div class="form-actions">
	@if($mode == 'edit')
	<button type="submit" class="btn btn-primary">Edit member</button>
	@else
	<button type="submit" class="btn btn-primary">Add member</button>
	@endif
	
	<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
</div>
{!! Form::close() !!}

@endsection