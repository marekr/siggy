@extends('layouts.manage',[
							'title' => 'siggy.manage: Add/Edit Chainmap'
						])

@section('content')

@if($mode == 'edit')
{!! Form::model($chainmap, ['url' => 'manage/chainmaps/edit/'.$chainmap->chainmap_id]) !!}
@else
{!! Form::open(['url' => 'manage/chainmaps/add']) !!}
@endif

<h2>
	@if($mode == 'edit')
	Edit Chain Map
	@else
	Add Chain Map
	@endif
</h2>

{!! Form::bsText('chainmap_name', 'Name') !!}
{!! Form::bsTextarea('chainmap_homesystems', 'Home system(s)','This setting allows siggy to do some advanced witchcraft relating to chain map management,signature deletion and possibly other things in the future. This is not required, and all eve systems are accepted as home systems and as many as you want/need. For more than one home system, use comma delimated format i.e. "Jita,Amarr,Dodixie" (without the quotes).') !!}
{!! Form::yesNo('chainmap_skip_purge_home_sigs', 'Do not purge home system sigs?', 'If yes, any signatures in your home system will not be deleted. The exception is wormholes which are deleted after 48 hours. If no, your home system sigs are treated like any other system.') !!}

<div class="form-actions">
	<button type="submit" class="btn btn-primary">Save</button>
	<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
</div>

{!! Form::close() !!}
@endsection