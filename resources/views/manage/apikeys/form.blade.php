
@extends('layouts.manage',[
							'title' => 'siggy.manage: Add Api Key'
						])

@section('content')

{!! Form::open(['url' => 'manage/apikeys/add/']) !!}
<h2>
	@if($mode == 'edit')
	Editing Access for {{$data['username']}}
	@else
	Adding Access
	@endif
</h2>

{!! Form::bsText('name', 'Name', 'Descriptive name for the key') !!}

<fieldset>
<legend>Chainmaps</legend>
{!! Form::yesNo('scopes[chainmaps_read]', 'Read') !!}
</fieldset>

<fieldset>
<legend>Systems</legend>
{!! Form::yesNo('scopes[systems_read]', 'Read') !!}
</fieldset>

<fieldset>
<legend>Groups</legend>
{!! Form::yesNo('scopes[group_read]', 'Read') !!}
</fieldset>

<div class="form-actions">
	<button type="submit" class="btn btn-primary">Create key</button>
	
	<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
</div>
{!! Form::close() !!}

@endsection