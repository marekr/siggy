@extends('layouts.manage',[
							'title' => 'siggy.manage: add blacklist character'
						])

@section('content')
{!! Form::open(['url' => 'manage/blacklist/add']) !!}
<h2>Add Character to Blacklist</h2>
{!! Form::bsText('character_name', 'Character Name', 'EVE character name. This must be the full and complete name.') !!}
{!! Form::bsText('reason', 'Reason', 'Reason why the character is blacklisted, it will be displayed to the character. This can be left empty') !!}

<div class="form-actions">
	<button type="submit" class="btn btn-primary">Save</button>
	<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
</div>
{!! Form::close() !!}
@endsection