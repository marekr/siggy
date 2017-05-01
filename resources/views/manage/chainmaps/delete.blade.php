@extends('layouts.manage',[
							'title' => 'siggy.manage: delete chainmap'
						])

@section('content')
<h1>Confirm deletion</h1>
<div class="content">
<form action="{{url('manage/chainmaps/remove/'.$chainmap->chainmap_id)}}" method="POST">
	<input type="hidden" value="1" name="confirm" />
	<p>Are you sure you want to remove the chain map '<?php echo $chainmap->chainmap_name; ?>'?</p>

	<button type="submit" class="btn">Confirm</button>
	<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
</form>
@endsection